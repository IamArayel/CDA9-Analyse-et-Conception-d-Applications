<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\VueDesParametres;
use App\Infrastructure\Persistance\ParametreRepository;

final class ConsulterLesParametres
{
    public function __construct(private readonly ParametreRepository $parametres)
    {
    }

    public function executer(): VueDesParametres
    {
        $parametre = $this->parametres->reglages();

        return new VueDesParametres(
            $parametre->heureDouverture(),
            $parametre->heureDeFermeture(),
            $parametre->heureDalerte(),
            $parametre->delaiDeConfirmationEnHeures(),
            $parametre->delaiDeRappelEnHeures(),
        );
    }
}
