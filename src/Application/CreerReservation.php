<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Reservation;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\CompositionDeLaReservation;
use App\Domaine\Politique\Coordonnees;
use App\Domaine\Politique\FermetureDesReservations;
use App\Domaine\Politique\Immobilisation;
use App\Domaine\ResultatDeReservation;
use App\Domaine\Service\CalculDuMontant;
use App\Domaine\StatutDeReservation;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use App\Infrastructure\Persistance\TarifRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Créer une réservation à partir d'un formulaire validé (SPEC-BOOKING-01, 03,
 * 04 et 06).
 *
 * **Toute la méthode tient dans une transaction qui verrouille la ligne
 * `sortie`** (ADR-003, architecture.md §5). C'est ce verrou, et lui seul, qui
 * empêche deux clients d'emporter la dernière place : la vérification et
 * l'écriture ne peuvent pas être séparées par une autre transaction.
 *
 * Le verrou est pris **à la validation du formulaire**, pas à l'encaissement.
 * C'est le revirement du 2026-08-14 : sans lui, le client perdant était débité
 * puis remboursé pour une place qu'il n'aurait jamais.
 *
 * Les refus sont rendus dans l'ordre où le client les rencontrerait : ce qu'il
 * a saisi d'abord, l'état du créneau ensuite, la disponibilité en dernier.
 */
final class CreerReservation
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly TarifRepository $tarifs,
        private readonly CompositionDeLaReservation $composition,
        private readonly Coordonnees $coordonnees,
        private readonly FermetureDesReservations $fermeture,
        private readonly Immobilisation $immobilisation,
    ) {
    }

    /**
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string|null} $client
     */
    public function executer(
        string $sortie,
        array $client,
        int $adultes,
        int $enfants = 0,
    ): ResultatDeReservation {
        $refusPrealable = $this->refuserLaSaisie($client, $adultes, $enfants);

        if ($refusPrealable !== null) {
            return $refusPrealable;
        }

        return $this->entites->wrapInTransaction(
            fn (): ResultatDeReservation => $this->sousVerrou($sortie, $client, $adultes, $enfants)
        );
    }

    /** Ce qui se refuse sans rien verrouiller : la saisie du client. */
    private function refuserLaSaisie(array $client, int $adultes, int $enfants): ?ResultatDeReservation
    {
        if (!$this->composition->estValide($adultes, $enfants)) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_COMPOSITION_INVALIDE,
            );
        }

        $champ = $this->coordonnees->champEnCause(
            $client['email'],
            $client['telephone_mobile'],
        );

        if ($champ !== null) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_COORDONNEES_INVALIDES,
                $champ,
            );
        }

        return null;
    }

    private function sousVerrou(
        string $reference,
        array $client,
        int $adultes,
        int $enfants,
    ): ResultatDeReservation {
        $sortie = $this->entites->find(Sortie::class, (int) $reference, LockMode::PESSIMISTIC_WRITE);

        if ($sortie === null) {
            throw new InvalidArgumentException(sprintf('Aucune sortie « %s ».', $reference));
        }

        $maintenant = $this->horloge->maintenant();

        if ($sortie->estAnnulee()) {
            return ResultatDeReservation::refusee(ResultatDeReservation::MOTIF_CRENEAU_ANNULE);
        }

        if (!$this->fermeture->estReservable($sortie->creneau()->departPrevu(), $maintenant)) {
            return ResultatDeReservation::refusee(ResultatDeReservation::MOTIF_CRENEAU_FERME);
        }

        // Un bateau privatisé ne vend plus de place à l'unité, quel que soit le
        // nombre de participants de la privatisation.
        if ($sortie->estPrivatisee()) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE,
            );
        }

        $restantes = $sortie->bateau()->capacite()
            - $this->reservations->placesPrises($sortie, $maintenant);

        if ($adultes + $enfants > $restantes) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_PLACES_INSUFFISANTES,
            );
        }

        $reservation = new Reservation(
            $sortie,
            $client['nom'],
            $client['prenom'],
            $client['email'],
            $this->coordonnees->normaliserLeMobile($client['telephone_mobile']),
            $adultes,
            $enfants,
            (new CalculDuMontant($this->tarifs->grille()))
                ->pour($sortie->typeDeSortie(), $adultes, $enfants),
            $client['langue'] ?? 'fr',
            $maintenant,
            $this->immobilisation->echeance($maintenant),
        );

        $this->reservations->enregistrer($reservation);

        return ResultatDeReservation::acceptee(
            $reservation->reference(),
            StatutDeReservation::EN_ATTENTE_DE_PAIEMENT,
            $reservation->expireLe(),
        );
    }
}
