<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\ConsulterLeCalendrier;
use App\Domaine\Horloge;
use App\Domaine\Politique\OffreDeCreneaux;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendrierController extends AbstractController
{
    public function __construct(
        private readonly ConsulterLeCalendrier $calendrier,
        private readonly Horloge $horloge,
    ) {
    }

    #[Route('/{_locale}/reserver', name: 'calendrier', requirements: ['_locale' => 'fr|en'])]
    public function semaine(Request $request): Response
    {
        $lundi = $this->lundiDemande($request);
        $filtre = (string) $request->query->get('type', 'TOUTES');

        if ($request->query->getBoolean('expire')) {
            $this->addFlash('erreur', 'erreur.immobilisation_expiree');
        }

        return $this->render('reservation/calendrier.html.twig', [
            'jours' => $this->calendrier->executerPourLaSemaine($lundi),
            'heures' => OffreDeCreneaux::CRENEAUX,
            'semaine_precedente' => $lundi->modify('-7 days'),
            'semaine_suivante' => $lundi->modify('+7 days'),
            'filtre' => $filtre,
        ]);
    }

    private function lundiDemande(Request $request): DateTimeImmutable
    {
        $parametre = $request->query->get('semaine');
        $reference = is_string($parametre)
            ? DateTimeImmutable::createFromFormat('Y-m-d', $parametre)
            : false;

        if ($reference === false) {
            $reference = $this->horloge->maintenant();
        }

        return $reference
            ->modify(sprintf('-%d days', ((int) $reference->format('N')) - 1))
            ->setTime(0, 0);
    }
}
