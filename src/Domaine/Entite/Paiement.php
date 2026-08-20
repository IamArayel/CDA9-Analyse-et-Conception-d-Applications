<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use DateTimeImmutable;

/**
 * Un versement rattaché à une réservation : l'acompte, ou le solde.
 *
 * **Une table et non trois colonnes sur `reservation`.** Trois colonnes
 * diraient où en est une réservation, pas comment on y est arrivé, et deux
 * exigences le demandent : `REQ-117`, qui fait de l'acompte et du solde deux
 * transactions distinctes, et `REQ-113`, qui exige qu'un pointage réversible
 * laisse une trace. Un pointage posé puis annulé puis reposé se lit dans une
 * table, il s'écrase dans une colonne.
 *
 * Un paiement `SUR_PLACE` n'a **jamais** de contrepartie chez le prestataire :
 * le gérant encaisse sur son terminal, l'outil enregistre le fait.
 *
 * `annuler()` marque la ligne sans la supprimer : le montant versé d'une
 * réservation est la somme de ses paiements non annulés, et l'historique
 * survit.
 */
class Paiement
{
    public const TYPE_ACOMPTE = 'ACOMPTE';
    public const TYPE_SOLDE = 'SOLDE';

    public const CANAL_EN_LIGNE = 'EN_LIGNE';
    public const CANAL_SUR_PLACE = 'SUR_PLACE';

    private ?int $id = null;
    private bool $annule = false;

    /** @param int $montant en centimes */
    public function __construct(
        private Reservation $reservation,
        private string $type,
        private int $montant,
        private string $canal,
        private DateTimeImmutable $datePaiement,
        private ?string $pointePar = null,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function reservation(): Reservation
    {
        return $this->reservation;
    }

    public function type(): string
    {
        return $this->type;
    }

    /** En centimes. */
    public function montant(): int
    {
        return $this->montant;
    }

    public function canal(): string
    {
        return $this->canal;
    }

    public function datePaiement(): DateTimeImmutable
    {
        return $this->datePaiement;
    }

    /** Renseigné pour un solde encaissé au quai, cf. SPEC-ADMIN-07. */
    public function pointePar(): ?string
    {
        return $this->pointePar;
    }

    public function estAnnule(): bool
    {
        return $this->annule;
    }

    /** Marque la ligne annulée sans la supprimer : l'historique survit. */
    public function annuler(): void
    {
        $this->annule = true;
    }

    public function compteDansLeVerse(): bool
    {
        return !$this->annule;
    }
}
