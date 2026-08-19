<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\VueDeReservation;
use App\Infrastructure\Persistance\ReservationRepository;
use InvalidArgumentException;

/**
 * Ce qu'une réservation donne à voir (SPEC-BOOKING-06, SPEC-CANCEL-04).
 *
 * `issuesProposees()` est vide : le triptyque report, avoir et remboursement
 * n'existe que pour une annulation **demandée par le client**, et il est servi
 * par SPEC-ADMIN-06. Une annulation décidée par le gérant, météo comprise,
 * rembourse intégralement sans alternative.
 */
final class ConsulterUneReservation
{
    private const DEVISE = 'EUR';

    public function __construct(private readonly ReservationRepository $reservations)
    {
    }

    public function executer(string $reference): VueDeReservation
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        return new VueDeReservation(
            $reservation->statut(),
            $reservation->montant(),
            self::DEVISE,
            $reservation->telephoneMobile(),
            issuesProposees: [],
            avoirProduit: null,
        );
    }
}
