<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend une tentative de connexion à l'espace de gestion.
 *
 * **Le message d'erreur est unique**, qu'il s'agisse d'une adresse inconnue ou
 * d'un mot de passe erroné : distinguer les deux permettrait de savoir quelles
 * adresses existent (SPEC-ADMIN-01 AC-4). C'est pourquoi le refus ne transporte
 * aucun motif, contrairement aux autres résultats du domaine.
 */
final class ResultatDeConnexion
{
    public const MESSAGE_DERREUR = 'Identifiants incorrects.';

    private function __construct(private readonly ?string $session)
    {
    }

    public static function acceptee(string $session): self
    {
        return new self($session);
    }

    public static function refusee(): self
    {
        return new self(null);
    }

    public function estAcceptee(): bool
    {
        return $this->session !== null;
    }

    public function estRefusee(): bool
    {
        return $this->session === null;
    }

    public function session(): ?string
    {
        return $this->session;
    }

    public function messageDerreur(): ?string
    {
        return $this->session === null ? self::MESSAGE_DERREUR : null;
    }
}
