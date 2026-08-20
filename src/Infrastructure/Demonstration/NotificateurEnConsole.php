<?php

declare(strict_types=1);

namespace App\Infrastructure\Demonstration;

use App\Domaine\Notificateur;
use DateTimeImmutable;

/**
 * Un notificateur qui retient les envois au lieu de les faire partir.
 *
 * La démonstration doit pouvoir dire « le lien de règlement est parti, par
 * courriel, à telle heure » sans écrire à qui que ce soit. Aucun message ne sort
 * de la machine.
 *
 * **Câblé dans le seul environnement `demo`.** En production, l'adaptateur reste
 * `NotificateurNonConfigure`, qui échoue bruyamment tant qu'ADR-004 n'est pas
 * intégrée : un envoi silencieusement perdu coûte plus cher qu'une erreur.
 */
final class NotificateurEnConsole implements Notificateur
{
    /** @var list<array{type: string, canal: string, destinataire: string, envoyeLe: DateTimeImmutable}> */
    private array $envois = [];

    public function envoyer(
        string $referenceDeReservation,
        string $type,
        string $canal,
        string $destinataire,
        DateTimeImmutable $envoyeLe,
        array $donnees = [],
    ): bool {
        $this->envois[] = [
            'type' => $type,
            'canal' => $canal,
            'destinataire' => $destinataire,
            'envoyeLe' => $envoyeLe,
        ];

        return true;
    }

    /** @return list<array{type: string, canal: string, destinataire: string, envoyeLe: DateTimeImmutable}> */
    public function envois(?string $type = null): array
    {
        if ($type === null) {
            return $this->envois;
        }

        return array_values(array_filter(
            $this->envois,
            static fn (array $envoi): bool => $envoi['type'] === $type,
        ));
    }
}
