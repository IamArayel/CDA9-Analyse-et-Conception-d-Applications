<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Reservation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La trace des envois (SPEC-CANCEL-04 AC-6).
 *
 * Elle sert deux choses à la fois : répondre à un client affirmant n'avoir rien
 * reçu, et **empêcher qu'un envoi déjà parti ne soit rejoué**. La tâche
 * programmée passe plusieurs fois ; sans cette trace, chaque passage renverrait
 * les mêmes messages.
 */
final class NotificationRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function dejaEnvoye(Reservation $reservation, string $type): bool
    {
        return $this->entites->getRepository(Notification::class)
            ->count(['reservation' => $reservation, 'type' => $type]) > 0;
    }

    public function tracer(Notification $notification): void
    {
        $this->entites->persist($notification);
        $this->entites->flush();
    }
}
