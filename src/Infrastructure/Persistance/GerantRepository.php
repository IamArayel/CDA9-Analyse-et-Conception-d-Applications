<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Gerant;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Le compte unique de l'espace de gestion (SPEC-ADMIN-01).
 *
 * Aucune méthode de création : il n'existe qu'un compte, et aucun écran ne
 * permet d'en ajouter un second.
 */
final class GerantRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function parEmail(string $email): ?Gerant
    {
        return $this->entites->getRepository(Gerant::class)->findOneBy(['email' => $email]);
    }
}
