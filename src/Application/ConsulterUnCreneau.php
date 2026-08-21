<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Reservation;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\FermetureDesReservations;
use App\Domaine\Service\EtatDuReglement;
use App\Domaine\StatutDeSortie;
use App\Domaine\VueDeCreneau;
use App\Domaine\FuseauDexploitation;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\PrevisionMeteoRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use DateTimeImmutable;

/**
 * Ce qu'un créneau donne à voir, au gérant comme au client (SPEC-CANCEL-01,
 * SPEC-CANCEL-06).
 *
 * **La consultation est sans effet de bord** : elle ne déclenche ni alerte, ni
 * annulation, ni message. C'est la garantie sans laquelle un gérant hésiterait à
 * ouvrir un créneau pour se faire une idée.
 *
 * Ne sont listés que les clients qui ont payé : une réservation immobilisée mais
 * non payée n'est pas un inscrit, même si ses places sont retenues.
 */
final class ConsulterUnCreneau
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
        private readonly FermetureDesReservations $fermeture,
        private readonly PrevisionMeteoRepository $previsions,
    ) {
    }

    public function executer(string $jour, string $heure): VueDeCreneau
    {
        $sorties = $this->sorties->sortiesDuCreneau($jour, $heure);
        $maintenant = $this->horloge->maintenant();

        $statuts = [];
        $datesDalerte = [];
        $inscrits = [];
        $immobilisations = [];

        foreach ($sorties as $sortie) {
            $bateau = $sortie->bateau()->nom();
            $statuts[$bateau] = $sortie->statut();
            $datesDalerte[$bateau] = $sortie->dateDeMiseEnAlerte();
            $inscrits[$bateau] = $this->lignesDinscrits($sortie);
            $immobilisations[$bateau] = $this->lignesDimmobilisation($sortie, $maintenant);
        }

        $prevision = $this->previsions->pourLeJour(FuseauDexploitation::instant($jour));
        $depart = $this->depart($sorties, $jour, $heure);

        return new VueDeCreneau(
            $statuts,
            $datesDalerte,
            $inscrits,
            $this->estReservable($sorties, $jour, $heure, $maintenant),
            $this->risqueDannulationSignale($sorties),
            $this->estAnnulable($sorties, $jour, $heure, $maintenant),
            $immobilisations,
            $prevision?->texte(),
            $depart === null ? null : $this->fermeture->fermetureDe($depart),
        );
    }

    /** @return list<array<string, mixed>> */
    private function lignesDinscrits(Sortie $sortie): array
    {
        return array_map(
            function (Reservation $r): array {
                $verse = $this->paiements->verse($r);

                return [
                    'nom' => $r->nomClient(),
                    'prenom' => $r->prenomClient(),
                    'email' => $r->email(),
                    'telephone_mobile' => $r->telephoneMobile(),
                    'participants' => $r->nombreDeParticipants(),
                    'montant' => $r->montant(),
                    'solde_du' => $this->reglement->soldeDu($r, $verse),
                    'etat' => $this->etat($r, $verse),
                ];
            },
            $this->reservations->inscrits($sortie),
        );
    }

    private function etat(Reservation $reservation, int $verse): string
    {
        if ($reservation->bonCadeau() !== null) {
            return 'bon_cadeau';
        }

        return $this->reglement->soldeDu($reservation, $verse) === 0 ? 'soldee' : 'confirmee';
    }

    /** @return list<array{minutes: int}> */
    private function lignesDimmobilisation(Sortie $sortie, DateTimeImmutable $maintenant): array
    {
        $lignes = [];

        foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
            if (!$reservation->immobiliseDesPlaces($maintenant)) {
                continue;
            }

            $secondes = $reservation->expireLe()->getTimestamp() - $maintenant->getTimestamp();
            $lignes[] = ['minutes' => max(0, (int) ceil($secondes / 60))];
        }

        return $lignes;
    }

    /**
     * Réservable si l'heure de fermeture n'est pas passée et qu'aucune sortie
     * du créneau n'a été annulée. Une alerte, elle, ne retire rien.
     *
     * @param list<Sortie> $sorties
     */
    private function estReservable(
        array $sorties,
        string $jour,
        string $heure,
        \DateTimeImmutable $maintenant,
    ): bool {
        foreach ($sorties as $sortie) {
            if ($sortie->estAnnulee()) {
                return false;
            }
        }

        $depart = $this->depart($sorties, $jour, $heure);

        return $depart !== null && $this->fermeture->estReservable($depart, $maintenant);
    }

    /** @param list<Sortie> $sorties */
    private function risqueDannulationSignale(array $sorties): bool
    {
        foreach ($sorties as $sortie) {
            if ($sortie->statut() === StatutDeSortie::EN_ALERTE) {
                return true;
            }
        }

        return false;
    }

    /**
     * Un créneau déjà passé n'est plus annulable : il a eu lieu. Un créneau sans
     * aucun inscrit l'est, en revanche.
     *
     * @param list<Sortie> $sorties
     */
    private function estAnnulable(
        array $sorties,
        string $jour,
        string $heure,
        \DateTimeImmutable $maintenant,
    ): bool {
        $depart = $this->depart($sorties, $jour, $heure);

        if ($depart === null || $maintenant >= $depart) {
            return false;
        }

        foreach ($sorties as $sortie) {
            if ($sortie->estAnnulee()) {
                return false;
            }
        }

        return true;
    }

    /** @param list<Sortie> $sorties */
    private function depart(array $sorties, string $jour, string $heure): ?\DateTimeImmutable
    {
        if ($sorties !== []) {
            return $sorties[0]->creneau()->departPrevu();
        }

        return $this->sorties->creneau($jour, $heure)?->departPrevu();
    }
}
