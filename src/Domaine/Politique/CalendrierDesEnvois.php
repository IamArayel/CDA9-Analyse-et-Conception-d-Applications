<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Quand partent les trois messages automatiques (SPEC-CANCEL-05 et 06).
 *
 * Règle pure : trois instants calculés à partir de l'heure de départ et des
 * réglages du gérant. Les réglages sont **lus au moment de l'envoi**, jamais
 * figés à la mise en alerte ni à la confirmation d'une réservation, ce qui est
 * exactement ce que vérifient les deux cas sur les horaires modifiables.
 *
 * Un instant déjà passé ne veut pas dire « jamais » : l'appelant envoie alors
 * immédiatement, qu'il s'agisse d'une alerte posée à 21h ou d'une réservation
 * prise après l'heure de rappel.
 */
final class CalendrierDesEnvois
{
    /** La veille du départ, à l'heure réglée par le gérant. */
    public function alerte(DateTimeImmutable $depart, string $heureDalerte): DateTimeImmutable
    {
        [$heures, $minutes] = array_map('intval', explode(':', $heureDalerte));

        return $depart->modify('-1 day')->setTime($heures, $minutes);
    }

    /** Un délai avant le départ, deux heures par défaut. */
    public function confirmationDannulation(
        DateTimeImmutable $depart,
        int $delaiEnHeures,
    ): DateTimeImmutable {
        return $depart->modify(sprintf('-%d hours', $delaiEnHeures));
    }

    /** Un délai avant le départ, vingt-quatre heures par défaut. */
    public function rappel(DateTimeImmutable $depart, int $delaiEnHeures): DateTimeImmutable
    {
        return $depart->modify(sprintf('-%d hours', $delaiEnHeures));
    }
}
