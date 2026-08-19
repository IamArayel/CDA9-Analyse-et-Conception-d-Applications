<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Ce que rend une tentative de réservation, acceptée ou refusée.
 *
 * Ce type ne décide rien : il transporte la décision prise ailleurs. Le motif
 * lui est donné, il ne le choisit pas.
 */
final class ResultatDeReservation
{
    public const MOTIF_PLACES_INSUFFISANTES = 'PLACES_INSUFFISANTES';
    public const MOTIF_COORDONNEES_INVALIDES = 'COORDONNEES_INVALIDES';
    public const MOTIF_BATEAU_DEJA_ENGAGE = 'BATEAU_DEJA_ENGAGE';
    public const MOTIF_CRENEAU_ANNULE = 'CRENEAU_ANNULE';

    private function __construct(
        private readonly ?string $referenceDeReservation,
        private readonly ?StatutDeReservation $statut,
        private readonly ?DateTimeImmutable $immobiliseeJusquA,
        private readonly ?string $motifDuRefus,
        private readonly ?string $champEnCause,
    ) {
    }

    public static function acceptee(
        string $referenceDeReservation,
        StatutDeReservation $statut,
        ?DateTimeImmutable $immobiliseeJusquA = null,
    ): self {
        return new self($referenceDeReservation, $statut, $immobiliseeJusquA, null, null);
    }

    /**
     * @param string|null $champEnCause le champ de formulaire fautif, quand le
     *                                  refus en désigne un : un refus doit
     *                                  nommer le champ, pas produire un message
     *                                  générique (SPEC-BOOKING-01 AC-7)
     */
    public static function refusee(string $motif, ?string $champEnCause = null): self
    {
        return new self(null, null, null, $motif, $champEnCause);
    }

    public function estAcceptee(): bool
    {
        return $this->motifDuRefus === null;
    }

    public function estRefusee(): bool
    {
        return $this->motifDuRefus !== null;
    }

    public function motifDuRefus(): ?string
    {
        return $this->motifDuRefus;
    }

    public function champEnCause(): ?string
    {
        return $this->champEnCause;
    }

    public function referenceDeReservation(): ?string
    {
        return $this->referenceDeReservation;
    }

    /** L'échéance de l'immobilisation des places, cf. ADR-003. */
    public function immobiliseeJusquA(): ?DateTimeImmutable
    {
        return $this->immobiliseeJusquA;
    }

    public function statut(): ?StatutDeReservation
    {
        return $this->statut;
    }
}
