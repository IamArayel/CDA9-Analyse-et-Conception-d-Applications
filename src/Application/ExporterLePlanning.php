<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\DocumentImprimable;
use App\Domaine\Entite\Reservation;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;

/**
 * L'export imprimable du planning d'une journée (SPEC-ADMIN-03).
 *
 * **Le planning liste ce qui embarque**, pas ce qui est en cours d'achat : une
 * réservation immobilisée mais non payée n'y figure pas.
 *
 * Une journée sans réservation produit un document, jamais une erreur : hors
 * saison c'est un résultat métier normal, et le gérant doit pouvoir l'imprimer
 * sans se demander si l'outil a échoué.
 *
 * **La colonne `solde_regle` est ce que le gérant lit sur le quai** : elle lui
 * dit qui doit encore sortir sa carte, et c'est elle qui déclenche le pointage
 * de `SPEC-ADMIN-07`.
 *
 * Ce service produit le **contenu** du document, groupé et ordonné. Sa mise en
 * page PDF appartient à la couche Interface et n'est pas encore écrite : c'est
 * déclaré dans docs/traceability-trous.md.
 */
final class ExporterLePlanning
{
    public function __construct(
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
    ) {
    }

    public function executer(string $jour): DocumentImprimable
    {
        $parCreneau = [];

        foreach ($this->sorties->sortiesDuJour($jour) as $sortie) {
            foreach ($this->reservations->inscrits($sortie) as $reservation) {
                $parCreneau[$sortie->creneau()->heureDeDepart()][] = $this->ligne(
                    $sortie->bateau()->nom(),
                    $reservation,
                );
            }
        }

        ksort($parCreneau);

        $lignes = [];

        foreach ($parCreneau as $heure => $lignesDuCreneau) {
            foreach ($lignesDuCreneau as $ligne) {
                $lignes[] = ['creneau' => $heure] + $ligne;
            }
        }

        return new DocumentImprimable(
            estUnPdf: true,
            creneaux: array_keys($parCreneau),
            lignes: $lignes,
            mentionneLabsenceDeReservation: $lignes === [],
        );
    }

    /** @return array<string, mixed> */
    private function ligne(string $bateau, Reservation $reservation): array
    {
        return [
            'bateau' => $bateau,
            'nom' => $reservation->nomClient(),
            'prenom' => $reservation->prenomClient(),
            'email' => $reservation->email(),
            'telephone_mobile' => $reservation->telephoneMobile(),
            'participants' => $reservation->nombreDeParticipants(),
            'solde_regle' => $this->reglement->soldeDu(
                $reservation,
                $this->paiements->verse($reservation),
            ) === 0,
        ];
    }
}
