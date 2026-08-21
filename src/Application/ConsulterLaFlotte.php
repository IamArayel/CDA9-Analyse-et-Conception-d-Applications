<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Bateau;
use App\Domaine\VueDunBateau;
use App\Infrastructure\Persistance\BateauRepository;

final class ConsulterLaFlotte
{
    public function __construct(private readonly BateauRepository $bateaux)
    {
    }

    /** @return list<VueDunBateau> */
    public function executer(): array
    {
        return array_map(
            static fn (Bateau $bateau): VueDunBateau => new VueDunBateau(
                $bateau->nom(),
                $bateau->capacite(),
                $bateau->forfaitDePrivatisation(),
            ),
            $this->bateaux->tous(),
        );
    }
}
