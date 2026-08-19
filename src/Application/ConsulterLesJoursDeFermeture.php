<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\JourDeFermeture;
use App\Infrastructure\Persistance\CalendrierRepository;

/**
 * Les jours de fermeture, dans l'ordre (SPEC-ADMIN-04 AC-1).
 *
 * Le 25 décembre et le 1er janvier y figurent **sans que personne ne les ait
 * saisis** : ce sont des fermetures d'usage, posées à l'installation.
 */
final class ConsulterLesJoursDeFermeture
{
    public function __construct(private readonly CalendrierRepository $calendrier)
    {
    }

    /** @return list<string> au format « 2026-12-25 » */
    public function executer(): array
    {
        return array_map(
            static fn (JourDeFermeture $jour): string => $jour->date()->format('Y-m-d'),
            $this->calendrier->joursDeFermeture(),
        );
    }
}
