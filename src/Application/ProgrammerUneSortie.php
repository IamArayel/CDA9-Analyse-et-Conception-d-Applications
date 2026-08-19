<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Sortie;
use App\Domaine\FuseauDexploitation;
use App\Domaine\HorsSaison;
use App\Domaine\NaturalisteIndisponible;
use App\Domaine\Politique\OffreDeCreneaux;
use App\Domaine\TypeDeSortie;
use App\Infrastructure\Persistance\SortieRepository;
use InvalidArgumentException;

/**
 * Programmer une sortie sur un créneau et un bateau (SPEC-BOOKING-02).
 *
 * Deux refus possibles, et ils viennent de deux endroits différents : la saison
 * est une règle du domaine, l'unicité du naturaliste une contrainte de la base.
 * Le second est traduit en refus métier par le dépôt, et non contrôlé ici : un
 * contrôle applicatif laisserait passer deux demandes simultanées.
 */
final class ProgrammerUneSortie
{
    public function __construct(
        private readonly SortieRepository $sorties,
        private readonly OffreDeCreneaux $offre,
    ) {
    }

    /**
     * @return string la référence de la sortie créée
     *
     * @throws HorsSaison si une sortie baleines est demandée hors saison
     */
    public function executer(
        string $jour,
        string $heure,
        string $bateau,
        string $typeDeSortie = TypeDeSortie::DAUPHINS,
    ): string {
        $this->refuserLesBaleinesHorsSaison($jour, $typeDeSortie);
        $this->refuserUnSecondNaturaliste($jour, $heure, $typeDeSortie);

        $navire = $this->sorties->bateau($bateau);

        if ($navire === null) {
            throw new InvalidArgumentException(sprintf('Aucun bateau nommé « %s ».', $bateau));
        }

        $sortie = new Sortie(
            $this->sorties->creneauOuNouveau($jour, $heure),
            $navire,
            $typeDeSortie,
        );

        $this->sorties->enregistrer($sortie);

        return (string) $sortie->id();
    }

    /**
     * Le contrôle applicatif de la règle du naturaliste.
     *
     * **Il ne remplace pas l'index unique, il le double.** L'index reste ce qui
     * garantit la règle sous deux demandes simultanées ; ce contrôle-ci sert à
     * rendre un refus métier propre dans le cas courant, sans laisser la
     * violation de contrainte remonter jusqu'à Doctrine, qui ferme son
     * gestionnaire d'entités et rend la suite de la requête inutilisable.
     *
     * C'est la même construction que le non-cumul des codes : une contrainte en
     * base, doublée d'une vérification applicative pour le message.
     */
    private function refuserUnSecondNaturaliste(
        string $jour,
        string $heure,
        string $typeDeSortie,
    ): void {
        if ($typeDeSortie !== TypeDeSortie::BALEINES) {
            return;
        }

        foreach ($this->sorties->sortiesDuCreneau($jour, $heure) as $dejaProgrammee) {
            if ($dejaProgrammee->typeDeSortie() === TypeDeSortie::BALEINES) {
                throw new NaturalisteIndisponible(
                    'Une sortie baleines est déjà programmée sur ce créneau.'
                );
            }
        }
    }

    private function refuserLesBaleinesHorsSaison(string $jour, string $typeDeSortie): void
    {
        if ($typeDeSortie !== TypeDeSortie::BALEINES) {
            return;
        }

        if ($this->offre->estEnSaisonDesBaleines(FuseauDexploitation::instant($jour))) {
            return;
        }

        throw new HorsSaison(
            'Les sorties baleines ne sont proposées que du 15 juin au 31 octobre.'
        );
    }
}
