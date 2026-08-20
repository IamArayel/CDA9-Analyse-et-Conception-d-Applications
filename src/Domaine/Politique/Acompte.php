<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * L'acompte exigé à la réservation (SPEC-BOOKING-07, REQ-108 et REQ-109).
 *
 * 30 % du montant pour une sortie, 50 % du forfait pour une privatisation,
 * **arrondi au centime**. Arrondir à l'euro ferait perdre ou gagner jusqu'à
 * 50 centimes par réservation, ce que ni le gérant ni le passager
 * n'accepteraient sur un relevé.
 *
 * Les deux taux sont **figés** : `CR-06/Q08` refuse explicitement qu'ils soient
 * réglables depuis l'espace de gestion.
 */
final class Acompte
{
    public const TAUX_SORTIE = 30;
    public const TAUX_PRIVATISATION = 50;

    /** @param int $montantTotal en centimes */
    public function pourUneSortie(int $montantTotal): int
    {
        return $this->part($montantTotal, self::TAUX_SORTIE);
    }

    /** @param int $forfait en centimes */
    public function pourUnePrivatisation(int $forfait): int
    {
        return $this->part($forfait, self::TAUX_PRIVATISATION);
    }

    private function part(int $montantEnCentimes, int $taux): int
    {
        return (int) round($montantEnCentimes * $taux / 100);
    }
}
