<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Les trois états d'une sortie, cf. RG-18 et mcd-mld.md §6.
 *
 * `EN_ALERTE` ne décide rien : l'alerte prévient, elle n'annule pas. C'est
 * `SPEC-CANCEL-02` qui annule, sur décision du gérant. Une sortie en alerte
 * laissée sans suite a lieu.
 */
enum StatutDeSortie: string
{
    case PROGRAMMEE = 'PROGRAMMEE';
    case EN_ALERTE = 'EN_ALERTE';
    case ANNULEE = 'ANNULEE';
}
