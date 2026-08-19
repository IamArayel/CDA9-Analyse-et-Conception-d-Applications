<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Persistance\CalendrierRepository;

/**
 * Retirer un jour de fermeture (SPEC-ADMIN-04 AC-3).
 *
 * Le retrait prend effet immédiatement : les trois créneaux sont de nouveau
 * proposés le jour même de l'enregistrement, sans redéploiement.
 */
final class RetirerUnJourDeFermeture
{
    public function __construct(private readonly CalendrierRepository $calendrier)
    {
    }

    public function executer(string $jour): void
    {
        $fermeture = $this->calendrier->parDate($jour);

        if ($fermeture === null) {
            return;
        }

        $this->calendrier->retirer($fermeture);
    }
}
