<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * L'envoi d'un message à un client, vu par le domaine.
 *
 * Un envoi laisse toujours une trace portant son type, son canal, son
 * destinataire et sa date : c'est une exigence métier (SPEC-CANCEL-04 AC-6),
 * pas un confort d'exploitation. Depuis que le gérant ne téléphone plus, un
 * message perdu n'est rattrapé par personne, et la trace est ce qui permet de
 * répondre à un client affirmant n'avoir rien reçu.
 *
 * La valeur de retour dit si l'envoi a abouti sur ce canal. Un échec n'emporte
 * pas l'autre canal.
 */
interface Notificateur
{
    /**
     * @param array<string, string> $donnees les données variables du message,
     *                                       la langue et la prévision météo par
     *                                       exemple
     */
    public function envoyer(
        string $referenceDeReservation,
        string $type,
        string $canal,
        string $destinataire,
        DateTimeImmutable $envoyeLe,
        array $donnees = [],
    ): bool;
}
