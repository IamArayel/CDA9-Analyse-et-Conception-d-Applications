<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * Ce qui fait un bateau valide (SPEC-ADMIN-05 AC-3 et AC-4).
 *
 * Le nom identifie le bateau sur le planning et pour le gérant : il doit rester
 * unique. Et une capacité n'est ni nulle ni fractionnaire, on n'embarque pas
 * une demi-personne.
 */
final class CreationDunBateau
{
    /** @param list<string> $nomsExistants les bateaux déjà dans la flotte */
    public function estValide(string $nom, array $nomsExistants, int|float $capacite): bool
    {
        if (in_array($nom, $nomsExistants, true)) {
            return false;
        }

        return $this->estUnNombreEntierDePlaces($capacite);
    }

    private function estUnNombreEntierDePlaces(int|float $capacite): bool
    {
        if ($capacite <= 0) {
            return false;
        }

        return (float) $capacite === floor((float) $capacite);
    }
}
