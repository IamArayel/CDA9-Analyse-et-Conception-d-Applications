<?php

declare(strict_types=1);

namespace App\Application\Tache;

use App\Domaine\Entite\Avoir;
use App\Domaine\Entite\BonCadeau;
use App\Domaine\Horloge;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La purge des données personnelles (SPEC-NFR-04).
 *
 * Trois mois après la sortie, les coordonnées du client sont effacées. La
 * valeur vient de l'équipe, faute de réponse du client, et le point de départ
 * exact reste la question 4 du §11.
 *
 * **L'exception compte autant que la règle.** Un bon cadeau vivant échappe à la
 * purge tant qu'il n'a pas expiré : sans elle, un bon valable un an deviendrait
 * inutilisable au bout d'un trimestre. C'est la contradiction relevée en revue
 * du modèle de données, et elle se voit ici, dans le code, plutôt que dans un
 * commentaire de spécification.
 *
 * La purge **anonymise**, elle ne supprime pas : le planning passé et les
 * montants restent lisibles, le client n'est plus identifiable.
 */
final class PurgerLesDonneesPersonnelles
{
    private const DELAI = '-3 months';

    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
    ) {
    }

    public function executer(): void
    {
        $maintenant = $this->horloge->maintenant();

        $this->purgerLesReservations($maintenant->modify(self::DELAI));
        $this->purgerLesCodesExpires($maintenant);

        $this->entites->flush();
    }

    private function purgerLesReservations(\DateTimeImmutable $limite): void
    {
        $sorties = $this->sorties->sortiesQuiPartentEntre(
            $limite->modify('-10 years'),
            $limite,
        );

        foreach ($sorties as $sortie) {
            foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
                $reservation->anonymiser();
            }
        }
    }

    /**
     * Un code n'est anonymisé qu'**après** son expiration : jusque-là, son
     * bénéficiaire doit rester joignable pour pouvoir s'en servir.
     */
    private function purgerLesCodesExpires(\DateTimeImmutable $maintenant): void
    {
        foreach ([BonCadeau::class, Avoir::class] as $classe) {
            foreach ($this->entites->getRepository($classe)->findAll() as $code) {
                if ($maintenant > $code->dateDexpiration()) {
                    $code->anonymiser();
                }
            }
        }
    }
}
