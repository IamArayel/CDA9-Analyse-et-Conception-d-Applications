<?php

declare(strict_types=1);

namespace App\Infrastructure\Paiement;

use App\Domaine\PrestataireDePaiement;
use LogicException;

/**
 * L'adaptateur de paiement tant que Stripe n'est pas intégré.
 *
 * `ADR-001` §5 retient Stripe, dont l'intégration appartient à la
 * spécification `SPEC-BOOKING-07`. Le port est lié ici pour que le conteneur
 * compile, et **échoue bruyamment** : un encaissement qui ne partirait pas sans
 * rien dire coûterait une place vendue et non payée.
 *
 * En test, `PaiementSimule` le remplace et enregistre ce qui aurait été demandé.
 */
final class PrestataireNonConfigure implements PrestataireDePaiement
{
    public function encaisser(string $referenceDeReservation, int $montantEnCentimes): bool
    {
        throw new LogicException($this->motif());
    }

    public function rembourser(string $referenceDeReservation, int $montantEnCentimes): void
    {
        throw new LogicException($this->motif());
    }

    private function motif(): string
    {
        return 'Aucun prestataire de paiement n\'est configuré : l\'intégration de '
            .'Stripe appartient à SPEC-BOOKING-07.';
    }
}
