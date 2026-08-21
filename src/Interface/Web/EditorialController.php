<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\ConsulterLaFlotte;
use App\Application\ConsulterLaFriseDeSaison;
use App\Application\ConsulterLaGrilleTarifaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Les pages éditoriales du site public : sans logique métier propre, elles ne
 * font que mettre en forme ce que les services applicatifs rendent déjà.
 */
final class EditorialController extends AbstractController
{
    public function __construct(
        private readonly ConsulterLaFriseDeSaison $consulterLaFriseDeSaison,
        private readonly ConsulterLaFlotte $consulterLaFlotte,
        private readonly ConsulterLaGrilleTarifaire $consulterLaGrilleTarifaire,
    ) {
    }

    #[Route('/{_locale}/sorties', name: 'sorties', requirements: ['_locale' => 'fr|en'])]
    public function sorties(): Response
    {
        return $this->render('public/sorties.html.twig', [
            'frise' => $this->consulterLaFriseDeSaison->executer(),
        ]);
    }

    #[Route('/{_locale}/bateaux', name: 'flotte', requirements: ['_locale' => 'fr|en'])]
    public function flotte(): Response
    {
        return $this->render('public/flotte.html.twig', [
            'flotte' => $this->consulterLaFlotte->executer(),
        ]);
    }

    #[Route('/{_locale}/tarifs', name: 'tarifs', requirements: ['_locale' => 'fr|en'])]
    public function tarifs(): Response
    {
        return $this->render('public/tarifs.html.twig', [
            'grille' => $this->consulterLaGrilleTarifaire->executer(),
            'flotte' => $this->consulterLaFlotte->executer(),
        ]);
    }
}
