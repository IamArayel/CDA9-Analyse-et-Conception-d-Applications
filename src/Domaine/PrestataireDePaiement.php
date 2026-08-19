<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Le prestataire de paiement, vu par le domaine.
 *
 * La signature ne transporte qu'une référence et un montant : **aucune donnée
 * de carte n'entre dans l'application** (REQ-018, SPEC-NFR-04 AC-2). C'est la
 * forme même de cette interface qui le garantit, pas une consigne.
 *
 * Les montants sont en centimes, pour que rien ne dépende d'un arrondi
 * flottant.
 */
interface PrestataireDePaiement
{
    /** Rend false si le prestataire a refusé la transaction. */
    public function encaisser(string $referenceDeReservation, int $montantEnCentimes): bool;

    public function rembourser(string $referenceDeReservation, int $montantEnCentimes): void;
}
