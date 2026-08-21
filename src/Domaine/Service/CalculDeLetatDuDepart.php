<?php

declare(strict_types=1);

namespace App\Domaine\Service;

use App\Domaine\Entite\Sortie;
use App\Domaine\EtatDuDepart;
use App\Domaine\Politique\FermetureDesReservations;
use App\Domaine\StatutDeSortie;
use DateTimeImmutable;

/**
 * L'état à afficher pour un départ sur le calendrier public (SPEC-BOOKING-04).
 *
 * La fermeture des ventes et la capacité atteinte interdisent la vente et
 * priment sur l'alerte météo, qui ne fait qu'informer sans jamais rien retirer
 * (`StatutDeSortie::EN_ALERTE`).
 */
final class CalculDeLetatDuDepart
{
    public function __construct(
        private readonly FermetureDesReservations $fermeture = new FermetureDesReservations(),
    ) {
    }

    public function pour(Sortie $sortie, int $placesRestantes, DateTimeImmutable $maintenant): EtatDuDepart
    {
        if (!$this->fermeture->estReservable($sortie->creneau()->departPrevu(), $maintenant)) {
            return EtatDuDepart::FERME;
        }

        if ($placesRestantes <= 0) {
            return EtatDuDepart::COMPLET;
        }

        if ($sortie->statut() === StatutDeSortie::EN_ALERTE) {
            return EtatDuDepart::ALERTE;
        }

        return EtatDuDepart::DISPONIBLE;
    }
}
