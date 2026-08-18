<?php

declare(strict_types=1);

namespace App\Tests\Doublures;

use App\Domaine\Horloge;
use App\Tests\JeuDeDonneesDeReference as Reference;
use DateTimeImmutable;

/**
 * L'horloge des tests, figée sur l'instant que le cas fixe, cf. ADR-005.
 *
 * Le domaine ne lit jamais l'heure système : il reçoit cette horloge. Un test
 * fixe un instant, avance d'une minute, et observe, sans jamais attendre.
 */
final class HorlogeFigee implements Horloge
{
    private DateTimeImmutable $instant;

    public function __construct(string $dateEtHeureLocales)
    {
        $this->instant = Reference::instant($dateEtHeureLocales);
    }

    public function maintenant(): DateTimeImmutable
    {
        return $this->instant;
    }

    /** « Quand nous sommes le 18 juillet à 14h16 ». */
    public function nousSommesLe(string $dateEtHeureLocales): void
    {
        $this->instant = Reference::instant($dateEtHeureLocales);
    }
}
