<?php

declare(strict_types=1);

namespace App\Application\Envoi;

use App\Domaine\Entite\Notification;
use App\Domaine\Entite\Reservation;
use App\Domaine\Politique\FenetreDeReglement;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\PaiementRepository;
use DateTimeImmutable;

/**
 * Le courriel portant le lien de règlement du solde (SPEC-CANCEL-07).
 *
 * Comme `RappelDeSortie`, il part de deux endroits : la tâche programmée
 * l'envoie à 7h la veille, et la confirmation d'une réservation l'envoie
 * immédiatement si cette heure est déjà passée. Sans le second chemin, un client
 * réservant la veille à 11h ne recevrait jamais son lien (`CR-07/Q02`).
 *
 * **Deux différences avec le rappel, et elles comptent.** L'heure d'envoi ne
 * dépend pas du créneau : 7h la veille, que la sortie parte à 7h ou à 14h. Et le
 * canal est le **courriel seul** : le client a demandé un mail, et un lien de
 * paiement dans un SMS inviterait au hameçonnage. Cette seconde décision est une
 * hypothèse d'équipe, déclarée dans la spécification.
 *
 * **Aucun lien pour une réservation sans solde.** Un bon cadeau qui couvre le
 * prix ne laisse rien à régler, et envoyer un lien de paiement à zéro euro
 * ferait douter le client de ce qu'il a déjà payé.
 */
final class LienDeReglement
{
    public function __construct(
        private readonly EnvoyerUnMessage $envoi,
        private readonly PaiementRepository $paiements,
        private readonly EtatDuReglement $reglement,
        private readonly FenetreDeReglement $fenetre,
    ) {
    }

    public function envoyerSiDu(Reservation $reservation, DateTimeImmutable $maintenant): void
    {
        $sortie = $reservation->sortie();

        if ($sortie->estAnnulee() || !$reservation->estConfirmee()) {
            return;
        }

        $solde = $this->reglement->soldeDu($reservation, $this->paiements->verse($reservation));

        if ($solde === 0) {
            return;
        }

        $ouverture = $this->fenetre->ouverture($sortie->creneau()->departPrevu());

        if ($maintenant < $ouverture) {
            return;
        }

        $this->envoi->pour(
            $reservation,
            Notification::TYPE_LIEN_DE_REGLEMENT,
            $maintenant,
            ['solde_du' => (string) $solde],
            canaux: [Notification::CANAL_EMAIL],
        );
    }
}
