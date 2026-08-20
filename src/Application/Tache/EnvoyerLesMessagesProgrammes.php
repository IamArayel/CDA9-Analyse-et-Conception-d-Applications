<?php

declare(strict_types=1);

namespace App\Application\Tache;

use App\Application\Envoi\EnvoyerUnMessage;
use App\Application\Envoi\LienDeReglement;
use App\Application\Envoi\RappelDeSortie;
use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\CalendrierDesEnvois;
use App\Domaine\StatutDeReservation;
use App\Domaine\StatutDeSortie;
use App\Infrastructure\Persistance\ParametreRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use DateTimeImmutable;

/**
 * La tâche qui envoie les quatre messages automatiques (SPEC-CANCEL-05, 06
 * et 07).
 *
 * Elle est le seul traitement qui se déclenche sans utilisateur devant
 * l'écran : c'est pour elle que l'horloge est injectée plutôt que lue
 * (ADR-005, option B).
 *
 * L'ordre des branches porte deux règles :
 *
 * - **une sortie annulée n'envoie que sa confirmation**, jamais de rappel : ses
 *   clients ont déjà reçu leur message d'annulation ;
 * - **une sortie maintenue n'envoie aucun second message**. Le silence vaut
 *   maintien, et c'est la règle que le client a posée deux fois.
 *
 * Les réglages sont relus à chaque passage : un horaire modifié s'applique donc
 * aux envois à venir sans qu'aucune alerte ni réservation n'ait à être reprise.
 */
final class EnvoyerLesMessagesProgrammes
{
    private const HORIZON = '+7 days';

    public function __construct(
        private readonly Horloge $horloge,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly EnvoyerUnMessage $envoi,
        private readonly RappelDeSortie $rappel,
        private readonly LienDeReglement $lien,
        private readonly CalendrierDesEnvois $calendrier,
        private readonly ParametreRepository $parametres,
    ) {
    }

    public function executer(): void
    {
        $maintenant = $this->horloge->maintenant();

        $sorties = $this->sorties->sortiesQuiPartentEntre(
            $maintenant,
            $maintenant->modify(self::HORIZON),
        );

        foreach ($sorties as $sortie) {
            if ($sortie->estAnnulee()) {
                $this->confirmerLannulation($sortie, $maintenant);

                continue;
            }

            $this->alerterSiLheureEstVenue($sortie, $maintenant);

            foreach ($this->reservations->inscrits($sortie) as $reservation) {
                $this->rappel->envoyerSiDu($reservation, $maintenant);
                $this->lien->envoyerSiDu($reservation, $maintenant);
            }
        }
    }

    /**
     * La confirmation part à tout client **inscrit au moment de l'annulation**,
     * y compris celui qui a réservé après l'alerte et n'a donc jamais reçu
     * celle-ci (SPEC-CANCEL-06 AC-7), et y compris celui qui n'avait pas encore
     * payé (SPEC-CANCEL-04 AC-5).
     */
    private function confirmerLannulation(Sortie $sortie, DateTimeImmutable $maintenant): void
    {
        $heureDenvoi = $this->calendrier->confirmationDannulation(
            $sortie->creneau()->departPrevu(),
            $this->parametres->reglages()->delaiDeConfirmationEnHeures(),
        );

        if ($maintenant < $heureDenvoi) {
            return;
        }

        foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
            if ($reservation->statut() !== StatutDeReservation::ANNULEE) {
                continue;
            }

            $this->envoi->pour(
                $reservation,
                Notification::TYPE_CONFIRMATION_ANNULATION,
                $maintenant,
            );
        }
    }

    private function alerterSiLheureEstVenue(Sortie $sortie, DateTimeImmutable $maintenant): void
    {
        if ($sortie->statut() !== StatutDeSortie::EN_ALERTE) {
            return;
        }

        $heureDenvoi = $this->calendrier->alerte(
            $sortie->creneau()->departPrevu(),
            $this->parametres->reglages()->heureDalerte(),
        );

        if ($maintenant < $heureDenvoi) {
            return;
        }

        foreach ($this->reservations->inscrits($sortie) as $reservation) {
            $this->envoi->pour($reservation, Notification::TYPE_ALERTE_METEO, $maintenant);
        }
    }
}
