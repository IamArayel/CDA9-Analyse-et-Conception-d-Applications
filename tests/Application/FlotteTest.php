<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterLesFormules;
use App\Application\ConsulterLesPlacesDisponibles;
use App\Application\CreerUnBateau;
use App\Application\DefinirLeForfaitDePrivatisation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-05 - gestion de la flotte.
 *
 * Un bateau créé est visible côté client sans intervention technique ni
 * redéploiement. En revanche le formulaire de création ne demande pas de
 * forfait de privatisation, alors que la privatisation est tarifée par bateau :
 * c'est la contradiction relevée en revue, et elle se règle en ne proposant pas
 * la privatisation tant que le forfait est vide.
 */
final class FlotteTest extends CasDapplication
{
    private const LE_PETIT_BLEU = 'Le Petit Bleu';

    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1 et AC-2 : un bateau créé apparaît côté client avec sa capacité, pour
     * les deux types de sortie.
     */
    public function test_CASE_ADMIN_10_bateau_cree_apparait_avec_sa_capacite(): void
    {
        (new CreerUnBateau($this->horloge))->executer(self::LE_PETIT_BLEU, capacite: 8);

        foreach ([Reference::SORTIE_DAUPHINS, Reference::SORTIE_BALEINES] as $type) {
            $sortie = $this->monde->sortieProgrammee(
                Reference::JOUR_EN_SAISON,
                $type === Reference::SORTIE_DAUPHINS
                    ? Reference::CRENEAU_MILIEU_DE_MATINEE
                    : Reference::CRENEAU_APRES_MIDI,
                self::LE_PETIT_BLEU,
                $type,
            );

            self::assertSame(
                8,
                (new ConsulterLesPlacesDisponibles($this->horloge))->pour($sortie),
                'la capacité affichée est exactement celle saisie',
            );
        }
    }

    /**
     * AC-5 : un bateau sans forfait n'est pas proposé à la privatisation, et le
     * devient dès que le forfait est saisi.
     */
    public function test_CASE_ADMIN_11_bateau_sans_forfait_non_privatisable(): void
    {
        (new CreerUnBateau($this->horloge))->executer(self::LE_PETIT_BLEU, capacite: 8);
        $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            self::LE_PETIT_BLEU,
            Reference::SORTIE_DAUPHINS,
        );

        self::assertNotContains(
            'privatisation',
            $this->formules(),
            'la privatisation est indisponible tant que le forfait est vide',
        );

        (new DefinirLeForfaitDePrivatisation($this->horloge))
            ->executer(self::LE_PETIT_BLEU, Reference::euros(450));

        self::assertContains(
            'privatisation',
            $this->formules(),
            'elle devient disponible dès que le forfait est saisi',
        );
        self::assertSame(
            Reference::euros(450),
            (new ConsulterLesFormules($this->horloge))->forfaitDePrivatisation(self::LE_PETIT_BLEU),
        );
    }

    /** @return list<string> */
    private function formules(): array
    {
        return (new ConsulterLesFormules($this->horloge))->pour(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            self::LE_PETIT_BLEU,
        );
    }
}
