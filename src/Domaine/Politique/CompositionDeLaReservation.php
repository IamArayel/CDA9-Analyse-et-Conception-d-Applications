<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * Qui peut composer une réservation (SPEC-BOOKING-01).
 *
 * Deux règles écrites : au moins un participant, et au moins un adulte dès
 * qu'un enfant est déclaré. **Elles se ramènent à une seule**, « au moins un
 * adulte » : un groupe sans adulte est refusé qu'il déclare des enfants ou
 * personne. Le code dit donc une chose là où la spécification en dit deux, et
 * c'est volontaire.
 *
 * Aucun minimum de personnes n'est imposé : un client seul est accepté. C'est
 * la règle corrigée en v3, la v1 et la v2 exigeaient à tort deux personnes.
 */
final class CompositionDeLaReservation
{
    public const MOTIF_ADULTE_REQUIS = 'ADULTE_REQUIS';

    public function estValide(int $adultes, int $enfants): bool
    {
        return $adultes >= 1 && $enfants >= 0;
    }

    public function motifDuRefus(int $adultes, int $enfants): ?string
    {
        return $this->estValide($adultes, $enfants) ? null : self::MOTIF_ADULTE_REQUIS;
    }
}
