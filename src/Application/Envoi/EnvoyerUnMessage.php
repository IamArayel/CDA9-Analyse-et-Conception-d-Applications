<?php

declare(strict_types=1);

namespace App\Application\Envoi;

use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Reservation;
use App\Domaine\Notificateur;
use App\Infrastructure\Persistance\NotificationRepository;
use DateTimeImmutable;

/**
 * Envoyer un message à un client, sur les deux canaux, et le tracer.
 *
 * Trois règles tiennent ici, et elles valent pour les trois types de message :
 *
 * - **les deux canaux systématiquement** (REQ-057), depuis que le gérant ne
 *   téléphone plus ;
 * - **l'échec d'un canal n'emporte pas l'autre**, et il est enregistré comme
 *   tel (SPEC-CANCEL-05 AC-6) ;
 * - **un message déjà parti n'est pas rejoué**, la tâche programmée passant
 *   plusieurs fois.
 *
 * Le destinataire tracé est l'adresse e-mail sur les deux canaux : elle
 * identifie le client, là où le numéro de mobile n'est qu'un moyen d'atteinte.
 */
final class EnvoyerUnMessage
{
    private const CANAUX = [Notification::CANAL_EMAIL, Notification::CANAL_SMS];

    public function __construct(
        private readonly Notificateur $notificateur,
        private readonly NotificationRepository $traces,
    ) {
    }

    /** @param array<string, string> $donnees */
    public function pour(
        Reservation $reservation,
        string $type,
        DateTimeImmutable $quand,
        array $donnees = [],
    ): void {
        if ($this->traces->dejaEnvoye($reservation, $type)) {
            return;
        }

        $donnees['langue'] = $reservation->langue();

        foreach (self::CANAUX as $canal) {
            $reussi = $this->notificateur->envoyer(
                $reservation->reference(),
                $type,
                $canal,
                $reservation->email(),
                $quand,
                $donnees,
            );

            $this->traces->tracer(new Notification(
                $reservation,
                $type,
                $canal,
                $quand,
                $reussi ? Notification::STATUT_ENVOYE : Notification::STATUT_ECHEC,
            ));
        }
    }
}
