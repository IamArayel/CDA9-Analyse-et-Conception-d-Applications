<?php

declare(strict_types=1);

namespace App\Application\Envoi;

use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Reservation;
use App\Domaine\Politique\CalendrierDesEnvois;
use App\Infrastructure\Persistance\ParametreRepository;
use App\Infrastructure\Persistance\PrevisionMeteoRepository;
use DateTimeImmutable;

/**
 * Le message de rappel avant la sortie (SPEC-CANCEL-05).
 *
 * Il part de deux endroits, et c'est voulu : la tâche programmée l'envoie à
 * l'heure dite, et la confirmation d'une réservation l'envoie immédiatement si
 * cette heure est déjà passée. **Sans le second chemin, tout client réservant
 * la veille au matin pour un départ à 7h n'aurait jamais de rappel**, ce qui
 * est fréquent puisque les réservations restent ouvertes jusqu'à midi la
 * veille (AC-5).
 *
 * Un créneau annulé ne rappelle rien : ses clients ont déjà reçu leur message
 * d'annulation.
 */
final class RappelDeSortie
{
    public function __construct(
        private readonly EnvoyerUnMessage $envoi,
        private readonly CalendrierDesEnvois $calendrier,
        private readonly ParametreRepository $parametres,
        private readonly PrevisionMeteoRepository $previsions,
    ) {
    }

    public function envoyerSiDu(Reservation $reservation, DateTimeImmutable $maintenant): void
    {
        $sortie = $reservation->sortie();

        if ($sortie->estAnnulee() || !$reservation->estConfirmee()) {
            return;
        }

        $depart = $sortie->creneau()->departPrevu();
        $heureDuRappel = $this->calendrier->rappel(
            $depart,
            $this->parametres->reglages()->delaiDeRappelEnHeures(),
        );

        if ($maintenant < $heureDuRappel) {
            return;
        }

        $this->envoi->pour(
            $reservation,
            Notification::TYPE_RAPPEL,
            $maintenant,
            $this->previsionDuJour($depart),
        );
    }

    /** @return array<string, string> */
    private function previsionDuJour(DateTimeImmutable $depart): array
    {
        $prevision = $this->previsions->pourLeJour($depart);

        return $prevision === null ? [] : ['prevision_meteo' => $prevision->texte()];
    }
}
