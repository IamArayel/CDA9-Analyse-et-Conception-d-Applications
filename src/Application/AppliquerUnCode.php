<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Avoir;
use App\Domaine\Entite\BonCadeau;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\Politique\ValiditeDunAvoir;
use App\Domaine\Politique\ValiditeDunCode;
use App\Domaine\ResultatDapplicationDunCode;
use App\Domaine\StatutDeCode;
use App\Infrastructure\Persistance\CodeRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Appliquer un bon cadeau ou un avoir à une réservation (SPEC-BOOKING-09 et 10).
 *
 * Deux refus, et un seul message pour trois causes. **Un code inexistant, déjà
 * utilisé ou expiré donnent le même motif** : les distinguer permettrait de
 * sonder les codes en observant la réponse (AC-5).
 *
 * Le non-cumul, lui, se dit franchement : le client sait qu'il a déjà un code,
 * il n'apprend rien en l'entendant. Il est par ailleurs porté par une contrainte
 * de la base, que cette vérification double sans remplacer.
 *
 * Le code n'est **pas consommé ici** : il l'est à la confirmation du paiement.
 * Un code posé sur une réservation jamais payée doit rester utilisable ailleurs.
 */
final class AppliquerUnCode
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly ReservationRepository $reservations,
        private readonly CodeRepository $codes,
        private readonly ValiditeDunCode $validiteDunBon,
        private readonly ValiditeDunAvoir $validiteDunAvoir,
    ) {
    }

    public function executer(string $reference, string $code): ResultatDapplicationDunCode
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        if ($reservation->porteDejaUnCode()) {
            return ResultatDapplicationDunCode::refuse(
                ResultatDapplicationDunCode::MOTIF_CODES_NON_CUMULABLES,
                $this->restantDu($reservation),
            );
        }

        $bon = $this->codes->bonCadeau($code);

        if ($bon !== null && $this->bonEstUtilisable($bon)) {
            $reservation->appliquerLeBonCadeau($bon);
            $this->entites->flush();

            return ResultatDapplicationDunCode::accepte($this->restantDu($reservation));
        }

        $avoir = $this->codes->avoir($code);

        if ($avoir !== null && $this->avoirEstUtilisable($avoir)) {
            $reservation->appliquerLavoir($avoir);
            $this->entites->flush();

            return ResultatDapplicationDunCode::accepte($this->restantDu($reservation));
        }

        return ResultatDapplicationDunCode::refuse(
            ResultatDapplicationDunCode::MOTIF_CODE_INVALIDE,
            $this->restantDu($reservation),
        );
    }

    private function restantDu(Reservation $reservation): int
    {
        $code = $reservation->bonCadeau() ?? $reservation->avoir();

        if ($code === null) {
            return $reservation->montant();
        }

        // Le surplus est perdu : le montant restant dû ne descend jamais sous
        // zéro, et aucun avoir n'est produit pour la différence.
        return max(0, $reservation->montant() - $code->montant());
    }

    private function bonEstUtilisable(BonCadeau $bon): bool
    {
        return $bon->statut() === StatutDeCode::DISPONIBLE
            && $this->validiteDunBon->estValide($bon->dateDachat(), $this->horloge->maintenant());
    }

    private function avoirEstUtilisable(Avoir $avoir): bool
    {
        return $this->validiteDunAvoir->estUtilisable(
            $avoir->statut(),
            $avoir->dateDemission(),
            $this->horloge->maintenant(),
        );
    }
}
