<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Horloge;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use InvalidArgumentException;

/**
 * Le nombre de places encore disponibles sur une sortie (SPEC-BOOKING-03,
 * REQ-004).
 *
 * Aucun compteur n'est stocké : le nombre est **toujours recalculé**. À la
 * volumétrie attendue, un compteur dénormalisé ne gagnerait rien et finirait
 * par se désynchroniser (architecture.md §5).
 *
 * Les places immobilisées comptent comme indisponibles : un client peut donc
 * voir « 0 place » alors que personne n'a encore payé, et c'est assumé.
 */
final class ConsulterLesPlacesDisponibles
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
    ) {
    }

    public function pour(string $sortie): int
    {
        $laSortie = $this->sorties->parReference($sortie);

        if ($laSortie === null) {
            throw new InvalidArgumentException(sprintf('Aucune sortie « %s ».', $sortie));
        }

        // Une privatisation bloque le bateau entier, pas seulement les places
        // de ses participants.
        if ($laSortie->estPrivatisee()) {
            return 0;
        }

        return max(0, $laSortie->bateau()->capacite() - $this->reservations->placesPrises(
            $laSortie,
            $this->horloge->maintenant(),
        ));
    }
}
