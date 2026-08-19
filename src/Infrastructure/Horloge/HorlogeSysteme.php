<?php

declare(strict_types=1);

namespace App\Infrastructure\Horloge;

use App\Domaine\FuseauDexploitation;
use App\Domaine\Horloge;
use DateTimeImmutable;
use DateTimeZone;

/**
 * L'horloge de production : la seule classe du projet qui lit l'heure système.
 *
 * Elle vit dans l'infrastructure, et non dans le domaine, pour que la règle de
 * revue d'`architecture.md` §2 reste vérifiable d'un coup d'œil : un appel à
 * l'heure système ailleurs que dans ce fichier est un défaut.
 *
 * En test, `HorlogeFigee` la remplace et rend l'instant que le cas a fixé.
 */
final class HorlogeSysteme implements Horloge
{
    public function __construct(
        private readonly string $fuseauDexploitation = FuseauDexploitation::IDENTIFIANT,
    ) {
    }

    public function maintenant(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone($this->fuseauDexploitation));
    }
}
