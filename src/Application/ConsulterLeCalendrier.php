<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\FuseauDexploitation;
use App\Domaine\Horloge;
use App\Domaine\Politique\FermetureDesReservations;
use App\Domaine\Politique\OffreDeCreneaux;
use App\Domaine\Service\CalculDeLetatDuDepart;
use App\Domaine\VueDeJournee;
use App\Domaine\VueDuDepart;
use App\Infrastructure\Persistance\CalendrierRepository;
use App\Infrastructure\Persistance\SortieRepository;
use DateTimeImmutable;

/**
 * Ce qu'une journée propose au client (SPEC-BOOKING-02, SPEC-CANCEL-03).
 *
 * Trois filtres se composent, et l'ordre importe :
 *
 * 1. un **jour fermé** ne propose rien, pas même un créneau grisé ;
 * 2. sinon la journée propose les trois créneaux, qu'une sortie y soit déjà
 *    programmée ou non : le client choisit un créneau, la sortie suit ;
 * 3. sauf ceux dont une sortie a été **annulée**, qui disparaissent de l'offre.
 *
 * Un créneau en alerte, lui, reste proposé : l'alerte prévient, elle ne retire
 * rien.
 */
final class ConsulterLeCalendrier
{
    public function __construct(
        private readonly OffreDeCreneaux $offre,
        private readonly CalendrierRepository $calendrier,
        private readonly SortieRepository $sorties,
        private readonly ConsulterLesPlacesDisponibles $placesDisponibles,
        private readonly FermetureDesReservations $fermeture,
        private readonly CalculDeLetatDuDepart $etatDuDepart,
        private readonly Horloge $horloge,
    ) {
    }

    public function executer(string $jour): VueDeJournee
    {
        if ($this->calendrier->jourEstFerme($jour)) {
            return new VueDeJournee($jour, [], [], []);
        }

        $instant = FuseauDexploitation::instant($jour);
        $annules = $this->sorties->heuresAnnulees($jour);

        $proposes = array_values(array_filter(
            $this->offre->creneauxProposes($instant),
            static fn (string $heure): bool => !in_array($heure, $annules, true),
        ));

        return new VueDeJournee(
            $jour,
            $proposes,
            $this->offre->typesDeSortieProposes($instant),
            $this->departsDuJour($jour),
        );
    }

    /** @return list<VueDeJournee> */
    public function executerPourLaSemaine(DateTimeImmutable $lundi): array
    {
        $jours = [];

        for ($decalage = 0; $decalage < 7; ++$decalage) {
            $jours[] = $this->executer($lundi->modify(sprintf('+%d days', $decalage))->format('Y-m-d'));
        }

        return $jours;
    }

    /** @return list<VueDuDepart> */
    private function departsDuJour(string $jour): array
    {
        $maintenant = $this->horloge->maintenant();
        $departs = [];

        foreach (OffreDeCreneaux::CRENEAUX as $heure) {
            foreach ($this->sorties->sortiesDuCreneau($jour, $heure) as $sortie) {
                if ($sortie->estAnnulee()) {
                    continue;
                }

                $restantes = $this->placesDisponibles->pour((string) $sortie->id());

                $departs[] = new VueDuDepart(
                    $heure,
                    $sortie->typeDeSortie(),
                    $sortie->bateau()->nom(),
                    (int) $sortie->id(),
                    $this->etatDuDepart->pour($sortie, $restantes, $maintenant),
                    $restantes,
                    $this->fermeture->fermetureDe($sortie->creneau()->departPrevu()),
                );
            }
        }

        return $departs;
    }
}
