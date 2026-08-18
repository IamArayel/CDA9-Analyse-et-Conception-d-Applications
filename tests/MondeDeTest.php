<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\ConfirmerLePaiement;
use App\Application\CreerReservation;
use App\Application\ProgrammerUneSortie;
use App\Application\ReglerLesParametres;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\Doublures\HorlogeFigee;
use App\Tests\Doublures\PaiementSimule;

/**
 * L'état du monde avant un cas de test, monté en vocabulaire métier.
 *
 * Les préconditions d'un cas se lisent ici telles qu'elles sont écrites dans
 * son fichier : « une sortie dauphins du 20 juillet à 10h sur le Ti Kap, avec
 * 9 places déjà vendues ». Le montage passe par les services applicatifs qui
 * créent réellement ces objets, jamais par un raccourci de test : un monde
 * monté autrement ne prouverait rien.
 *
 * C'est aussi le seul endroit à rebrancher au conteneur Symfony et à la base
 * de données quand le socle sera livré.
 */
final class MondeDeTest
{
    public function __construct(
        private readonly HorlogeFigee $horloge,
        private readonly EnvoisEnregistres $messages,
        private readonly PaiementSimule $paiement,
    ) {
    }

    /**
     * Une sortie programmée sur un créneau et un bateau donnés.
     *
     * @return string la référence de la sortie
     */
    public function sortieProgrammee(
        string $jour,
        string $heure,
        string $bateau,
        string $typeDeSortie = JeuDeDonneesDeReference::SORTIE_DAUPHINS,
    ): string {
        return (new ProgrammerUneSortie($this->horloge))
            ->executer($jour, $heure, $bateau, $typeDeSortie);
    }

    /**
     * Une réservation payée, donc confirmée et comptée dans les inscrits.
     *
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string} $client
     *
     * @return string la référence de la réservation
     */
    public function reservationPayee(
        string $sortie,
        array $client,
        int $adultes,
        int $enfants = 0,
        ?int $montant = null,
    ): string {
        $reservation = $this->reservationImmobilisee($sortie, $client, $adultes, $enfants);

        (new ConfirmerLePaiement($this->horloge))->executer($reservation, $montant);

        return $reservation;
    }

    /**
     * Une réservation dont le formulaire est validé mais que personne n'a
     * payée : ses places sont immobilisées, cf. ADR-003.
     *
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string} $client
     *
     * @return string la référence de la réservation
     */
    public function reservationImmobilisee(
        string $sortie,
        array $client,
        int $adultes,
        int $enfants = 0,
    ): string {
        $resultat = (new CreerReservation($this->horloge))
            ->executer($sortie, $client, $adultes, $enfants);

        return $resultat->referenceDeReservation();
    }

    /**
     * Un nombre de places vendues sur une sortie, quand l'identité des clients
     * n'intervient pas dans le cas.
     */
    public function placesVendues(string $sortie, int $nombre): void
    {
        for ($place = 1; $place <= $nombre; ++$place) {
            $this->reservationPayee(
                $sortie,
                [
                    'nom' => 'Passager',
                    'prenom' => (string) $place,
                    'email' => sprintf('passager%02d@example.test', $place),
                    'telephone_mobile' => sprintf('06920100%02d', $place),
                    'langue' => 'fr',
                ],
                adultes: 1,
            );
        }
    }

    /** Les réglages de l'espace de gestion, cf. SPEC-CANCEL-06 AC-9. */
    public function parametresDenvoi(
        ?string $heureDenvoiDeLalerte = null,
        ?int $delaiDeConfirmationEnHeures = null,
    ): void {
        (new ReglerLesParametres())
            ->executer($heureDenvoiDeLalerte, $delaiDeConfirmationEnHeures);
    }

    public function messages(): EnvoisEnregistres
    {
        return $this->messages;
    }

    public function paiement(): PaiementSimule
    {
        return $this->paiement;
    }
}
