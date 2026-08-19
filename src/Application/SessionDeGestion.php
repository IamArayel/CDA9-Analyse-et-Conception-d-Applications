<?php

declare(strict_types=1);

namespace App\Application;

/**
 * Les sessions ouvertes sur l'espace de gestion.
 *
 * **Volontairement minimal.** En production, l'ouverture et la vérification
 * d'une session relèvent du pare-feu de sécurité du framework, pas d'un service
 * applicatif : ce qui est écrit ici ne couvre que ce que la spécification
 * demande, à savoir qu'une page de gestion demandée sans session ouverte
 * n'affiche aucune donnée (SPEC-ADMIN-01 AC-4).
 *
 * Le jeton est opaque et tiré au hasard : rien ne s'en déduit.
 */
final class SessionDeGestion
{
    /** @var array<string, true> */
    private array $ouvertes = [];

    public function ouvrir(): string
    {
        $jeton = bin2hex(random_bytes(16));
        $this->ouvertes[$jeton] = true;

        return $jeton;
    }

    public function estOuverte(?string $jeton): bool
    {
        return $jeton !== null && isset($this->ouvertes[$jeton]);
    }

    public function fermer(string $jeton): void
    {
        unset($this->ouvertes[$jeton]);
    }
}
