<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\ChoixAnnulation;
use App\Domaine\Entite\Reservation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * L'issue retenue pour une annulation demandée par le client (SPEC-ADMIN-06).
 *
 * Une réservation n'en porte qu'une : l'unicité en base le garantit, cette
 * lecture ne fait que produire un refus lisible avant d'y arriver.
 */
final class ChoixAnnulationRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function pourLaReservation(Reservation $reservation): ?ChoixAnnulation
    {
        return $this->entites->getRepository(ChoixAnnulation::class)
            ->findOneBy(['reservation' => $reservation]);
    }

    public function enregistrer(ChoixAnnulation $choix): void
    {
        $this->entites->persist($choix);
        $this->entites->flush();
    }
}
