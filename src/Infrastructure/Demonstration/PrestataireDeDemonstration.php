<?php

declare(strict_types=1);

namespace App\Infrastructure\Demonstration;

use App\Domaine\PrestataireDePaiement;

/**
 * Un prestataire de paiement qui accepte tout et retient les montants.
 *
 * Il permet de montrer que l'acompte et le solde sont **deux transactions
 * distinctes** (`REQ-117`) en affichant les deux encaissements, ce qu'aucune
 * capture d'écran ne dirait aussi clairement.
 *
 * **Câblé dans le seul environnement `demo`.** Il ne doit jamais l'être
 * ailleurs : un prestataire qui répond toujours oui est exactement ce qu'un
 * système de paiement ne doit pas faire.
 */
final class PrestataireDeDemonstration implements PrestataireDePaiement
{
    /** @var list<array{sens: string, reservation: string, montant: int}> */
    private array $transactions = [];

    public function encaisser(string $referenceDeReservation, int $montantEnCentimes): bool
    {
        $this->transactions[] = [
            'sens' => 'encaissement',
            'reservation' => $referenceDeReservation,
            'montant' => $montantEnCentimes,
        ];

        return true;
    }

    public function rembourser(string $referenceDeReservation, int $montantEnCentimes): void
    {
        $this->transactions[] = [
            'sens' => 'remboursement',
            'reservation' => $referenceDeReservation,
            'montant' => $montantEnCentimes,
        ];
    }

    /** @return list<array{sens: string, reservation: string, montant: int}> */
    public function transactions(): array
    {
        return $this->transactions;
    }
}
