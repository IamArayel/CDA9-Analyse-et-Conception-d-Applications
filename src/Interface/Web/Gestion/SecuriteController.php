<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecuriteController extends AbstractController
{
    #[Route(
        '/{_locale}/gestion/connexion',
        name: 'gestion_connexion',
        requirements: ['_locale' => 'fr|en'],
        methods: ['GET', 'POST'],
    )]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('gestion/connexion.html.twig', [
            'erreur' => $authenticationUtils->getLastAuthenticationError(),
            'dernierIdentifiant' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route(
        '/{_locale}/gestion/deconnexion',
        name: 'gestion_deconnexion',
        requirements: ['_locale' => 'fr|en'],
        methods: ['POST'],
    )]
    public function deconnexion(): never
    {
        throw new LogicException('Interceptée par le pare-feu avant d\'atteindre le contrôleur.');
    }
}
