<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Envoi\LienDeReglement;
use App\Application\Envoi\RappelDeSortie;
use App\Domaine\Entite\Paiement;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDePaiement;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Confirmer une réservation après paiement (SPEC-BOOKING-07).
 *
 * Trois points méritent d'être lus attentivement.
 *
 * **L'idempotence.** Une réservation déjà confirmée ressort confirmée sans
 * second encaissement : un double clic ou un retour arrière du navigateur ne
 * doit produire qu'un débit (AC-4).
 *
 * **L'ordre encaissement puis vérification.** Quand le prestataire confirme, le
 * débit a déjà eu lieu de son côté. Si l'immobilisation a expiré pendant le
 * tunnel et qu'un autre client a emporté la place, il est donc trop tard pour
 * refuser sans rembourser : le remboursement part **sans que le client ait à le
 * demander** (AC-7). Le cas est rare, l'immobilisation le réduit sans le
 * supprimer.
 *
 * **Ce qui est encaissé n'est plus le prix, c'est l'acompte** (`REQ-108`) : 30 %
 * pour une sortie, 50 % pour une privatisation, et le reste devient un solde que
 * `SolderUneReservation` ou `PointerLeSolde` iront chercher. Un bon cadeau ou un
 * avoir change la donne : `REQ-116` demande alors la différence en une seule
 * fois, et cette différence peut être nulle, auquel cas le prestataire n'est
 * jamais sollicité (AC-6). Le calcul lui-même vit dans `EtatDuReglement`.
 */
final class ConfirmerLePaiement
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly PrestataireDePaiement $prestataire,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
        private readonly RappelDeSortie $rappel,
        private readonly LienDeReglement $lien,
    ) {
    }

    public function executer(string $reference): ResultatDePaiement
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        if ($reservation->estConfirmee()) {
            return ResultatDePaiement::confirme();
        }

        if ($reservation->sortie()->estAnnulee()) {
            $reservation->annuler();
            $this->entites->flush();

            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_CRENEAU_ANNULE);
        }

        $aVerser = $this->reglement->versementDentree($reservation);

        if ($aVerser > 0
            && !$this->prestataire->encaisser($reservation->reference(), $aVerser)) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_TRANSACTION_REFUSEE);
        }

        if ($this->laPlaceEstPartie($reservation)) {
            if ($aVerser > 0) {
                $this->prestataire->rembourser($reservation->reference(), $aVerser);
            }

            $reservation->annuler();
            $this->entites->flush();

            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_PLACES_INSUFFISANTES);
        }

        $this->confirmer($reservation, $aVerser);

        return ResultatDePaiement::confirme();
    }

    private function laPlaceEstPartie(Reservation $reservation): bool
    {
        $sortie = $reservation->sortie();

        $prisesParLesAutres = $this->reservations->placesPrisesSauf(
            $sortie,
            $reservation,
            $this->horloge->maintenant(),
        );

        return $prisesParLesAutres + $reservation->nombreDeParticipants()
            > $sortie->bateau()->capacite();
    }

    /**
     * Le code est consommé ici, et non à sa saisie : un code appliqué sur une
     * réservation jamais payée doit rester utilisable ailleurs.
     */
    private function confirmer(Reservation $reservation, int $verse): void
    {
        $reservation->confirmer();
        $reservation->bonCadeau()?->marquerUtilise();
        $reservation->avoir()?->marquerUtilise();

        $this->entites->flush();

        // Un versement nul ne laisse pas d'écriture : rien n'a été encaissé, et
        // une ligne à zéro euro ferait croire à une transaction.
        if ($verse > 0) {
            $this->paiements->enregistrer(new Paiement(
                $reservation,
                Paiement::TYPE_ACOMPTE,
                $verse,
                Paiement::CANAL_EN_LIGNE,
                $this->horloge->maintenant(),
            ));
        }

        // Une réservation prise après l'heure de rappel déclenche le rappel
        // immédiatement, et non pas jamais (SPEC-CANCEL-05 AC-5). Le lien de
        // règlement suit la même règle, pour la même raison (SPEC-CANCEL-07).
        $this->rappel->envoyerSiDu($reservation, $this->horloge->maintenant());
        $this->lien->envoyerSiDu($reservation, $this->horloge->maintenant());
    }
}
