<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use DateTimeImmutable;

/**
 * Un jour où aucune sortie n'est proposée (SPEC-ADMIN-04).
 *
 * Le 25 décembre et le 1er janvier y figurent sans que personne les ait saisis.
 * `recurrentAnnuel` distingue une fermeture d'usage, qui revient chaque année,
 * d'une fermeture ponctuelle : c'est une hypothèse d'équipe, le client n'ayant
 * pas été interrogé là-dessus.
 */
class JourDeFermeture
{
    private ?int $id = null;

    public function __construct(
        private DateTimeImmutable $date,
        private bool $recurrentAnnuel = false,
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

    public function estRecurrentAnnuel(): bool
    {
        return $this->recurrentAnnuel;
    }
}
