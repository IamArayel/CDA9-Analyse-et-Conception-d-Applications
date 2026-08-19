<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\BonCadeau;
use App\Domaine\Horloge;
use App\Domaine\Politique\ValiditeDunCode;
use App\Domaine\PrestataireDePaiement;
use App\Infrastructure\Persistance\CodeRepository;

/**
 * Acheter un bon cadeau (SPEC-BOOKING-09).
 *
 * **La signature ne transporte aucun type de sortie.** C'est la règle inversée
 * en v4 : un bon vaut un montant, il ne vaut plus une sortie, et aucun écran de
 * l'achat ne demande donc ni type ni catégorie de passager.
 */
final class AcheterUnBonCadeau
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly PrestataireDePaiement $prestataire,
        private readonly CodeRepository $codes,
        private readonly ValiditeDunCode $validite,
    ) {
    }

    /**
     * @param int                     $montant  en centimes
     * @param array<string, mixed>    $acheteur
     *
     * @return string le code délivré
     */
    public function executer(int $montant, array $acheteur): string
    {
        $achat = $this->horloge->maintenant();

        $bon = new BonCadeau(
            $this->codes->codeNeuf(),
            $montant,
            $achat,
            $this->validite->expirationDe($achat),
            $acheteur['email'] ?? null,
        );

        $this->codes->enregistrer($bon);
        $this->prestataire->encaisser($bon->code(), $montant);

        return $bon->code();
    }
}
