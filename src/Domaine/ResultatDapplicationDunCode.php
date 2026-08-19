<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend la saisie d'un bon cadeau ou d'un code d'avoir sur une
 * réservation.
 *
 * `MOTIF_CODE_INVALIDE` couvre indifféremment un code inexistant, déjà utilisé
 * ou expiré. **C'est volontaire :** distinguer les cas permettrait de sonder
 * les codes en observant la réponse (SPEC-BOOKING-09 AC-5).
 *
 * Le montant restant dû est porté dans les deux cas, y compris sur un refus,
 * où il vaut le montant inchangé.
 */
final class ResultatDapplicationDunCode
{
    public const MOTIF_CODE_INVALIDE = 'CODE_INVALIDE';
    public const MOTIF_CODES_NON_CUMULABLES = 'CODES_NON_CUMULABLES';

    private function __construct(
        private readonly int $montantRestantDu,
        private readonly ?string $motifDuRefus,
    ) {
    }

    public static function accepte(int $montantRestantDu): self
    {
        return new self($montantRestantDu, null);
    }

    public static function refuse(string $motif, int $montantRestantDu): self
    {
        return new self($montantRestantDu, $motif);
    }

    public function estAccepte(): bool
    {
        return $this->motifDuRefus === null;
    }

    public function estRefuse(): bool
    {
        return $this->motifDuRefus !== null;
    }

    public function motifDuRefus(): ?string
    {
        return $this->motifDuRefus;
    }

    /** En centimes. */
    public function montantRestantDu(): int
    {
        return $this->montantRestantDu;
    }
}
