<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Les états d'une réservation, cf. mcd-mld.md §6.
 *
 * `EN_ATTENTE_DE_PAIEMENT` est l'état d'une réservation dont le formulaire est
 * validé et dont les places sont immobilisées, mais que personne n'a payée
 * (ADR-003).
 */
enum StatutDeReservation: string
{
    case EN_ATTENTE_DE_PAIEMENT = 'EN_ATTENTE_DE_PAIEMENT';
    case CONFIRMEE = 'CONFIRMEE';
    case ANNULEE = 'ANNULEE';
}
