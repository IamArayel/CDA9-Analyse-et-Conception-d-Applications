<?php

declare(strict_types=1);

namespace App\Domaine\Service;

use App\Domaine\Entite\Sortie;
use App\Domaine\StatutDeLaCarteDeSortie;
use App\Domaine\StatutDeSortie;
use DateTimeImmutable;

/**
 * L'état d'une carte de sortie sur le tableau de bord du gérant (G2).
 *
 * Priorité, du plus définitif au plus informatif : une sortie déjà partie
 * l'est pour de bon ; l'alerte et le complet restent, eux, réversibles tant
 * que le départ n'a pas eu lieu.
 */
final class CalculDuStatutDeLaCarte
{
    public function pour(
        Sortie $sortie,
        DateTimeImmutable $depart,
        int $participants,
        DateTimeImmutable $maintenant,
    ): StatutDeLaCarteDeSortie {
        if ($depart <= $maintenant) {
            return StatutDeLaCarteDeSortie::PARTIE;
        }

        if ($sortie->statut() === StatutDeSortie::EN_ALERTE) {
            return StatutDeLaCarteDeSortie::EN_ALERTE;
        }

        if ($participants >= $sortie->bateau()->capacite()) {
            return StatutDeLaCarteDeSortie::COMPLETE;
        }

        return StatutDeLaCarteDeSortie::OUVERTE;
    }
}
