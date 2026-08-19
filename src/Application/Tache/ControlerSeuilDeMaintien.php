<?php

declare(strict_types=1);

namespace App\Application\Tache;

use App\Domaine\Entite\Sortie;
use App\Domaine\Horloge;
use App\Domaine\Politique\SeuilDeMaintien;
use App\Domaine\PrestataireDePaiement;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Le contrôle des 24 heures (SPEC-BOOKING-03, REQ-002 et REQ-003).
 *
 * **C'est la seule annulation automatique de l'outil.** Toutes les autres sont
 * une décision du gérant. Une sortie qui n'atteint pas six inscrits vingt-quatre
 * heures avant son départ est annulée, et chaque client remboursé intégralement.
 *
 * Une privatisation n'y est pas soumise : le bateau est payé en entier, le seuil
 * n'a pas de sens. C'est une hypothèse d'équipe, le client ayant énoncé le seuil
 * pour une sortie ouverte à la vente.
 *
 * Aucun message n'est envoyé : le client n'a jamais été interrogé sur le texte
 * de cette annulation, et c'est déclaré comme trou plutôt que comblé au jugé.
 */
final class ControlerSeuilDeMaintien
{
    private const FENETRE = '+24 hours';

    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly PrestataireDePaiement $prestataire,
        private readonly SeuilDeMaintien $seuil,
    ) {
    }

    public function executer(): void
    {
        $maintenant = $this->horloge->maintenant();

        $aControler = $this->sorties->sortiesQuiPartentEntre(
            $maintenant,
            $maintenant->modify(self::FENETRE),
        );

        foreach ($aControler as $sortie) {
            $this->controler($sortie, $maintenant);
        }

        $this->entites->flush();
    }

    private function controler(Sortie $sortie, \DateTimeImmutable $maintenant): void
    {
        if ($sortie->estAnnulee() || $sortie->estPrivatisee()) {
            return;
        }

        $inscrits = $this->reservations->inscrits($sortie);
        $participants = 0;

        foreach ($inscrits as $reservation) {
            $participants += $reservation->nombreDeParticipants();
        }

        $decision = $this->seuil->decider(
            $participants,
            $sortie->creneau()->departPrevu(),
            $maintenant,
        );

        if ($decision->sortieEstMaintenue()) {
            return;
        }

        $sortie->annuler();

        foreach ($inscrits as $reservation) {
            $this->prestataire->rembourser($reservation->reference(), $reservation->montant());
            $reservation->annuler();
        }
    }
}
