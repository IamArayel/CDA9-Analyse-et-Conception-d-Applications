<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Paiement;
use App\Domaine\Entite\Reservation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Les versements d'une réservation.
 *
 * **Le montant versé se calcule, il ne se lit pas.** C'est la somme des
 * paiements non annulés, ce qui permet à un pointage rétracté de disparaître du
 * total sans disparaître de l'historique (`REQ-113`).
 */
final class PaiementRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    /**
     * Tous les versements, annulés compris, dans l'ordre où ils ont eu lieu.
     *
     * @return list<Paiement>
     */
    public function pour(Reservation $reservation): array
    {
        return $this->entites->getRepository(Paiement::class)
            ->findBy(['reservation' => $reservation], ['id' => 'ASC']);
    }

    /** En centimes : la somme des versements qui comptent encore. */
    public function verse(Reservation $reservation): int
    {
        $verse = 0;

        foreach ($this->pour($reservation) as $paiement) {
            if ($paiement->compteDansLeVerse()) {
                $verse += $paiement->montant();
            }
        }

        return $verse;
    }

    /** Le dernier versement de solde encore actif, s'il y en a un. */
    public function soldeActif(Reservation $reservation): ?Paiement
    {
        foreach (array_reverse($this->pour($reservation)) as $paiement) {
            if ($paiement->type() === Paiement::TYPE_SOLDE && $paiement->compteDansLeVerse()) {
                return $paiement;
            }
        }

        return null;
    }

    public function enregistrer(Paiement $paiement): void
    {
        $this->entites->persist($paiement);
        $this->entites->flush();
    }
}
