<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Paiement;
use App\Domaine\Horloge;
use App\Domaine\Politique\FenetreDeReglement;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDePaiement;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use InvalidArgumentException;

/**
 * Régler en ligne le solde d'une réservation (SPEC-BOOKING-12).
 *
 * **Une seconde transaction, jamais un ajustement de la première.** Le client a
 * versé un acompte à la réservation ; le solde part chez le prestataire pour son
 * propre montant, et laisse sa propre écriture (`REQ-117`). Cela répond aussi à
 * l'obligation des deux factures, acompte puis solde, arbitrée en CR-07/Q07.
 *
 * **La fenêtre s'ouvre avec le lien**, à 7h la veille, et non 24 heures avant le
 * départ : les deux coïncident pour les créneaux du matin, elles divergent de
 * sept heures pour celui de 14h, et c'est le mail qui fait foi. Elle se ferme
 * quand le créneau se ferme, après quoi il reste la carte au quai
 * (SPEC-ADMIN-07).
 *
 * **Rien à régler n'est pas une erreur.** Un code qui a couvert le prix laisse
 * un solde nul : le service le dit et ne sollicite personne (AC-3).
 */
final class SolderUneReservation
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly PrestataireDePaiement $prestataire,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
        private readonly FenetreDeReglement $fenetre,
    ) {
    }

    public function executer(string $reference): ResultatDePaiement
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        if ($reservation->estAnnulee() || $reservation->sortie()->estAnnulee()) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_CRENEAU_ANNULE);
        }

        $solde = $this->reglement->soldeDu($reservation, $this->paiements->verse($reservation));

        if ($solde === 0) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_RIEN_A_REGLER);
        }

        $maintenant = $this->horloge->maintenant();

        if (!$this->fenetre->estOuverte($reservation->sortie()->creneau()->departPrevu(), $maintenant)) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_HORS_FENETRE);
        }

        if (!$this->prestataire->encaisser($reservation->reference(), $solde)) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_TRANSACTION_REFUSEE);
        }

        $this->paiements->enregistrer(new Paiement(
            $reservation,
            Paiement::TYPE_SOLDE,
            $solde,
            Paiement::CANAL_EN_LIGNE,
            $maintenant,
        ));

        return ResultatDePaiement::confirme();
    }
}
