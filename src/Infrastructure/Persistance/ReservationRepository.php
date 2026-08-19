<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Reservation;
use App\Domaine\Entite\Sortie;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Accès aux réservations.
 *
 * `placesPrises()` porte une règle : **une immobilisation échue ne compte
 * plus**, et cela s'évalue à la lecture, sans qu'aucune tâche planifiée n'ait à
 * passer (ADR-003, architecture.md §5). Une panne du planificateur ne bloque
 * donc aucune vente.
 */
final class ReservationRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function parReference(string $reference): ?Reservation
    {
        return $this->entites->find(Reservation::class, (int) $reference);
    }

    /** @return list<Reservation> */
    public function deLaSortie(Sortie $sortie): array
    {
        return $this->entites->getRepository(Reservation::class)->findBy(['sortie' => $sortie]);
    }

    /**
     * Les places indisponibles : celles payées, plus celles qu'une
     * immobilisation non échue retient encore.
     */
    public function placesPrises(Sortie $sortie, DateTimeImmutable $maintenant): int
    {
        $places = 0;

        foreach ($this->deLaSortie($sortie) as $reservation) {
            if ($reservation->estConfirmee() || $reservation->immobiliseDesPlaces($maintenant)) {
                $places += $reservation->nombreDeParticipants();
            }
        }

        return $places;
    }

    /**
     * Les places prises par les **autres** réservations.
     *
     * Sert à vérifier, au moment d'encaisser, que la place retenue est toujours
     * là : si l'immobilisation a expiré pendant le tunnel de paiement et qu'un
     * autre client l'a emportée, elle ne l'est plus (SPEC-BOOKING-07 AC-7).
     */
    public function placesPrisesSauf(
        Sortie $sortie,
        Reservation $exclue,
        DateTimeImmutable $maintenant,
    ): int {
        $places = 0;

        foreach ($this->deLaSortie($sortie) as $reservation) {
            if ($reservation->reference() === $exclue->reference()) {
                continue;
            }

            if ($reservation->estConfirmee() || $reservation->immobiliseDesPlaces($maintenant)) {
                $places += $reservation->nombreDeParticipants();
            }
        }

        return $places;
    }

    /**
     * Les clients qui embarquent, c'est-à-dire ceux qui ont payé.
     *
     * @return list<Reservation>
     */
    public function inscrits(Sortie $sortie): array
    {
        return array_values(array_filter(
            $this->deLaSortie($sortie),
            static fn (Reservation $r): bool => $r->estConfirmee(),
        ));
    }

    public function enregistrer(Reservation $reservation): void
    {
        $this->entites->persist($reservation);
        $this->entites->flush();
    }
}
