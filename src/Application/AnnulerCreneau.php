<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Envoi\EnvoyerUnMessage;
use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\CalendrierDesEnvois;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDannulation;
use App\Domaine\StatutDeReservation;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ParametreRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Annuler un créneau, sur décision du gérant (SPEC-CANCEL-02 et 04).
 *
 * **L'alerte préalable n'est pas un passage obligé** : la météo peut se
 * dégrader en quelques heures, et l'exiger empêcherait d'annuler un départ du
 * matin décidé la veille au soir.
 *
 * Le remboursement est **intégral et sans alternative**. Le triptyque report,
 * avoir, remboursement n'existe que pour une annulation demandée par le client :
 * c'est la correction du 2026-08-14, après une transcription erronée de
 * CR-02/Q04.
 *
 * Annuler deux fois est un geste sans effet, pas une faute, et surtout ne
 * produit ni doublon d'envoi ni doublon de remboursement.
 */
final class AnnulerCreneau
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
        private readonly PrestataireDePaiement $prestataire,
        private readonly EnvoyerUnMessage $envoi,
        private readonly CalendrierDesEnvois $calendrier,
        private readonly ParametreRepository $parametres,
    ) {
    }

    public function executer(string $jour, string $heure): ResultatDannulation
    {
        $sorties = $this->sorties->sortiesDuCreneau($jour, $heure);
        $maintenant = $this->horloge->maintenant();

        if ($sorties === []) {
            return ResultatDannulation::refusee(ResultatDannulation::MOTIF_CRENEAU_DEJA_PASSE);
        }

        $depart = $sorties[0]->creneau()->departPrevu();

        if ($maintenant >= $depart) {
            return ResultatDannulation::refusee(ResultatDannulation::MOTIF_CRENEAU_DEJA_PASSE);
        }

        $aAnnuler = array_values(array_filter(
            $sorties,
            static fn (Sortie $sortie): bool => !$sortie->estAnnulee(),
        ));

        if ($aAnnuler === []) {
            return ResultatDannulation::sansEffet();
        }

        foreach ($aAnnuler as $sortie) {
            $this->annulerEtRembourser($sortie);
        }

        $this->entites->flush();
        $this->confirmerSiLheureEstPassee($aAnnuler, $depart, $maintenant);

        return ResultatDannulation::acceptee();
    }

    /**
     * Une réservation non payée ne donne lieu à aucun remboursement, et son
     * immobilisation est libérée : aucun montant nul n'est envoyé au
     * prestataire (SPEC-CANCEL-04 AC-5). Le test du versé suffit à le garantir,
     * une réservation jamais payée n'ayant aucune écriture.
     */
    private function annulerEtRembourser(Sortie $sortie): void
    {
        $sortie->annuler();

        foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
            if ($reservation->statut() === StatutDeReservation::ANNULEE) {
                continue;
            }

            // Le versé, et non le prix : depuis REQ-108 le client n'a réglé
            // qu'un acompte, sauf s'il a soldé en ligne entre-temps. Rendre le
            // prix rembourserait de l'argent jamais encaissé.
            $verse = $this->paiements->verse($reservation);

            if ($verse > 0) {
                $this->prestataire->rembourser($reservation->reference(), $verse);
            }

            $reservation->annuler();
        }
    }

    /**
     * Une annulation décidée après le repère des deux heures part
     * immédiatement, au lieu de n'être jamais envoyée.
     *
     * @param list<Sortie> $sorties
     */
    private function confirmerSiLheureEstPassee(
        array $sorties,
        DateTimeImmutable $depart,
        DateTimeImmutable $maintenant,
    ): void {
        $heureDenvoi = $this->calendrier->confirmationDannulation(
            $depart,
            $this->parametres->reglages()->delaiDeConfirmationEnHeures(),
        );

        if ($maintenant < $heureDenvoi) {
            return;
        }

        foreach ($sorties as $sortie) {
            foreach ($this->reservations->deLaSortie($sortie) as $reservation) {
                $this->envoi->pour(
                    $reservation,
                    Notification::TYPE_CONFIRMATION_ANNULATION,
                    $maintenant,
                );
            }
        }
    }
}
