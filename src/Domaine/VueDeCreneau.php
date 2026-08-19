<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Ce qu'un créneau donne à voir, côté gestion comme côté client.
 *
 * Un créneau porte une sortie **par bateau** : trois de ses accesseurs prennent
 * donc un nom de bateau. `estReservable()` et `risqueDannulationSignale()`, eux,
 * valent pour le créneau entier, la météo ne distinguant pas les bateaux
 * (SPEC-CANCEL-06 AC-10).
 *
 * Modèle de lecture, sans aucun calcul : tout lui est fourni.
 */
final class VueDeCreneau
{
    /**
     * @param array<string, StatutDeSortie>            $statutsParBateau
     * @param array<string, DateTimeImmutable|null>    $datesDeMiseEnAlerteParBateau
     * @param array<string, list<array<string, mixed>>> $inscritsParBateau
     */
    public function __construct(
        private readonly array $statutsParBateau,
        private readonly array $datesDeMiseEnAlerteParBateau,
        private readonly array $inscritsParBateau,
        private readonly bool $estReservable,
        private readonly bool $risqueDannulationSignale,
        private readonly bool $estAnnulable,
    ) {
    }

    public function statutDeLaSortie(string $bateau): ?StatutDeSortie
    {
        return $this->statutsParBateau[$bateau] ?? null;
    }

    public function dateDeMiseEnAlerte(string $bateau): ?DateTimeImmutable
    {
        return $this->datesDeMiseEnAlerteParBateau[$bateau] ?? null;
    }

    /**
     * Les clients qui embarquent, c'est-à-dire ceux qui ont payé. Une
     * réservation immobilisée mais non payée n'est pas un inscrit
     * (SPEC-CANCEL-01, cas limite 2).
     *
     * @return list<array<string, mixed>>
     */
    public function inscrits(string $bateau): array
    {
        return $this->inscritsParBateau[$bateau] ?? [];
    }

    public function estReservable(): bool
    {
        return $this->estReservable;
    }

    public function risqueDannulationSignale(): bool
    {
        return $this->risqueDannulationSignale;
    }

    public function estAnnulable(): bool
    {
        return $this->estAnnulable;
    }
}
