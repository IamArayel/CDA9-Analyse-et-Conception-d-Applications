<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Avoir;
use App\Domaine\Horloge;
use App\Domaine\Politique\ValiditeDunCode;
use App\Infrastructure\Persistance\CodeRepository;

/**
 * Émettre un avoir (SPEC-ADMIN-06, SPEC-BOOKING-10).
 *
 * **C'est la seule origine d'un avoir**, depuis la correction du 2026-08-14 :
 * l'issue « avoir » d'une annulation demandée par le client. Le montant est
 * celui que le gérant saisit, pas un montant calculé : le barème dégressif
 * reste à son appréciation.
 *
 * Aucun encaissement : un avoir ne se vend pas, il se concède.
 */
final class EmettreUnAvoir
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly CodeRepository $codes,
        private readonly ValiditeDunCode $validite,
    ) {
    }

    /**
     * @param int                  $montant      en centimes
     * @param array<string, mixed> $beneficiaire
     *
     * @return string le code de l'avoir
     */
    public function executer(int $montant, array $beneficiaire): string
    {
        $emission = $this->horloge->maintenant();

        $avoir = new Avoir(
            $this->codes->codeNeuf(),
            $montant,
            $emission,
            $this->validite->expirationDe($emission),
        );

        $this->codes->enregistrer($avoir);

        return $avoir->code();
    }
}
