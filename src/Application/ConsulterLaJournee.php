<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Horloge;
use App\Domaine\Politique\SeuilDeMaintien;
use App\Domaine\Service\CalculDuStatutDeLaCarte;
use App\Domaine\Service\EtatDuReglement;
use App\Domaine\VueDeCarteDeSortie;
use App\Domaine\VueDeLaDecisionAPrendre;
use App\Domaine\VueDuTableauDeBord;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;

/**
 * Le tableau de bord du gérant pour une journée (G2).
 *
 * Le bandeau de décision reprend exactement la fenêtre du contrôle
 * automatique des 24 heures (`Application\Tache\ControlerSeuilDeMaintien`) :
 * il ne fait qu'avertir de ce que ce contrôle décidera seul si rien ne change,
 * il ne décide rien lui-même.
 */
final class ConsulterLaJournee
{
    private const FENETRE_DE_DECISION = '+24 hours';

    public function __construct(
        private readonly Horloge $horloge,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
        private readonly SeuilDeMaintien $seuil,
        private readonly CalculDuStatutDeLaCarte $statutDeLaCarte,
    ) {
    }

    public function executer(string $jour): VueDuTableauDeBord
    {
        $maintenant = $this->horloge->maintenant();
        $limiteDeDecision = $maintenant->modify(self::FENETRE_DE_DECISION);

        $inscritsDuJour = 0;
        $sortiesProgrammees = 0;
        $encaisse = 0;
        $soldeABord = 0;
        $cartes = [];
        $decisions = [];

        foreach ($this->sorties->sortiesDuJour($jour) as $sortie) {
            if ($sortie->estAnnulee()) {
                continue;
            }

            ++$sortiesProgrammees;
            $depart = $sortie->creneau()->departPrevu();
            $participants = 0;

            foreach ($this->reservations->inscrits($sortie) as $reservation) {
                $participants += $reservation->nombreDeParticipants();
                $verse = $this->paiements->verse($reservation);
                $encaisse += $verse;
                $soldeABord += $this->reglement->soldeDu($reservation, $verse);
            }

            $inscritsDuJour += $participants;

            $cartes[] = new VueDeCarteDeSortie(
                $jour,
                $sortie->creneau()->heureDeDepart(),
                $sortie->bateau()->nom(),
                $sortie->typeDeSortie(),
                $this->statutDeLaCarte->pour($sortie, $depart, $participants, $maintenant),
                $participants,
                $sortie->bateau()->capacite(),
            );

            $dansLaFenetre = $depart > $maintenant && $depart <= $limiteDeDecision;
            $sousLeSeuil = !$this->seuil->decider($participants, $depart, $maintenant)->sortieEstMaintenue();

            if (!$sortie->estPrivatisee() && $dansLaFenetre && $sousLeSeuil) {
                $decisions[] = new VueDeLaDecisionAPrendre(
                    $jour,
                    $sortie->creneau()->heureDeDepart(),
                    $sortie->bateau()->nom(),
                    $participants,
                );
            }
        }

        return new VueDuTableauDeBord(
            $jour,
            $inscritsDuJour,
            $sortiesProgrammees,
            $encaisse,
            $soldeABord,
            $cartes,
            $decisions,
        );
    }
}
