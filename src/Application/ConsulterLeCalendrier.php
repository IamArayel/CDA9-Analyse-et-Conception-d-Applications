<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\FuseauDexploitation;
use App\Domaine\Politique\OffreDeCreneaux;
use App\Domaine\VueDeJournee;
use App\Infrastructure\Persistance\CalendrierRepository;
use App\Infrastructure\Persistance\SortieRepository;

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
    ) {
    }

    public function executer(string $jour): VueDeJournee
    {
        if ($this->calendrier->jourEstFerme($jour)) {
            return new VueDeJournee([], []);
        }

        $instant = FuseauDexploitation::instant($jour);
        $annules = $this->sorties->heuresAnnulees($jour);

        $proposes = array_values(array_filter(
            $this->offre->creneauxProposes($instant),
            static fn (string $heure): bool => !in_array($heure, $annules, true),
        ));

        return new VueDeJournee($proposes, $this->offre->typesDeSortieProposes($instant));
    }
}
