<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Tarif;
use App\Domaine\Politique\ValiditeDunTarif;
use App\Infrastructure\Persistance\TarifRepository;
use InvalidArgumentException;

/**
 * Modifier la grille tarifaire (SPEC-ADMIN-02).
 *
 * Le nouveau tarif ne rattrape **aucune** réservation existante : le montant a
 * été recopié sur chacune à sa validation. Un client ne peut donc pas être
 * débité d'un montant différent de celui qui lui a été présenté.
 */
final class ModifierUnTarif
{
    public function __construct(
        private readonly TarifRepository $tarifs,
        private readonly ValiditeDunTarif $validite,
    ) {
    }

    /**
     * @param int $prixAdulte en centimes
     * @param int $prixEnfant en centimes
     */
    public function executer(string $typeDeSortie, int $prixAdulte, int $prixEnfant): void
    {
        if (!$this->validite->estValide($prixAdulte) || !$this->validite->estValide($prixEnfant)) {
            throw new InvalidArgumentException('Un tarif est strictement positif.');
        }

        $tarif = $this->tarifs->parTypeDeSortie($typeDeSortie);

        if ($tarif === null) {
            $this->tarifs->enregistrer(new Tarif($typeDeSortie, $prixAdulte, $prixEnfant));

            return;
        }

        $tarif->modifier($prixAdulte, $prixEnfant);
        $this->tarifs->enregistrer($tarif);
    }
}
