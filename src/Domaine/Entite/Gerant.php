<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

/**
 * Le compte unique de l'espace de gestion (SPEC-ADMIN-01).
 *
 * Un seul compte existe : aucun écran de création d'un second n'est prévu.
 * `motDePasse` porte un condensat, jamais le mot de passe en clair.
 */
class Gerant
{
    private ?int $id = null;

    public function __construct(
        private string $email,
        private string $motDePasse,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    /** Le condensat, jamais le mot de passe en clair. */
    public function motDePasse(): string
    {
        return $this->motDePasse;
    }
}
