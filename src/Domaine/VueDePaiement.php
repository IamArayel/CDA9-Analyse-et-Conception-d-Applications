<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Une ligne du journal des versements d'une réservation.
 *
 * `estRetracte()` porte volontairement un nom différent de celui de l'entité :
 * pour le gérant qui lit le journal, la ligne n'est pas « annulée », elle a été
 * reprise. Ce qui est annulé, c'est une réservation.
 */
final class VueDePaiement
{
    /** @param int $montant en centimes */
    public function __construct(
        private readonly string $type,
        private readonly int $montant,
        private readonly string $canal,
        private readonly DateTimeImmutable $date,
        private readonly bool $retracte,
    ) {
    }

    /** `ACOMPTE` ou `SOLDE`. */
    public function type(): string
    {
        return $this->type;
    }

    /** En centimes. */
    public function montant(): int
    {
        return $this->montant;
    }

    /** `EN_LIGNE` ou `SUR_PLACE`. */
    public function canal(): string
    {
        return $this->canal;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function estRetracte(): bool
    {
        return $this->retracte;
    }
}
