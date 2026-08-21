<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\ConfirmerLePaiement;
use App\Domaine\ResultatDePaiement;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaiementController extends AbstractController
{
    public function __construct(private readonly ConfirmerLePaiement $confirmerLePaiement)
    {
    }

    #[Route(
        '/{_locale}/reserver/{reference}/payer',
        name: 'paiement_demarrer',
        requirements: ['_locale' => 'fr|en', 'reference' => '\d+'],
        methods: ['POST'],
    )]
    public function demarrer(Request $request, string $reference): Response
    {
        $locale = $request->getLocale();

        try {
            $resultat = $this->confirmerLePaiement->executer($reference);
        } catch (InvalidArgumentException) {
            throw $this->createNotFoundException();
        }

        if ($resultat->estConfirme()) {
            return $this->redirectToRoute('reservation_confirmation', [
                '_locale' => $locale,
                'reference' => $reference,
            ]);
        }

        if ($resultat->motifDuRefus() === ResultatDePaiement::MOTIF_TRANSACTION_REFUSEE) {
            $this->addFlash('erreur', 'erreur.transaction_refusee');

            $sortie = $request->getSession()->get(ReservationController::CLE_SESSION_SORTIE);

            return $this->redirectToRoute('reservation_formulaire', ['_locale' => $locale, 'sortie' => $sortie]);
        }

        $request->getSession()->remove(ReservationController::CLE_SESSION_REFERENCE);
        $request->getSession()->remove(ReservationController::CLE_SESSION_SORTIE);
        $this->addFlash(
            'erreur',
            $resultat->motifDuRefus() === ResultatDePaiement::MOTIF_CRENEAU_ANNULE
                ? 'erreur.creneau_annule'
                : 'erreur.place_prise',
        );

        return $this->redirectToRoute('calendrier', ['_locale' => $locale]);
    }
}
