<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Horloge;
use App\Domaine\Politique\ValiditeDunAvoir;
use App\Domaine\Politique\ValiditeDunCode;
use App\Domaine\StatutDeCode;
use App\Domaine\VueDeCode;
use App\Infrastructure\Persistance\CodeRepository;
use InvalidArgumentException;

/**
 * Ce qu'un bon cadeau ou un avoir donne à voir (SPEC-BOOKING-09 et 10).
 *
 * `estUtilisable()` combine les deux conditions : le code n'a pas servi, et il
 * n'a pas dépassé son année de validité.
 */
final class ConsulterUnCode
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly CodeRepository $codes,
        private readonly ValiditeDunCode $validiteDunBon,
        private readonly ValiditeDunAvoir $validiteDunAvoir,
    ) {
    }

    public function executer(string $code): VueDeCode
    {
        $maintenant = $this->horloge->maintenant();
        $bon = $this->codes->bonCadeau($code);

        if ($bon !== null) {
            return new VueDeCode(
                $bon->montant(),
                $bon->dateDexpiration(),
                $bon->statut() === StatutDeCode::DISPONIBLE
                    && $this->validiteDunBon->estValide($bon->dateDachat(), $maintenant),
            );
        }

        $avoir = $this->codes->avoir($code);

        if ($avoir !== null) {
            return new VueDeCode(
                $avoir->montant(),
                $avoir->dateDexpiration(),
                $this->validiteDunAvoir->estUtilisable(
                    $avoir->statut(),
                    $avoir->dateDemission(),
                    $maintenant,
                ),
            );
        }

        throw new InvalidArgumentException(sprintf('Aucun code « %s ».', $code));
    }
}
