<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\FermetureDesReservations;
use App\Domaine\Service\CalculDeLetatDuDepart;
use App\Domaine\Service\CalculDuMontant;
use App\Domaine\VueDuDepart;
use App\Infrastructure\Persistance\SortieRepository;
use App\Infrastructure\Persistance\TarifRepository;
use InvalidArgumentException;

/**
 * Ce qu'un départ donne à voir, pour le panneau « Votre départ » du formulaire
 * de réservation (SPEC-BOOKING-01).
 *
 * Même construction que `ConsulterLeCalendrier::departsDuJour()`, pour une
 * seule sortie déjà identifiée plutôt qu'un créneau entier.
 */
final class ConsulterUnDepart
{
    public function __construct(
        private readonly SortieRepository $sorties,
        private readonly ConsulterLesPlacesDisponibles $placesDisponibles,
        private readonly FermetureDesReservations $fermeture,
        private readonly CalculDeLetatDuDepart $etatDuDepart,
        private readonly Horloge $horloge,
        private readonly TarifRepository $tarifs,
    ) {
    }

    public function executer(string $sortie): VueDuDepart
    {
        $laSortie = $this->sorties->parReference($sortie);

        if ($laSortie === null) {
            throw new InvalidArgumentException(sprintf('Aucune sortie « %s ».', $sortie));
        }

        return $this->vueDe($laSortie);
    }

    private function vueDe(Sortie $sortie): VueDuDepart
    {
        $maintenant = $this->horloge->maintenant();
        $restantes = $this->placesDisponibles->pour((string) $sortie->id());
        $montant = new CalculDuMontant($this->tarifs->grille());

        return new VueDuDepart(
            $sortie->creneau()->heureDeDepart(),
            $sortie->typeDeSortie(),
            $sortie->bateau()->nom(),
            (int) $sortie->id(),
            $this->etatDuDepart->pour($sortie, $restantes, $maintenant),
            $restantes,
            $this->fermeture->fermetureDe($sortie->creneau()->departPrevu()),
            $montant->pour($sortie->typeDeSortie(), 1, 0),
            $montant->pour($sortie->typeDeSortie(), 0, 1),
        );
    }
}
