<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use DateTimeImmutable;

/**
 * Un créneau de départ : une date et une heure.
 *
 * Un créneau porte de zéro à plusieurs sorties, une par bateau engagé. C'est le
 * créneau, et non la sortie, que le gérant met en alerte : la météo ne
 * distingue pas les bateaux (SPEC-CANCEL-06 AC-10).
 */
class Creneau
{
    private ?int $id = null;

    public function __construct(
        private DateTimeImmutable $date,
        private DateTimeImmutable $heureDeDepart,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    /** « 07:00 », « 10:00 » ou « 14:00 ». */
    public function heureDeDepart(): string
    {
        return $this->heureDeDepart->format('H:i');
    }

    /** L'instant exact du départ, dans le fuseau d'exploitation. */
    public function departPrevu(): DateTimeImmutable
    {
        return $this->date->setTime(
            (int) $this->heureDeDepart->format('H'),
            (int) $this->heureDeDepart->format('i'),
        );
    }
}
