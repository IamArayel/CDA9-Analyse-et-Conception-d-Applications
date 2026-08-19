<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use App\Domaine\StatutDeSortie;
use DateTimeImmutable;

/**
 * Une sortie : un bateau engagé sur un créneau, pour un type et une formule.
 *
 * **`creneauBaleines` n'est pas une propriété métier.** C'est une colonne
 * générée par la base, qui vaut l'identifiant du créneau pour une sortie
 * baleines et NULL sinon ; l'index unique posé dessus empêche deux sorties
 * baleines sur le même créneau, y compris sous deux demandes simultanées
 * (mcd-mld.md §7). Elle n'est jamais écrite depuis le code, seulement lue.
 */
class Sortie
{
    public const FORMULE_INDIVIDUELLE = 'INDIVIDUELLE';
    public const FORMULE_PRIVATISATION = 'PRIVATISATION';

    private ?int $id = null;
    private StatutDeSortie $statut = StatutDeSortie::PROGRAMMEE;
    private ?DateTimeImmutable $dateDeMiseEnAlerte = null;

    /** Colonne générée, lue seulement. */
    private ?int $creneauBaleines = null;

    public function __construct(
        private Creneau $creneau,
        private Bateau $bateau,
        private string $typeDeSortie,
        private string $formule = self::FORMULE_INDIVIDUELLE,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function creneau(): Creneau
    {
        return $this->creneau;
    }

    public function bateau(): Bateau
    {
        return $this->bateau;
    }

    public function typeDeSortie(): string
    {
        return $this->typeDeSortie;
    }

    public function formule(): string
    {
        return $this->formule;
    }

    public function estPrivatisee(): bool
    {
        return $this->formule === self::FORMULE_PRIVATISATION;
    }

    public function statut(): StatutDeSortie
    {
        return $this->statut;
    }

    public function dateDeMiseEnAlerte(): ?DateTimeImmutable
    {
        return $this->dateDeMiseEnAlerte;
    }

    public function mettreEnAlerte(DateTimeImmutable $quand): void
    {
        $this->statut = StatutDeSortie::EN_ALERTE;
        $this->dateDeMiseEnAlerte = $quand;
    }

    public function annuler(): void
    {
        $this->statut = StatutDeSortie::ANNULEE;
    }

    public function estAnnulee(): bool
    {
        return $this->statut === StatutDeSortie::ANNULEE;
    }

    public function privatiser(): void
    {
        $this->formule = self::FORMULE_PRIVATISATION;
    }
}
