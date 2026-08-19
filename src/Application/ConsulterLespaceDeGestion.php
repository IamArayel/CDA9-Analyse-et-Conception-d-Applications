<?php

declare(strict_types=1);

namespace App\Application;

/**
 * Les sections accessibles de l'espace de gestion (SPEC-ADMIN-01 AC-1 et AC-4).
 *
 * Sans session ouverte, la liste est vide : **l'accès direct par URL ne
 * contourne rien**. Avec session, les quatre sections que le client a
 * demandées, et elles seules.
 */
final class ConsulterLespaceDeGestion
{
    public const SECTIONS = ['tarifs', 'planning', 'horaires', 'flotte'];

    public function __construct(private readonly SessionDeGestion $sessions)
    {
    }

    /** @return list<string> */
    public function sections(?string $session): array
    {
        return $this->sessions->estOuverte($session) ? self::SECTIONS : [];
    }
}
