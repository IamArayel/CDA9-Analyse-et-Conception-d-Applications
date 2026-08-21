<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce qu'une journée propose au client.
 *
 * Un jour de fermeture ne propose **aucun** créneau, pas même grisé
 * (SPEC-BOOKING-02 AC-4). Les trois créneaux, eux, sont proposés tous les jours
 * d'ouverture : c'est le type de sortie qui varie avec la saison, pas l'horaire.
 */
final class VueDeJournee
{
    /**
     * @param list<string>      $creneauxProposes     heures de départ, ordre chronologique
     * @param list<string>      $typesDeSortieProposes
     * @param list<VueDuDepart> $departs              un par bateau engagé, tous créneaux confondus
     */
    public function __construct(
        private readonly string $date,
        private readonly array $creneauxProposes,
        private readonly array $typesDeSortieProposes,
        private readonly array $departs,
    ) {
    }

    /** « 2026-07-20 ». */
    public function date(): string
    {
        return $this->date;
    }

    /** @return list<string> */
    public function creneauxProposes(): array
    {
        return $this->creneauxProposes;
    }

    /** @return list<string> */
    public function typesDeSortieProposes(): array
    {
        return $this->typesDeSortieProposes;
    }

    /** @return list<VueDuDepart> */
    public function departs(): array
    {
        return $this->departs;
    }
}
