<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\AcheterUnBonCadeau;
use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\CreerReservation;
use App\Application\EmettreUnAvoir;
use App\Application\ProgrammerUneSortie;
use App\Application\ReglerLesParametres;
use App\Application\SaisirLaPrevisionMeteo;
use App\Domaine\Entite\Bateau;
use App\Domaine\Entite\Gerant;
use App\Domaine\Entite\JourDeFermeture;
use App\Domaine\Entite\Parametre;
use App\Domaine\Entite\Tarif;
use App\Domaine\TypeDeSortie;
use App\Tests\Doublures\HorlogeFigee;
use App\Tests\JeuDeDonneesDeReference as Reference;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * L'état du monde avant un cas de test, monté en vocabulaire métier.
 *
 * Les préconditions d'un cas se lisent ici telles qu'elles sont écrites dans
 * son fichier : « une sortie dauphins du 20 juillet à 10h sur le Ti Kap, avec
 * 9 places déjà vendues ». Le montage passe par les services applicatifs qui
 * créent réellement ces objets, jamais par un raccourci de test : un monde
 * monté autrement ne prouverait rien.
 *
 * Seules les **données de référence** sont écrites directement en base : ce
 * sont des données d'exploitation, pas le produit d'un cas d'usage.
 */
final class MondeDeTest
{
    public function __construct(
        private readonly ContainerInterface $conteneur,
        private readonly EntityManagerInterface $entites,
        private readonly HorlogeFigee $horloge,
    ) {
    }

    /**
     * Le jeu de données de référence de docs/strategie-de-test.md §7 : la
     * flotte, les tarifs, les jours de fermeture, le compte du gérant et les
     * réglages d'envoi.
     */
    public function chargerLeJeuDeReference(): void
    {
        $this->entites->persist(new Bateau(
            Reference::TI_KAP,
            Reference::TI_KAP_CAPACITE,
            Reference::TI_KAP_FORFAIT_PRIVATISATION,
        ));
        $this->entites->persist(new Bateau(
            Reference::LE_GRAND_BLEU,
            Reference::LE_GRAND_BLEU_CAPACITE,
            Reference::LE_GRAND_BLEU_FORFAIT_PRIVATISATION,
        ));

        $this->entites->persist(new Tarif(
            TypeDeSortie::BALEINES,
            Reference::BALEINES_PRIX_ADULTE,
            Reference::BALEINES_PRIX_ENFANT,
        ));
        $this->entites->persist(new Tarif(
            TypeDeSortie::DAUPHINS,
            Reference::DAUPHINS_PRIX_ADULTE,
            Reference::DAUPHINS_PRIX_ENFANT,
        ));

        foreach (Reference::JOURS_DE_FERMETURE as $jour) {
            $this->entites->persist(new JourDeFermeture(Reference::instant($jour), true));
        }

        $this->entites->persist(new Gerant(
            Reference::EMAIL_DU_GERANT,
            password_hash(Reference::MOT_DE_PASSE_DU_GERANT, PASSWORD_DEFAULT),
        ));

        $this->entites->persist(new Parametre());

        $this->entites->flush();
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
        string $typeDeSortie = TypeDeSortie::DAUPHINS,
    ): string {
        return $this->service(ProgrammerUneSortie::class)
            ->executer($jour, $heure, $bateau, $typeDeSortie);
    }

    /**
     * Une réservation payée, donc confirmée et comptée dans les inscrits.
     *
     * Le montant n'est pas fourni : il est calculé par le domaine à partir des
     * tarifs de référence, comme il le sera en production. Un cas qui attend
     * 160 € l'exprime par le nombre d'adultes et d'enfants, pas par le nombre.
     *
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string|null} $client
     *
     * @return string la référence de la réservation
     */
    public function reservationPayee(
        string $sortie,
        array $client,
        int $adultes,
        int $enfants = 0,
    ): string {
        $reservation = $this->reservationImmobilisee($sortie, $client, $adultes, $enfants);

        $this->service(ConfirmerLePaiement::class)->executer($reservation);

        return $reservation;
    }

    /**
     * Une réservation dont le formulaire est validé mais que personne n'a
     * payée : ses places sont immobilisées, cf. ADR-003.
     *
     * @param array{nom: string, prenom: string, email: string,
     *              telephone_mobile: string, langue: string|null} $client
     *
     * @return string la référence de la réservation
     */
    public function reservationImmobilisee(
        string $sortie,
        array $client,
        int $adultes,
        int $enfants = 0,
    ): string {
        return $this->service(CreerReservation::class)
            ->executer($sortie, $client, $adultes, $enfants)
            ->referenceDeReservation();
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

    /**
     * Un bon cadeau acheté et payé, donc utilisable.
     *
     * @return string le code délivré à l'acheteur
     */
    public function bonCadeauAchete(int $montant, string $jourDachat): string
    {
        $this->horloge->nousSommesLe($jourDachat.' 10:00');

        return $this->service(AcheterUnBonCadeau::class)
            ->executer($montant, Reference::CLIENT_MARIE);
    }

    /**
     * Un bon cadeau déjà consommé sur une réservation antérieure.
     *
     * @return string le code, désormais inutilisable
     */
    public function bonCadeauDejaUtilise(int $montant, string $jourDachat, string $sortie): string
    {
        $code = $this->bonCadeauAchete($montant, $jourDachat);

        $reservation = $this->reservationImmobilisee($sortie, Reference::CLIENT_MARIE, adultes: 1);
        $this->service(AppliquerUnCode::class)->executer($reservation, $code);
        $this->service(ConfirmerLePaiement::class)->executer($reservation);

        return $code;
    }

    /**
     * Un avoir émis au bénéfice d'un client, cf. SPEC-ADMIN-06.
     *
     * @return string le code de l'avoir
     */
    public function avoirEmis(int $montant, string $jourDemission): string
    {
        $this->horloge->nousSommesLe($jourDemission.' 10:00');

        return $this->service(EmettreUnAvoir::class)
            ->executer($montant, Reference::CLIENT_MARIE);
    }

    /** Les réglages de l'espace de gestion, cf. SPEC-CANCEL-06 AC-9. */
    public function parametresDenvoi(
        ?string $heureDenvoiDeLalerte = null,
        ?int $delaiDeConfirmationEnHeures = null,
        ?int $delaiDeRappelEnHeures = null,
    ): void {
        $this->service(ReglerLesParametres::class)->executer(
            $heureDenvoiDeLalerte,
            $delaiDeConfirmationEnHeures,
            $delaiDeRappelEnHeures,
        );
    }

    /**
     * La prévision météo du jour, saisie par le gérant : l'application
     * n'interroge aucun service externe, cf. SPEC-CANCEL-05.
     */
    public function previsionMeteo(string $jour, string $prevision): void
    {
        $this->service(SaisirLaPrevisionMeteo::class)->executer($jour, $prevision);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $service
     *
     * @return T
     */
    private function service(string $service): object
    {
        return $this->conteneur->get($service);
    }
}
