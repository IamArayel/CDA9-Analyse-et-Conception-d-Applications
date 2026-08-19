<?php

declare(strict_types=1);

namespace App\Application;

/**
 * L'état du parcours de réservation côté client (SPEC-BOOKING-11).
 *
 * **Aucune détection automatique de la langue.** Le français s'applique tant que
 * le client n'a rien choisi, quelle que soit la configuration de son navigateur.
 * C'est une décision, pas un oubli : deviner la langue à partir du navigateur
 * donne un résultat faux pour un touriste francophone en voyage.
 *
 * Et les champs déjà saisis survivent au changement de langue, sans quoi le
 * client recommence son formulaire.
 */
final class ParcoursDeReservation
{
    public const LANGUE_PAR_DEFAUT = 'fr';

    private string $langue = self::LANGUE_PAR_DEFAUT;

    /** @var array<string, string> */
    private array $champsSaisis = [];

    public function demarrer(string $langueDuNavigateur): void
    {
        $this->langue = self::LANGUE_PAR_DEFAUT;
        $this->champsSaisis = [];
    }

    public function langue(): string
    {
        return $this->langue;
    }

    /** @param array<string, string> $champs */
    public function renseigner(array $champs): void
    {
        $this->champsSaisis = [...$this->champsSaisis, ...$champs];
    }

    public function basculerEn(string $langue): void
    {
        $this->langue = $langue;
    }

    /** @return array<string, string> */
    public function champsSaisis(): array
    {
        return $this->champsSaisis;
    }
}
