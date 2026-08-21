<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\ConsulterLesParametres;
use App\Application\ConsulterUnCreneau;
use App\Application\MettreEnAlerte;
use App\Application\SaisirLaPrevisionMeteo;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlerteController extends AbstractController
{
    public function __construct(
        private readonly ConsulterLesParametres $consulterLesParametres,
        private readonly ConsulterUnCreneau $consulterUnCreneau,
        private readonly SaisirLaPrevisionMeteo $saisirLaPrevisionMeteo,
        private readonly MettreEnAlerte $mettreEnAlerte,
    ) {
    }

    #[Route(
        '/{_locale}/gestion/alerte',
        name: 'gestion_alerte',
        requirements: ['_locale' => 'fr|en'],
        methods: ['GET', 'POST'],
    )]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();

        if ($request->isMethod('POST')) {
            $jour = (string) $request->request->get('jour', '');
            $heure = (string) $request->request->get('heure', '');
            $prevision = trim((string) $request->request->get('prevision', ''));

            if ($prevision !== '') {
                $this->saisirLaPrevisionMeteo->executer($jour, $prevision);
            }

            $this->mettreEnAlerte->executer($jour, $heure);

            return $this->redirectToRoute('gestion_creneau', ['_locale' => $locale, 'jour' => $jour, 'heure' => $heure]);
        }

        $jour = $request->query->get('jour');
        $heure = $request->query->get('heure');
        $creneau = null;

        if (is_string($jour) && is_string($heure)) {
            try {
                $creneau = $this->consulterUnCreneau->executer($jour, $heure);
            } catch (InvalidArgumentException) {
                $jour = null;
                $heure = null;
            }
        }

        return $this->render('gestion/alerte.html.twig', [
            'jour' => $jour,
            'heure' => $heure,
            'creneau' => $creneau,
            'parametres' => $this->consulterLesParametres->executer(),
        ]);
    }
}
