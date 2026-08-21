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
     * @param array<string, StatutDeSortie>                    $statutsParBateau
     * @param array<string, DateTimeImmutable|null>            $datesDeMiseEnAlerteParBateau
     * @param array<string, list<array<string, mixed>>>        $inscritsParBateau
     * @param array<string, list<array{minutes: int}>>         $immobilisationsParBateau
     */
    public function __construct(
        private readonly array $statutsParBateau,
        private readonly array $datesDeMiseEnAlerteParBateau,
        private readonly array $inscritsParBateau,
        private readonly bool $estReservable,
        private readonly bool $risqueDannulationSignale,
        private readonly bool $estAnnulable,
        private readonly array $immobilisationsParBateau = [],
        private readonly ?string $previsionMeteo = null,
        private readonly ?DateTimeImmutable $fermetureDesVentes = null,
    ) {
    }

    public function previsionMeteo(): ?string
    {
        return $this->previsionMeteo;
    }

    /** Quand les ventes ferment pour ce créneau (`FermetureDesReservations`). */
    public function fermetureDesVentes(): ?DateTimeImmutable
    {
        return $this->fermetureDesVentes;
    }

    /** @return list<string> */
    public function bateaux(): array
    {
        return array_keys($this->statutsParBateau);
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

    /**
     * Les réservations dont le formulaire est validé mais le paiement non
     * abouti : elles retiennent des places sans compter dans les inscrits
     * (SPEC-CANCEL-01, cas limite 2).
     *
     * @return list<array{minutes: int}>
     */
    public function immobilisations(string $bateau): array
    {
        return $this->immobilisationsParBateau[$bateau] ?? [];
    }
}
