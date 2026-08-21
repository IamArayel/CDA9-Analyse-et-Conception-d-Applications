<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\AnnulerCreneau;
use App\Application\ConsulterUneReservation;
use App\Application\ConsulterUnCreneau;
use App\Application\EnregistrerUneIssueDannulation;
use App\Domaine\IssueDannulation;
use App\Domaine\ResultatDannulation;
use App\Domaine\ResultatDissue;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use ValueError;

final class AnnulationController extends AbstractController
{
    public function __construct(
        private readonly ConsulterUnCreneau $consulterUnCreneau,
        private readonly AnnulerCreneau $annulerCreneau,
        private readonly ConsulterUneReservation $consulterUneReservation,
        private readonly EnregistrerUneIssueDannulation $enregistrerUneIssueDannulation,
    ) {
    }

    #[Route(
        '/{_locale}/gestion/annulations',
        name: 'gestion_annulation_bareme',
        requirements: ['_locale' => 'fr|en'],
        methods: ['GET', 'POST'],
    )]
    public function bareme(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $reference = trim((string) $request->request->get('reference', ''));

            return $this->redirectToRoute('gestion_annulation_issue', [
                '_locale' => $request->getLocale(),
                'reference' => $reference,
            ]);
        }

        return $this->render('gestion/annulation_bareme.html.twig');
    }

    #[Route(
        '/{_locale}/gestion/creneau/{jour}/{heure}/annuler',
        name: 'gestion_annulation_creneau',
        requirements: ['_locale' => 'fr|en', 'jour' => '\d{4}-\d{2}-\d{2}', 'heure' => '\d{2}:\d{2}'],
        methods: ['GET', 'POST'],
    )]
    public function creneau(Request $request, string $jour, string $heure): Response
    {
        $locale = $request->getLocale();

        if ($request->isMethod('POST')) {
            $resultat = $this->annulerCreneau->executer($jour, $heure);

            if ($resultat->estRefusee()) {
                $this->addFlash('erreur', $this->cleDuRefus($resultat));

                return $this->redirectToRoute('gestion_creneau', ['_locale' => $locale, 'jour' => $jour, 'heure' => $heure]);
            }

            return $this->redirectToRoute('gestion_journee', ['_locale' => $locale]);
        }

        return $this->render('gestion/annulation_creneau.html.twig', [
            'jour' => $jour,
            'heure' => $heure,
            'creneau' => $this->consulterUnCreneau->executer($jour, $heure),
        ]);
    }

    #[Route(
        '/{_locale}/gestion/reservation/{reference}/issue',
        name: 'gestion_annulation_issue',
        requirements: ['_locale' => 'fr|en', 'reference' => '\d+'],
        methods: ['GET', 'POST'],
    )]
    public function issue(Request $request, string $reference): Response
    {
        $locale = $request->getLocale();

        try {
            $reservation = $this->consulterUneReservation->executer($reference);
        } catch (InvalidArgumentException) {
            throw $this->createNotFoundException();
        }

        if ($request->isMethod('POST')) {
            $issueChoisie = (string) $request->request->get('issue', '');
            $montantSaisi = trim((string) $request->request->get('montant', ''));
            $montant = $montantSaisi === '' ? null : (int) round(((float) str_replace(',', '.', $montantSaisi)) * 100);

            try {
                $issue = IssueDannulation::from($issueChoisie);
            } catch (ValueError) {
                return $this->redirectToRoute('gestion_annulation_issue', ['_locale' => $locale, 'reference' => $reference]);
            }

            $resultat = $this->enregistrerUneIssueDannulation->executer($reference, $issue, $montant);

            if ($resultat->estRefusee()) {
                $this->addFlash('erreur', $this->cleDuRefusDissue($resultat));
            }

            return $this->redirectToRoute('gestion_annulation_bareme', ['_locale' => $locale]);
        }

        return $this->render('gestion/annulation_issue.html.twig', [
            'reference' => $reference,
            'reservation' => $reservation,
        ]);
    }

    private function cleDuRefus(ResultatDannulation $resultat): string
    {
        return match ($resultat->motifDuRefus()) {
            ResultatDannulation::MOTIF_CRENEAU_DEJA_PASSE => 'erreur.creneau_deja_passe',
            default => 'erreur.creneau_ferme',
        };
    }

    private function cleDuRefusDissue(ResultatDissue $resultat): string
    {
        return match ($resultat->motifDuRefus()) {
            ResultatDissue::MOTIF_ISSUE_DEJA_ENREGISTREE => 'erreur.issue_deja_enregistree',
            default => 'erreur.creneau_ferme',
        };
    }
}
