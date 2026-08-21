<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Persistance\TarifRepository;

final class ConsulterLaGrilleTarifaire
{
    public function __construct(private readonly TarifRepository $tarifs)
    {
    }

    /** @return array<string, array{adulte: int, enfant: int}> */
    public function executer(): array
    {
        return $this->tarifs->grille();
    }
}
