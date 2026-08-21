<?php

declare(strict_types=1);

namespace App\Infrastructure\Securite;

use App\Domaine\Entite\Gerant;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * L'adaptateur entre le compte du gérant (`Domaine\Entite\Gerant`) et la
 * sécurité Symfony.
 *
 * Le domaine ne connaît pas Symfony (architecture.md §2) : cet adaptateur vit
 * en Infrastructure et porte seul la dépendance à `UserInterface`.
 */
final class GerantUtilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private readonly Gerant $gerant)
    {
    }

    public function gerant(): Gerant
    {
        return $this->gerant;
    }

    public function getRoles(): array
    {
        return ['ROLE_GERANT'];
    }

    public function getPassword(): string
    {
        return $this->gerant->motDePasse();
    }

    public function getUserIdentifier(): string
    {
        return $this->gerant->email();
    }

    public function eraseCredentials(): void
    {
    }
}
