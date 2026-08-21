<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\AjouterUnJourDeFermeture;
use App\Application\ConsulterLaFlotte;
use App\Application\ConsulterLaGrilleTarifaire;
use App\Application\ConsulterLesCodesEnCirculation;
use App\Application\ConsulterLesJoursDeFermeture;
use App\Application\ConsulterLesParametres;
use App\Application\CreerUnBateau;
use App\Application\DefinirLeForfaitDePrivatisation;
use App\Application\ModifierUnTarif;
use App\Application\RetirerUnJourDeFermeture;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReglagesController extends AbstractController
{
    public function __construct(
        private readonly ConsulterLaGrilleTarifaire $consulterLaGrilleTarifaire,
        private readonly ConsulterLaFlotte $consulterLaFlotte,
        private readonly ConsulterLesJoursDeFermeture $consulterLesJoursDeFermeture,
        private readonly ConsulterLesCodesEnCirculation $consulterLesCodesEnCirculation,
        private readonly ConsulterLesParametres $consulterLesParametres,
        private readonly ModifierUnTarif $modifierUnTarif,
        private readonly CreerUnBateau $creerUnBateau,
        private readonly DefinirLeForfaitDePrivatisation $definirLeForfaitDePrivatisation,
        private readonly AjouterUnJourDeFermeture $ajouterUnJourDeFermeture,
        private readonly RetirerUnJourDeFermeture $retirerUnJourDeFermeture,
    ) {
    }

    #[Route('/{_locale}/gestion/reglages', name: 'gestion_reglages', requirements: ['_locale' => 'fr|en'])]
    public function index(): Response
    {
        return $this->render('gestion/reglages.html.twig', [
            'grille' => $this->consulterLaGrilleTarifaire->executer(),
            'flotte' => $this->consulterLaFlotte->executer(),
            'joursDeFermeture' => $this->consulterLesJoursDeFermeture->executer(),
            'codes' => $this->consulterLesCodesEnCirculation->executer(),
            'parametres' => $this->consulterLesParametres->executer(),
        ]);
    }

    #[Route('/{_locale}/gestion/reglages/tarifs', name: 'gestion_reglages_tarifs', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    public function tarifs(Request $request): Response
    {
        foreach (['BALEINES', 'DAUPHINS'] as $type) {
            $adulte = $request->request->get('adulte_'.$type);
            $enfant = $request->request->get('enfant_'.$type);

            if ($adulte === null || $enfant === null) {
                continue;
            }

            try {
                $this->modifierUnTarif->executer($type, $this->centimes((string) $adulte), $this->centimes((string) $enfant));
            } catch (InvalidArgumentException) {
                $this->addFlash('erreur', 'erreur.montant_invalide');
            }
        }

        return $this->redirectToRoute('gestion_reglages', ['_locale' => $request->getLocale()]);
    }

    #[Route('/{_locale}/gestion/reglages/bateau', name: 'gestion_reglages_bateau', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    public function bateau(Request $request): Response
    {
        $nom = trim((string) $request->request->get('nom', ''));
        $capacite = (int) $request->request->get('capacite', 0);

        try {
            $this->creerUnBateau->executer($nom, $capacite);
        } catch (InvalidArgumentException) {
            $this->addFlash('erreur', 'erreur.montant_invalide');
        }

        return $this->redirectToRoute('gestion_reglages', ['_locale' => $request->getLocale()]);
    }

    #[Route('/{_locale}/gestion/reglages/forfait', name: 'gestion_reglages_forfait', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    public function forfait(Request $request): Response
    {
        $bateau = (string) $request->request->get('bateau', '');
        $forfait = $this->centimes((string) $request->request->get('forfait', '0'));

        try {
            $this->definirLeForfaitDePrivatisation->executer($bateau, $forfait);
        } catch (InvalidArgumentException) {
            $this->addFlash('erreur', 'erreur.montant_invalide');
        }

        return $this->redirectToRoute('gestion_reglages', ['_locale' => $request->getLocale()]);
    }

    #[Route('/{_locale}/gestion/reglages/fermeture/ajouter', name: 'gestion_reglages_fermeture_ajouter', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    public function fermetureAjouter(Request $request): Response
    {
        $jour = (string) $request->request->get('jour', '');

        if ($jour !== '') {
            $resultat = $this->ajouterUnJourDeFermeture->executer($jour, (bool) $request->request->get('recurrent'));

            if ($resultat->reservationsConcernees() !== []) {
                $this->addFlash('info', 'gestion.reglages.jours_note');
            }
        }

        return $this->redirectToRoute('gestion_reglages', ['_locale' => $request->getLocale()]);
    }

    #[Route('/{_locale}/gestion/reglages/fermeture/retirer', name: 'gestion_reglages_fermeture_retirer', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    public function fermetureRetirer(Request $request): Response
    {
        $jour = (string) $request->request->get('jour', '');

        if ($jour !== '') {
            $this->retirerUnJourDeFermeture->executer($jour);
        }

        return $this->redirectToRoute('gestion_reglages', ['_locale' => $request->getLocale()]);
    }

    private function centimes(string $saisie): int
    {
        $normalise = str_replace(',', '.', trim($saisie));

        return is_numeric($normalise) ? (int) round(((float) $normalise) * 100) : 0;
    }
}
