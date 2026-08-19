<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Quand un créneau cesse d'être réservable (SPEC-BOOKING-04, REQ-005).
 *
 * La fermeture tombe **à midi**, et la seule question est de quel jour : le jour
 * même si midi laisse encore les deux heures d'avance exigées, la veille sinon.
 * Écrite ainsi, la règle donne midi le jour même pour le créneau de 14h et midi
 * la veille pour ceux de 7h et de 10h, sans énumérer les trois créneaux.
 *
 * La fermeture est effective **à partir de** 12h00, pas après : 11h59 accepte,
 * 12h00 refuse.
 *
 * L'heure de référence est l'heure locale du lieu d'exploitation, portée par
 * les instants qu'on lui passe.
 */
final class FermetureDesReservations
{
    public const HEURE_DE_FERMETURE = 12;
    public const PREAVIS_EN_HEURES = 2;

    public function estReservable(
        DateTimeImmutable $departPrevu,
        DateTimeImmutable $maintenant,
    ): bool {
        return $maintenant < $this->fermetureDe($departPrevu);
    }

    public function fermetureDe(DateTimeImmutable $departPrevu): DateTimeImmutable
    {
        $midiDuJourDeDepart = $departPrevu->setTime(self::HEURE_DE_FERMETURE, 0);
        $dernierInstantUtile = $departPrevu->modify(sprintf('-%d hours', self::PREAVIS_EN_HEURES));

        return $midiDuJourDeDepart <= $dernierInstantUtile
            ? $midiDuJourDeDepart
            : $midiDuJourDeDepart->modify('-1 day');
    }
}
