<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Combien de temps les places d'un formulaire validé restent retenues
 * (SPEC-BOOKING-03, ADR-003).
 *
 * Quinze minutes est une **hypothèse d'équipe**, jamais soumise au client :
 * assez pour un paiement sur mobile avec authentification forte, assez peu pour
 * ne pas stériliser une place. Elle est écrite ici pour être discutable.
 *
 * L'échéance s'évalue à la lecture, jamais par une tâche planifiée : une panne
 * du planificateur ne doit bloquer aucune vente.
 */
final class Immobilisation
{
    public const DUREE_EN_MINUTES = 15;

    public function echeance(DateTimeImmutable $validationDuFormulaire): DateTimeImmutable
    {
        return $validationDuFormulaire->modify(sprintf('+%d minutes', self::DUREE_EN_MINUTES));
    }

    public function estEchue(DateTimeImmutable $echeance, DateTimeImmutable $maintenant): bool
    {
        return $maintenant >= $echeance;
    }
}
