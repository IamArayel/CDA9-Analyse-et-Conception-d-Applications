<?php

declare(strict_types=1);

namespace App\Tests\Doublures;

use App\Domaine\PrestataireDePaiement;

/**
 * Le prestataire de paiement des tests : il n'encaisse ni ne rembourse rien,
 * il enregistre ce qui lui a été demandé.
 *
 * Nous testons nos réactions à ses réponses, pas son fonctionnement, cf.
 * docs/strategie-de-test.md §4.
 */
final class PaiementSimule implements PrestataireDePaiement
{
    /** Le prestataire accepte, sauf si le cas de test lui demande de refuser. */
    private bool $refuseraLaProchaine = false;

    /** @var list<array{reservation: string, montant: int}> */
    private array $encaissements = [];

    /** @var list<array{reservation: string, montant: int}> */
    private array $remboursements = [];

    /**
     * Nous testons nos réactions à ses réponses, pas son fonctionnement : le
     * cas de test choisit la réponse, et nous observons ce que nous en faisons.
     */
    public function refuseraLaProchaineTransaction(): void
    {
        $this->refuseraLaProchaine = true;
    }

    public function encaisser(string $referenceDeReservation, int $montantEnCentimes): bool
    {
        if ($this->refuseraLaProchaine) {
            $this->refuseraLaProchaine = false;

            return false;
        }

        $this->encaissements[] = [
            'reservation' => $referenceDeReservation,
            'montant' => $montantEnCentimes,
        ];

        return true;
    }

    public function rembourser(string $referenceDeReservation, int $montantEnCentimes): void
    {
        $this->remboursements[] = [
            'reservation' => $referenceDeReservation,
            'montant' => $montantEnCentimes,
        ];
    }

    /** @return list<array{reservation: string, montant: int}> */
    public function encaissementsDemandes(): array
    {
        return $this->encaissements;
    }

    /** Vrai si aucune carte n'a été débitée, quel qu'en soit le porteur. */
    public function aucunEncaissementDemande(): bool
    {
        return $this->encaissements === [];
    }

    /** Vrai si ce client n'a jamais été débité. */
    public function aEteDebite(string $referenceDeReservation): bool
    {
        foreach ($this->encaissements as $encaissement) {
            if ($encaissement['reservation'] === $referenceDeReservation) {
                return true;
            }
        }

        return false;
    }

    /** @return list<array{reservation: string, montant: int}> */
    public function remboursementsDemandes(): array
    {
        return $this->remboursements;
    }

    public function nombreDencaissements(): int
    {
        return count($this->encaissements);
    }

    /**
     * Le **premier** montant encaissé pour une réservation, en centimes.
     *
     * Depuis `REQ-108` une réservation porte deux transactions : cette méthode
     * rend celle de l'acompte. Le solde se lit avec `dernierMontantEncaisse()`.
     * Tant qu'il n'y en avait qu'une, la distinction ne se posait pas.
     */
    public function montantEncaisse(string $referenceDeReservation): ?int
    {
        foreach ($this->encaissements as $encaissement) {
            if ($encaissement['reservation'] === $referenceDeReservation) {
                return $encaissement['montant'];
            }
        }

        return null;
    }

    /** Le **dernier** montant encaissé, en centimes : le solde, s'il a été réglé. */
    public function dernierMontantEncaisse(string $referenceDeReservation): ?int
    {
        foreach (array_reverse($this->encaissements) as $encaissement) {
            if ($encaissement['reservation'] === $referenceDeReservation) {
                return $encaissement['montant'];
            }
        }

        return null;
    }

    public function nombreDeRemboursements(): int
    {
        return count($this->remboursements);
    }

    public function aucunRemboursementDemande(): bool
    {
        return $this->remboursements === [];
    }

    /** Le montant remboursé à une réservation, en centimes, ou null si aucun. */
    public function montantRembourse(string $referenceDeReservation): ?int
    {
        foreach ($this->remboursements as $remboursement) {
            if ($remboursement['reservation'] === $referenceDeReservation) {
                return $remboursement['montant'];
            }
        }

        return null;
    }
}
