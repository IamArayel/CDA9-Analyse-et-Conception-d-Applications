<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Envoi\RappelDeSortie;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDePaiement;
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
 * **Le montant restant dû.** Un bon cadeau ou un avoir peut le ramener à zéro,
 * auquel cas le prestataire n'est jamais sollicité (AC-6).
 */
final class ConfirmerLePaiement
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly PrestataireDePaiement $prestataire,
        private readonly ReservationRepository $reservations,
        private readonly RappelDeSortie $rappel,
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

        $restantDu = $this->montantRestantDu($reservation);

        if ($restantDu > 0
            && !$this->prestataire->encaisser($reservation->reference(), $restantDu)) {
            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_TRANSACTION_REFUSEE);
        }

        if ($this->laPlaceEstPartie($reservation)) {
            if ($restantDu > 0) {
                $this->prestataire->rembourser($reservation->reference(), $restantDu);
            }

            $reservation->annuler();
            $this->entites->flush();

            return ResultatDePaiement::refuse(ResultatDePaiement::MOTIF_PLACES_INSUFFISANTES);
        }

        $this->confirmer($reservation);

        return ResultatDePaiement::confirme();
    }

    /** Le montant du séjour, diminué du code éventuellement appliqué. */
    private function montantRestantDu(Reservation $reservation): int
    {
        $code = $reservation->bonCadeau() ?? $reservation->avoir();

        if ($code === null) {
            return $reservation->montant();
        }

        return max(0, $reservation->montant() - $code->montant());
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
    private function confirmer(Reservation $reservation): void
    {
        $reservation->confirmer();
        $reservation->bonCadeau()?->marquerUtilise();
        $reservation->avoir()?->marquerUtilise();

        $this->entites->flush();

        // Une réservation prise après l'heure de rappel déclenche le rappel
        // immédiatement, et non pas jamais (SPEC-CANCEL-05 AC-5).
        $this->rappel->envoyerSiDu($reservation, $this->horloge->maintenant());
    }
}
