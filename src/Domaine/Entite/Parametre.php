<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

/**
 * Les réglages de l'espace de gestion.
 *
 * Ligne unique. Les trois délais d'envoi sont **lus au moment de l'envoi**, pas
 * figés à la mise en alerte ni à la confirmation d'une réservation : un
 * changement s'applique donc aux envois à venir (SPEC-CANCEL-05 AC-3 et
 * SPEC-CANCEL-06 AC-9).
 */
class Parametre
{
    public const HEURE_DALERTE_PAR_DEFAUT = '18:00';
    public const DELAI_DE_CONFIRMATION_PAR_DEFAUT = 2;
    public const DELAI_DE_RAPPEL_PAR_DEFAUT = 24;

    private ?int $id = null;

    public function __construct(
        private string $heureDouverture = '07:00',
        private string $heureDeFermeture = '18:00',
        private int $delaiDeRappelEnHeures = self::DELAI_DE_RAPPEL_PAR_DEFAUT,
        private string $heureDalerte = self::HEURE_DALERTE_PAR_DEFAUT,
        private int $delaiDeConfirmationEnHeures = self::DELAI_DE_CONFIRMATION_PAR_DEFAUT,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function heureDouverture(): string
    {
        return $this->heureDouverture;
    }

    public function heureDeFermeture(): string
    {
        return $this->heureDeFermeture;
    }

    public function delaiDeRappelEnHeures(): int
    {
        return $this->delaiDeRappelEnHeures;
    }

    public function heureDalerte(): string
    {
        return $this->heureDalerte;
    }

    public function delaiDeConfirmationEnHeures(): int
    {
        return $this->delaiDeConfirmationEnHeures;
    }

    /** Chaque réglage n'est modifié que s'il est fourni. */
    public function regler(
        ?string $heureDalerte = null,
        ?int $delaiDeConfirmationEnHeures = null,
        ?int $delaiDeRappelEnHeures = null,
    ): void {
        $this->heureDalerte = $heureDalerte ?? $this->heureDalerte;
        $this->delaiDeConfirmationEnHeures = $delaiDeConfirmationEnHeures
            ?? $this->delaiDeConfirmationEnHeures;
        $this->delaiDeRappelEnHeures = $delaiDeRappelEnHeures ?? $this->delaiDeRappelEnHeures;
    }
}
