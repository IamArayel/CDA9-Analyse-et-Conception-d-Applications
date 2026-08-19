<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use App\Domaine\TypeDeSortie;
use DateTimeImmutable;

/**
 * Ce qui est proposé un jour donné (SPEC-BOOKING-02).
 *
 * La saison des baleines court du 15 juin au 31 octobre, **bornes incluses**.
 * Les trois créneaux, eux, sont proposés tous les jours d'ouverture : c'est le
 * type de sortie qui varie avec la saison, jamais l'horaire.
 *
 * Les jours de fermeture ne sont pas traités ici : ils sont une donnée du
 * gérant, lue par la couche application, et non une règle de calendrier.
 */
final class OffreDeCreneaux
{
    public const OUVERTURE_DE_LA_SAISON = 615;
    public const FERMETURE_DE_LA_SAISON = 1031;

    public const CRENEAUX = ['07:00', '10:00', '14:00'];

    /** @return list<string> */
    public function typesDeSortieProposes(DateTimeImmutable $jour): array
    {
        return $this->estEnSaisonDesBaleines($jour)
            ? TypeDeSortie::TOUS
            : [TypeDeSortie::DAUPHINS];
    }

    /** @return list<string> */
    public function creneauxProposes(DateTimeImmutable $jour): array
    {
        return self::CRENEAUX;
    }

    public function estEnSaisonDesBaleines(DateTimeImmutable $jour): bool
    {
        $moisEtJour = (int) $jour->format('md');

        return $moisEtJour >= self::OUVERTURE_DE_LA_SAISON
            && $moisEtJour <= self::FERMETURE_DE_LA_SAISON;
    }
}
