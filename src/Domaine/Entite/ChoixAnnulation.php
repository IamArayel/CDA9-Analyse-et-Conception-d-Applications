<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use App\Domaine\IssueDannulation;
use DateTimeImmutable;

/**
 * L'issue retenue pour une annulation **demandée par le client**
 * (SPEC-ADMIN-06).
 *
 * L'unicité sur `reservation_id` porte une règle : une réservation ne compte
 * qu'une issue, on ne rejoue pas une annulation. Et `avoir` n'est renseigné que
 * pour l'issue `AVOIR`, seule des trois à produire un code.
 *
 * Rien de tout cela n'existe pour une annulation décidée par le gérant, météo
 * comprise : elle rembourse intégralement, sans alternative.
 */
class ChoixAnnulation
{
    private ?int $id = null;
    private ?Avoir $avoir = null;

    public function __construct(
        private Reservation $reservation,
        private IssueDannulation $type,
        private DateTimeImmutable $dateDenregistrement,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function reservation(): Reservation
    {
        return $this->reservation;
    }

    public function type(): IssueDannulation
    {
        return $this->type;
    }

    public function dateDenregistrement(): DateTimeImmutable
    {
        return $this->dateDenregistrement;
    }

    public function avoir(): ?Avoir
    {
        return $this->avoir;
    }

    public function rattacherLavoir(Avoir $avoir): void
    {
        $this->avoir = $avoir;
    }
}
