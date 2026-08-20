<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Quand le solde d'une réservation est réglable en ligne (SPEC-BOOKING-12).
 *
 * **La fenêtre s'ouvre avec le lien de paiement**, envoyé à 7h du matin la
 * veille de la sortie, et non 24 heures avant le départ (`CR-07/Q12`). Sans ce
 * repère, un client réservant un départ de 14h recevrait à 7h un lien inactif
 * pendant sept heures.
 *
 * Le repère de fermeture n'est pas recalculé ici : c'est celui de
 * `FermetureDesReservations`, et deux règles qui diraient la même chose
 * finiraient par diverger.
 *
 * Une réservation prise **après** 7h la veille trouve la fenêtre déjà ouverte,
 * ce qui satisfait `CR-07/Q02` sans qu'aucun cas particulier ne soit écrit.
 */
final class FenetreDeReglement
{
    public const HEURE_DENVOI_DU_LIEN = 7;

    public function __construct(
        private readonly FermetureDesReservations $fermeture = new FermetureDesReservations(),
    ) {
    }

    public function estOuverte(DateTimeImmutable $depart, DateTimeImmutable $maintenant): bool
    {
        return $maintenant >= $this->ouverture($depart)
            && $maintenant < $this->fermeture->fermetureDe($depart);
    }

    /** L'instant où part le lien, et où la fenêtre s'ouvre. */
    public function ouverture(DateTimeImmutable $depart): DateTimeImmutable
    {
        return $depart->modify('-1 day')->setTime(self::HEURE_DENVOI_DU_LIEN, 0);
    }
}
