<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use App\Domaine\StatutDeReservation;
use DateTimeImmutable;

/**
 * Une réservation sur une sortie.
 *
 * Deux points portent des règles :
 *
 * - `montant` est **recopié** à la validation du formulaire, jamais relu dans
 *   la grille tarifaire : un client ne peut pas être débité d'un montant
 *   différent de celui qui lui a été présenté (SPEC-BOOKING-06 AC-3) ;
 * - `expireLe` porte l'échéance de l'immobilisation des places. Elle est
 *   **évaluée à la lecture** : une réservation échue ne compte plus dans les
 *   places prises, sans qu'aucune tâche n'ait à passer (ADR-003).
 *
 * `bonCadeau` et `avoir` sont exclusifs : une contrainte CHECK en base porte le
 * non-cumul, et l'unicité de chaque clé étrangère interdit qu'un code serve
 * deux fois.
 */
class Reservation
{
    private ?int $id = null;
    private StatutDeReservation $statut = StatutDeReservation::EN_ATTENTE_DE_PAIEMENT;
    private ?BonCadeau $bonCadeau = null;
    private ?Avoir $avoir = null;

    /** @param int $montant en centimes */
    public function __construct(
        private Sortie $sortie,
        private string $nomClient,
        private string $prenomClient,
        private string $email,
        private string $telephoneMobile,
        private int $nombreDAdultes,
        private int $nombreDEnfants,
        private int $montant,
        private string $langue,
        private DateTimeImmutable $dateDeCreation,
        private DateTimeImmutable $expireLe,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    /** La référence stable citée par le prestataire et par les envois. */
    public function reference(): string
    {
        return (string) $this->id;
    }

    public function sortie(): Sortie
    {
        return $this->sortie;
    }

    public function nomClient(): string
    {
        return $this->nomClient;
    }

    public function prenomClient(): string
    {
        return $this->prenomClient;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function telephoneMobile(): string
    {
        return $this->telephoneMobile;
    }

    public function nombreDAdultes(): int
    {
        return $this->nombreDAdultes;
    }

    public function nombreDEnfants(): int
    {
        return $this->nombreDEnfants;
    }

    /** Adultes et enfants comptent chacun pour une place. */
    public function nombreDeParticipants(): int
    {
        return $this->nombreDAdultes + $this->nombreDEnfants;
    }

    /** En centimes, figé à la validation du formulaire. */
    public function montant(): int
    {
        return $this->montant;
    }

    public function langue(): string
    {
        return $this->langue;
    }

    public function dateDeCreation(): DateTimeImmutable
    {
        return $this->dateDeCreation;
    }

    public function expireLe(): DateTimeImmutable
    {
        return $this->expireLe;
    }

    public function statut(): StatutDeReservation
    {
        return $this->statut;
    }

    public function confirmer(): void
    {
        $this->statut = StatutDeReservation::CONFIRMEE;
    }

    public function annuler(): void
    {
        $this->statut = StatutDeReservation::ANNULEE;
    }

    public function estAnnulee(): bool
    {
        return $this->statut === StatutDeReservation::ANNULEE;
    }

    public function estConfirmee(): bool
    {
        return $this->statut === StatutDeReservation::CONFIRMEE;
    }

    /**
     * Immobilise encore des places : le formulaire est validé, le paiement non
     * abouti, et l'échéance n'est pas passée.
     */
    public function immobiliseDesPlaces(DateTimeImmutable $maintenant): bool
    {
        return $this->statut === StatutDeReservation::EN_ATTENTE_DE_PAIEMENT
            && $maintenant < $this->expireLe;
    }

    /**
     * Efface les données personnelles sans supprimer la ligne : le planning
     * passé et les montants restent lisibles, le client n'est plus
     * identifiable (SPEC-NFR-04 AC-3).
     */
    public function anonymiser(): void
    {
        $this->nomClient = '';
        $this->prenomClient = '';
        $this->email = '';
        $this->telephoneMobile = '';
    }

    public function estAnonymisee(): bool
    {
        return $this->email === '';
    }

    public function bonCadeau(): ?BonCadeau
    {
        return $this->bonCadeau;
    }

    public function avoir(): ?Avoir
    {
        return $this->avoir;
    }

    public function appliquerLeBonCadeau(BonCadeau $bonCadeau): void
    {
        $this->bonCadeau = $bonCadeau;
    }

    public function appliquerLavoir(Avoir $avoir): void
    {
        $this->avoir = $avoir;
    }

    public function porteDejaUnCode(): bool
    {
        return $this->bonCadeau !== null || $this->avoir !== null;
    }
}
