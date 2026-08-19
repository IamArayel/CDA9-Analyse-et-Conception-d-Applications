<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterLesDonneesConservees;
use App\Application\Tache\PurgerLesDonneesPersonnelles;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-NFR-04 - données personnelles conservées et purgées.
 *
 * Rien n'est collecté au-delà du formulaire, et aucune donnée bancaire ne
 * transite par l'application. La purge à trois mois porte une exception : un
 * bon cadeau vivant y échappe, sans quoi un bon valable un an deviendrait
 * inutilisable au bout d'un trimestre. C'est la contradiction relevée en revue
 * du modèle de données.
 */
final class DonneesPersonnellesTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1 et AC-2 : seules les données du formulaire sont stockées, et aucune
     * donnée de carte nulle part.
     */
    public function test_CASE_NFR_03_seules_les_donnees_du_formulaire_sont_stockees(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $reservation = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $champs = ($this->service(ConsulterLesDonneesConservees::class))->pour($reservation);

        self::assertSame(
            [
                'creneau',
                'email',
                'langue',
                'nom',
                'nombre_adultes',
                'nombre_enfants',
                'prenom',
                'telephone_mobile',
                'type_sortie',
            ],
            $this->trier(array_keys($champs)),
            'la liste des champs stockés est exactement celle du formulaire',
        );

        foreach (['numero_carte', 'date_expiration', 'cryptogramme'] as $donneeBancaire) {
            self::assertArrayNotHasKey(
                $donneeBancaire,
                $champs,
                'aucune donnée bancaire : elle ne transite pas par l\'application',
            );
        }
    }

    /**
     * AC-3 et AC-4 : les données sont purgées au terme du délai, sauf celles
     * qu'un bon cadeau vivant rend encore nécessaires.
     */
    public function test_CASE_NFR_04_purge_a_trois_mois_sauf_bon_cadeau_vivant(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $reservation = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $bonCadeau = $this->monde->bonCadeauAchete(
            Reference::euros(150),
            Reference::JOUR_EN_SAISON,
        );

        // Trois mois après la sortie du 20 juillet.
        $this->horloge->nousSommesLe('2026-10-21 03:00');
        $this->purger();

        self::assertSame(
            [],
            ($this->service(ConsulterLesDonneesConservees::class))->pour($reservation),
            'les données personnelles de la réservation sont supprimées ou anonymisées',
        );
        self::assertNotSame(
            [],
            ($this->service(ConsulterLesDonneesConservees::class))->pourUnCode($bonCadeau),
            'le bon cadeau échappe à la purge tant qu\'il est vivant',
        );

        // Le lendemain de son expiration, il n'a plus de raison d'être conservé.
        $this->horloge->nousSommesLe('2027-07-21 03:00');
        $this->purger();

        self::assertSame(
            [],
            ($this->service(ConsulterLesDonneesConservees::class))->pourUnCode($bonCadeau),
        );
    }

    private function purger(): void
    {
        ($this->service(PurgerLesDonneesPersonnelles::class))->executer();
    }

    /**
     * @param list<string> $champs
     *
     * @return list<string>
     */
    private function trier(array $champs): array
    {
        sort($champs);

        return $champs;
    }
}
