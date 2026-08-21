<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\ConsulterLaJournee;
use App\Domaine\Horloge;
use App\Domaine\Politique\SeuilDeMaintien;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class JourneeController extends AbstractController
{
    public function __construct(
        private readonly ConsulterLaJournee $consulterLaJournee,
        private readonly Horloge $horloge,
    ) {
    }

    #[Route('/{_locale}/gestion', name: 'gestion_journee', requirements: ['_locale' => 'fr|en'])]
    public function index(Request $request): Response
    {
        $jour = (string) $request->query->get('jour', $this->horloge->maintenant()->format('Y-m-d'));

        return $this->render('gestion/journee.html.twig', [
            'jour' => $jour,
            'tableau' => $this->consulterLaJournee->executer($jour),
            'seuil' => SeuilDeMaintien::SEUIL,
        ]);
    }

    /**
     * Geste sans contrepartie applicative aujourd'hui : rien dans le domaine
     * ne peut « geler » une sortie contre le contrôle automatique des 24
     * heures (`Tache\ControlerSeuilDeMaintien`), qui annulera seul si le
     * seuil n'est toujours pas atteint à son passage, quel que soit ce clic.
     * Écart assumé, annoncé plutôt que masqué par un message qui mentirait.
     */
    #[Route(
        '/{_locale}/gestion/creneau/{jour}/{heure}/maintenir',
        name: 'gestion_journee_maintenir',
        requirements: ['_locale' => 'fr|en', 'jour' => '\d{4}-\d{2}-\d{2}', 'heure' => '\d{2}:\d{2}'],
        methods: ['POST'],
    )]
    public function maintenir(Request $request): Response
    {
        return $this->redirectToRoute('gestion_journee', ['_locale' => $request->getLocale()]);
    }
}
