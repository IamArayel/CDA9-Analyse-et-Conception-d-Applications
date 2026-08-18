<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterLeCalendrier;
use App\Domaine\HorsSaison;
use App\Domaine\VueDeJournee;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-02 - le calendrier des sorties proposées.
 *
 * Un jour fermé ne propose rien, pas même un créneau grisé. Et masquer une
 * option ne suffit pas à la rendre impossible : le refus d'une sortie baleines
 * hors saison vient de l'enregistrement, pas de l'affichage.
 */
final class CalendrierDesSortiesTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-11-01 09:00';
    }

    /**
     * AC-4 : un jour de fermeture ne propose aucun créneau, et la fermeture ne
     * déborde pas sur le lendemain.
     */
    public function test_CASE_BOOKING_25_jour_de_fermeture_aucun_creneau(): void
    {
        foreach (Reference::JOURS_DE_FERMETURE as $jourFerme) {
            self::assertSame(
                [],
                $this->journee($jourFerme)->creneauxProposes(),
                'aucun des trois créneaux n\'existe sur un jour fermé',
            );
        }

        $lendemain = $this->journee('2026-12-26');
        self::assertSame(
            [
                Reference::CRENEAU_MATIN,
                Reference::CRENEAU_MILIEU_DE_MATINEE,
                Reference::CRENEAU_APRES_MIDI,
            ],
            $lendemain->creneauxProposes(),
            'le 26 décembre est un jour ouvert ordinaire',
        );
        self::assertSame(
            [Reference::SORTIE_DAUPHINS],
            $lendemain->typesDeSortieProposes(),
            'hors saison, les dauphins seuls',
        );
    }

    /**
     * AC-5 : une sortie baleines hors saison est refusée à l'enregistrement,
     * et pas seulement masquée à l'affichage.
     */
    public function test_CASE_BOOKING_26_sortie_baleines_hors_saison_refusee(): void
    {
        $refus = null;

        try {
            $this->monde->sortieProgrammee(
                Reference::JOUR_HORS_SAISON,
                Reference::CRENEAU_MILIEU_DE_MATINEE,
                Reference::TI_KAP,
                Reference::SORTIE_BALEINES,
            );
        } catch (HorsSaison $horsSaison) {
            $refus = $horsSaison;
        }

        self::assertNotNull(
            $refus,
            'les sorties baleines ne sont proposées que du 15 juin au 31 octobre',
        );

        $dauphins = $this->monde->sortieProgrammee(
            Reference::JOUR_HORS_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        self::assertNotSame(
            '',
            $dauphins,
            'une sortie dauphins à la même date reste acceptée',
        );
    }

    private function journee(string $jour): VueDeJournee
    {
        return (new ConsulterLeCalendrier($this->horloge))->executer($jour);
    }
}
