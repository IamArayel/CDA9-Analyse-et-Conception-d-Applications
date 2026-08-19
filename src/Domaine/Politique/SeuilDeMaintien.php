<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use App\Domaine\DecisionDeMaintien;
use DateTimeImmutable;

/**
 * Le seuil à partir duquel une sortie est maintenue (SPEC-BOOKING-03, REQ-002).
 *
 * « À partir de 6 inscrits » : le mot **à partir de** est le cœur de la règle,
 * six suffit. C'est ce que vérifie le cas de test, sur la borne exacte.
 *
 * L'heure de départ et l'instant courant figurent dans la signature parce que
 * la règle est celle du **contrôle des 24 heures** : c'est la couche
 * application qui décide quand le contrôle passe, et le domaine reçoit ces deux
 * repères plutôt que d'aller les chercher (ADR-005, option A). La décision
 * elle-même ne dépend que du nombre d'inscrits.
 */
final class SeuilDeMaintien
{
    public const SEUIL = 6;

    public function decider(
        int $nombreDInscrits,
        DateTimeImmutable $heureDeDepart,
        DateTimeImmutable $maintenant,
    ): DecisionDeMaintien {
        return $nombreDInscrits >= self::SEUIL
            ? DecisionDeMaintien::maintien()
            : DecisionDeMaintien::annulation();
    }
}
