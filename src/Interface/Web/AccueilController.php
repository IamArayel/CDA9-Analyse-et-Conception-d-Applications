<?php

declare(strict_types=1);

namespace App\Interface\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'racine')]
    public function racine(): RedirectResponse
    {
        return $this->redirectToRoute('accueil', ['_locale' => 'fr']);
    }

    #[Route('/{_locale}/', name: 'accueil', requirements: ['_locale' => 'fr|en'])]
    public function index(): Response
    {
        return $this->render('public/accueil.html.twig');
    }
}
