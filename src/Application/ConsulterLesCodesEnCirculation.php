<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\VueDunCodeEnCirculation;
use App\Infrastructure\Persistance\CodeRepository;

final class ConsulterLesCodesEnCirculation
{
    public function __construct(private readonly CodeRepository $codes)
    {
    }

    /** @return list<VueDunCodeEnCirculation> */
    public function executer(): array
    {
        $vues = [];

        foreach ($this->codes->tousLesBonsCadeaux() as $bon) {
            $vues[] = new VueDunCodeEnCirculation($bon->code(), $bon->montant(), 'bon', $bon->statut());
        }

        foreach ($this->codes->tousLesAvoirs() as $avoir) {
            $vues[] = new VueDunCodeEnCirculation($avoir->code(), $avoir->montant(), 'avoir', $avoir->statut());
        }

        return $vues;
    }
}
