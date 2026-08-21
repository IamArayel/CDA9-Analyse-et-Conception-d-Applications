<?php

declare(strict_types=1);

namespace App\Infrastructure\Securite;

use App\Infrastructure\Persistance\GerantRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Le fournisseur d'utilisateur du pare-feu `gestion`, adossé au compte unique
 * du gérant (SPEC-ADMIN-01).
 */
final class GerantUserProvider implements UserProviderInterface
{
    public function __construct(private readonly GerantRepository $gerants)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $gerant = $this->gerants->parEmail($identifier);

        if ($gerant === null) {
            throw new UserNotFoundException(sprintf('Aucun gérant « %s ».', $identifier));
        }

        return new GerantUtilisateur($gerant);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof GerantUtilisateur) {
            throw new UnsupportedUserException(sprintf('Utilisateur inattendu « %s ».', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return GerantUtilisateur::class === $class;
    }
}
