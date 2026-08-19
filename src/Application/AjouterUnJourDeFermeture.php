<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\JourDeFermeture;
use App\Domaine\Entite\Reservation;
use App\Domaine\FuseauDexploitation;
use App\Domaine\ResultatDeFermeture;
use App\Domaine\StatutDeReservation;
use App\Infrastructure\Persistance\CalendrierRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;

/**
 * Ajouter un jour de fermeture (SPEC-ADMIN-04).
 *
 * **Fermer une date déjà réservée est accepté, et n'annule ni ne rembourse
 * rien.** Les réservations concernées sont listées au gérant, à lui de traiter
 * ces clients. C'est l'effet de bord relevé dans l'analyse d'impact, que le
 * client n'avait pas envisagé : le geste paraît anodin et ne l'est pas.
 *
 * L'ajout prend effet le jour même de l'enregistrement, sans intervention
 * technique.
 */
final class AjouterUnJourDeFermeture
{
    public function __construct(
        private readonly CalendrierRepository $calendrier,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
    ) {
    }

    public function executer(string $jour, bool $recurrentAnnuel = false): ResultatDeFermeture
    {
        $concernees = $this->reservationsDeLaJournee($jour);

        if ($this->calendrier->parDate($jour) === null) {
            $this->calendrier->ajouter(new JourDeFermeture(
                FuseauDexploitation::instant($jour),
                $recurrentAnnuel,
            ));
        }

        return new ResultatDeFermeture(true, $concernees);
    }

    /** @return list<string> les références des réservations encore actives */
    private function reservationsDeLaJournee(string $jour): array
    {
        $references = [];

        foreach ($this->sorties->sortiesDuJour($jour) as $sortie) {
            foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
                if ($reservation->statut() !== StatutDeReservation::ANNULEE) {
                    $references[] = $reservation->reference();
                }
            }
        }

        return $references;
    }
}
