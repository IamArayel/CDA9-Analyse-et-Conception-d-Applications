<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\ConsulterUnCreneau;
use App\Domaine\Politique\SeuilDeMaintien;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreneauController extends AbstractController
{
    public function __construct(private readonly ConsulterUnCreneau $consulterUnCreneau)
    {
    }

    #[Route(
        '/{_locale}/gestion/creneau/{jour}/{heure}',
        name: 'gestion_creneau',
        requirements: ['_locale' => 'fr|en', 'jour' => '\d{4}-\d{2}-\d{2}', 'heure' => '\d{2}:\d{2}'],
    )]
    public function detail(string $jour, string $heure): Response
    {
        return $this->render('gestion/creneau.html.twig', [
            'jour' => $jour,
            'heure' => $heure,
            'creneau' => $this->consulterUnCreneau->executer($jour, $heure),
            'seuil' => SeuilDeMaintien::SEUIL,
        ]);
    }
}
