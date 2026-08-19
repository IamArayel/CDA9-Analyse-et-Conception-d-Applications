<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Persistance\CodeRepository;
use App\Infrastructure\Persistance\ReservationRepository;

/**
 * Ce que l'application conserve d'un client (SPEC-NFR-04).
 *
 * **La liste est exactement celle du formulaire, ni plus ni moins.** Aucune
 * donnée bancaire n'y figure, et ce n'est pas un filtrage : elles ne transitent
 * jamais par l'application, la signature du prestataire ne transportant qu'une
 * référence et un montant.
 *
 * Une réservation anonymisée par la purge rend une liste vide : la ligne existe
 * encore, le client n'est plus identifiable.
 */
final class ConsulterLesDonneesConservees
{
    public function __construct(
        private readonly ReservationRepository $reservations,
        private readonly CodeRepository $codes,
    ) {
    }

    /** @return array<string, mixed> */
    public function pour(string $reference): array
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null || $reservation->estAnonymisee()) {
            return [];
        }

        return [
            'nom' => $reservation->nomClient(),
            'prenom' => $reservation->prenomClient(),
            'email' => $reservation->email(),
            'telephone_mobile' => $reservation->telephoneMobile(),
            'nombre_adultes' => $reservation->nombreDAdultes(),
            'nombre_enfants' => $reservation->nombreDEnfants(),
            'langue' => $reservation->langue(),
            'creneau' => $reservation->sortie()->creneau()->heureDeDepart(),
            'type_sortie' => $reservation->sortie()->typeDeSortie(),
        ];
    }

    /**
     * Ce qu'un bon cadeau ou un avoir conserve de son bénéficiaire.
     *
     * @return array<string, mixed>
     */
    public function pourUnCode(string $code): array
    {
        $porteur = $this->codes->bonCadeau($code) ?? $this->codes->avoir($code);

        if ($porteur === null || $porteur->emailBeneficiaire() === null) {
            return [];
        }

        return [
            'email' => $porteur->emailBeneficiaire(),
            'montant' => $porteur->montant(),
            'expire_le' => $porteur->dateDexpiration()->format('Y-m-d'),
        ];
    }
}
