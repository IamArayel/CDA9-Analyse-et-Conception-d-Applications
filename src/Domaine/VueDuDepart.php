<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Ce qu'une carte du calendrier public donne à voir pour un départ.
 *
 * Un créneau peut porter plusieurs départs : un par bateau engagé. Modèle de
 * lecture, sans aucun calcul, comme `VueDeCreneau` : tout lui est fourni.
 */
final class VueDuDepart
{
    public function __construct(
        private readonly string $heure,
        private readonly string $type,
        private readonly string $bateau,
        private readonly int $idDeLaSortie,
        private readonly EtatDuDepart $etat,
        private readonly int $placesRestantes,
        private readonly DateTimeImmutable $heureDeFermeture,
    ) {
    }

    /** « 07:00 », « 10:00 » ou « 14:00 ». */
    public function heure(): string
    {
        return $this->heure;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function bateau(): string
    {
        return $this->bateau;
    }

    public function idDeLaSortie(): int
    {
        return $this->idDeLaSortie;
    }

    public function etat(): EtatDuDepart
    {
        return $this->etat;
    }

    public function placesRestantes(): int
    {
        return $this->placesRestantes;
    }

    public function heureDeFermeture(): DateTimeImmutable
    {
        return $this->heureDeFermeture;
    }
}
