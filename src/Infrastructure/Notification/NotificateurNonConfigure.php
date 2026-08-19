<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domaine\Notificateur;
use DateTimeImmutable;
use LogicException;

/**
 * L'adaptateur d'envoi tant que la plateforme n'est pas ouverte.
 *
 * `ADR-004` retient une plateforme française multicanal et pressent Brevo, mais
 * le compte reste suspendu à trois vérifications qui ne se font pas depuis le
 * dépôt. En attendant, le port est lié à cet adaptateur, qui **échoue
 * bruyamment** : un envoi silencieusement perdu serait bien pire, puisque
 * depuis REQ-026 le gérant ne rattrape plus rien par téléphone.
 *
 * En test, `EnvoisEnregistres` le remplace et note ce qui aurait été envoyé.
 */
final class NotificateurNonConfigure implements Notificateur
{
    public function envoyer(
        string $referenceDeReservation,
        string $type,
        string $canal,
        string $destinataire,
        DateTimeImmutable $envoyeLe,
        array $donnees = [],
    ): bool {
        throw new LogicException(
            'Aucune plateforme d\'envoi n\'est configurée : voir ADR-004, dont '
            .'les trois vérifications d\'ouverture de compte restent à faire.'
        );
    }
}
