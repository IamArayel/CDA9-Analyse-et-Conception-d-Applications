<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Envoi\EnvoyerUnMessage;
use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\CalendrierDesEnvois;
use App\Domaine\StatutDeSortie;
use App\Infrastructure\Persistance\ParametreRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Mettre un créneau en alerte météo (SPEC-CANCEL-06).
 *
 * **L'alerte prévient, elle n'annule rien.** Elle ne se déclenche jamais seule :
 * aucune règle météo n'est automatisée, et l'application n'interroge aucun
 * service extérieur.
 *
 * Elle couvre **les deux bateaux du créneau** : la météo ne les distingue pas.
 * Une seconde alerte sur un créneau déjà alerté est sans effet, et ne renvoie
 * aucun message.
 *
 * Si l'heure d'envoi programmée est déjà passée, le message part immédiatement
 * au lieu d'attendre le lendemain (AC-8).
 */
final class MettreEnAlerte
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly EnvoyerUnMessage $envoi,
        private readonly CalendrierDesEnvois $calendrier,
        private readonly ParametreRepository $parametres,
    ) {
    }

    public function executer(string $jour, string $heure): void
    {
        $maintenant = $this->horloge->maintenant();
        $misEnAlerte = [];

        foreach ($this->sorties->sortiesDuCreneau($jour, $heure) as $sortie) {
            if ($sortie->estAnnulee() || $sortie->statut() === StatutDeSortie::EN_ALERTE) {
                continue;
            }

            $sortie->mettreEnAlerte($maintenant);
            $misEnAlerte[] = $sortie;
        }

        $this->entites->flush();

        foreach ($misEnAlerte as $sortie) {
            $this->envoyerSiLheureEstPassee($sortie, $maintenant);
        }
    }

    private function envoyerSiLheureEstPassee(Sortie $sortie, \DateTimeImmutable $maintenant): void
    {
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
