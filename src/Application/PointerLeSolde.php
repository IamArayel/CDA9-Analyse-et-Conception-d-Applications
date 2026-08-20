<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Paiement;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\ResultatDePaiement;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Pointer un solde encaissé au quai (SPEC-ADMIN-07).
 *
 * **Aucune transaction ne part.** Le gérant encaisse la carte sur son terminal
 * bancaire habituel, puis vient dire à l'outil que c'est fait. Le prestataire de
 * paiement n'est pas sollicité, et ne doit pas l'être : le débit a déjà eu lieu
 * ailleurs. C'est la différence de fond avec `SolderUneReservation`.
 *
 * **Le pointage est réversible et il laisse une trace** (`REQ-113`). Rétracter
 * ne supprime pas la ligne : elle est marquée comme ne comptant plus, et le
 * geste de rétractation s'inscrit à son tour. Un solde pointé, repris, puis
 * repointé laisse donc trois écritures, là où un simple drapeau n'en laisserait
 * aucune. C'est ce que le gérant pourra montrer si un client conteste.
 *
 * Pointer une réservation déjà soldée en ligne est **sans effet** : le gérant ne
 * peut pas savoir de tête qui a réglé la veille, et l'outil ne doit pas le punir
 * de vérifier (AC-6).
 */
final class PointerLeSolde
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
    ) {
    }

    public function executer(string $reference): ResultatDePaiement
    {
        $reservation = $this->reservation($reference);

        if ($reservation->estAnnulee() || $reservation->sortie()->estAnnulee()) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_CRENEAU_ANNULE);
        }

        $solde = $this->reglement->soldeDu($reservation, $this->paiements->verse($reservation));

        if ($solde === 0) {
            return ResultatDePaiement::confirme();
        }

        $this->paiements->enregistrer(new Paiement(
            $reservation,
            Paiement::TYPE_SOLDE,
            $solde,
            Paiement::CANAL_SUR_PLACE,
            $this->horloge->maintenant(),
            // `pointe_par` reste vide : SessionDeGestion ne porte qu'un jeton,
            // aucun service ne sait qui est connecté. Déclaré dans
            // docs/traceability-trous.md plutôt qu'inventé ici.
            pointePar: null,
        ));

        return ResultatDePaiement::confirme();
    }

    /**
     * Rétracter le pointage.
     *
     * Deux lignes en ressortent marquées : celle qui est reprise, et le geste
     * qui la reprend. Aucune des deux ne compte dans le versé, et ensemble elles
     * disent qu'un solde a été pointé puis rendu.
     */
    public function annuler(string $reference): ResultatDePaiement
    {
        $reservation = $this->reservation($reference);
        $pointage = $this->paiements->soldeActif($reservation);

        if ($pointage === null || $pointage->canal() !== Paiement::CANAL_SUR_PLACE) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_RIEN_A_REGLER);
        }

        $pointage->annuler();

        $retractation = new Paiement(
            $reservation,
            Paiement::TYPE_SOLDE,
            $pointage->montant(),
            Paiement::CANAL_SUR_PLACE,
            $this->horloge->maintenant(),
            pointePar: $pointage->pointePar(),
        );
        $retractation->annuler();

        $this->paiements->enregistrer($retractation);
        $this->entites->flush();

        return ResultatDePaiement::confirme();
    }

    private function reservation(string $reference): Reservation
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        return $reservation;
    }
}
