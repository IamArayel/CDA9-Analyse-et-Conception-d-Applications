<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Reservation;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\Immobilisation;
use App\Domaine\Politique\Coordonnees;
use App\Domaine\ResultatDeReservation;
use App\Domaine\StatutDeReservation;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Privatiser un bateau sur un créneau (SPEC-BOOKING-05).
 *
 * **Le montant est le forfait du bateau**, indépendant du nombre de
 * participants : quatre personnes ou douze paient la même chose. Et la
 * privatisation bloque **toutes** les places, pas seulement celles occupées.
 *
 * Elle est refusée si le bateau porte déjà des places vendues sur ce créneau.
 * Les réservations existantes ne sont ni annulées ni déplacées : le gérant ne
 * réattribue rien automatiquement.
 *
 * Le seuil de six inscrits ne s'y applique pas, le bateau étant payé en entier.
 */
final class PrivatiserUnBateau
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly Coordonnees $coordonnees,
        private readonly Immobilisation $immobilisation,
    ) {
    }

    /**
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string|null} $client
     */
    public function executer(
        string $jour,
        string $heure,
        string $bateau,
        array $client,
        int $participants,
    ): ResultatDeReservation {
        $sortie = $this->sortieDuBateau($jour, $heure, $bateau);

        if ($sortie === null) {
            throw new InvalidArgumentException(
                sprintf('Aucune sortie du %s à %s sur « %s ».', $jour, $heure, $bateau)
            );
        }

        if ($sortie->estPrivatisee() || $this->reservations->inscrits($sortie) !== []) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE,
            );
        }

        $forfait = $sortie->bateau()->forfaitDePrivatisation();

        if ($forfait === null) {
            return ResultatDeReservation::refusee(
                ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE,
            );
        }

        $maintenant = $this->horloge->maintenant();

        $reservation = new Reservation(
            $sortie,
            $client['nom'],
            $client['prenom'],
            $client['email'],
            $this->coordonnees->normaliserLeMobile($client['telephone_mobile']),
            $participants,
            0,
            $forfait,
            $client['langue'] ?? 'fr',
            $maintenant,
            $this->immobilisation->echeance($maintenant),
        );

        $sortie->privatiser();
        $this->reservations->enregistrer($reservation);
        $this->entites->flush();

        return ResultatDeReservation::acceptee(
            $reservation->reference(),
            StatutDeReservation::EN_ATTENTE_DE_PAIEMENT,
            $reservation->expireLe(),
        );
    }

    private function sortieDuBateau(string $jour, string $heure, string $bateau): ?Sortie
    {
        foreach ($this->sorties->sortiesDuCreneau($jour, $heure) as $sortie) {
            if ($sortie->bateau()->nom() === $bateau) {
                return $sortie;
            }
        }

        return null;
    }
}
