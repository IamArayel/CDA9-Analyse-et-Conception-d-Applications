<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\ChoixAnnulation;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\IssueDannulation;
use App\Domaine\Politique\RetenueDannulation;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDissue;
use App\Domaine\Service\EtatDuReglement;
use App\Infrastructure\Persistance\ChoixAnnulationRepository;
use App\Infrastructure\Persistance\CodeRepository;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Enregistrer l'issue d'une annulation **demandée par le client**
 * (SPEC-ADMIN-06).
 *
 * Ce triptyque n'existe que là. Une annulation décidée par le gérant, météo
 * comprise, rembourse intégralement sans alternative : c'est la correction du
 * 2026-08-14, après une transcription erronée de CR-02/Q04.
 *
 * **Seul l'avoir produit un code**, et c'est sa seule origine.
 *
 * **Le barème est calculé, et non plus laissé à l'appréciation du gérant.** Il
 * l'était jusqu'à CR-07, faute de connaître les paliers ; le client les a donnés
 * en Q05, et `RetenueDannulation` les porte. Le gérant peut toujours saisir un
 * montant, qui l'emporte alors sur le calcul : c'est sa marge de geste
 * commercial, pas la règle.
 *
 * **L'alerte météo l'emporte sur le barème** (AC-4). Le risque vient du gérant,
 * pas du client, et il n'y a pas lieu de retenir quoi que ce soit : le versé
 * repart en entier. Cette branche est nécessaire depuis que le barème existe,
 * là où auparavant « le gérant ne saisit rien » suffisait à l'obtenir.
 */
final class EnregistrerUneIssueDannulation
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly ReservationRepository $reservations,
        private readonly ChoixAnnulationRepository $choix,
        private readonly CodeRepository $codes,
        private readonly PaiementRepository $paiements,
        private readonly PrestataireDePaiement $prestataire,
        private readonly EmettreUnAvoir $emettreUnAvoir,
        private readonly EtatDuReglement $reglement,
        private readonly RetenueDannulation $retenue,
    ) {
    }

    /** @param int|null $montant en centimes, ou null pour laisser la règle décider */
    public function executer(
        string $reference,
        IssueDannulation $issue,
        ?int $montant = null,
    ): ResultatDissue {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        if ($this->choix->pourLaReservation($reservation) !== null) {
            return ResultatDissue::refusee(ResultatDissue::MOTIF_ISSUE_DEJA_ENREGISTREE);
        }

        $montantRetenu = $montant ?? $this->montantParDefaut($reservation);
        $code = $this->appliquerLissue($reservation, $issue, $montantRetenu);

        $choix = new ChoixAnnulation($reservation, $issue, $this->horloge->maintenant());

        if ($code !== null) {
            $choix->rattacherLavoir($this->codes->avoir($code));
        }

        $this->choix->enregistrer($choix);

        $reservation->annuler();
        $this->entites->flush();

        return ResultatDissue::acceptee($montantRetenu, $code);
    }

    /**
     * Sans montant saisi, le barème.
     *
     * Le versé est le plafond de ce qui peut revenir au client dans tous les
     * cas : on ne rend pas de l'argent qui n'a jamais été encaissé, et une
     * retenue supérieure au versé ne se réclame pas, elle se plafonne à zéro.
     */
    private function montantParDefaut(Reservation $reservation): int
    {
        $verse = $this->paiements->verse($reservation);

        if ($reservation->sortie()->dateDeMiseEnAlerte() !== null) {
            return $verse;
        }

        return $this->retenue->rembourse(
            $verse,
            $this->reglement->montantACouvrir($reservation),
            $reservation->sortie()->creneau()->departPrevu(),
            $this->horloge->maintenant(),
        );
    }

    /** @return string|null le code produit, pour la seule issue « avoir » */
    private function appliquerLissue(
        Reservation $reservation,
        IssueDannulation $issue,
        int $montant,
    ): ?string {
        return match ($issue) {
            IssueDannulation::AVOIR => $this->emettreUnAvoir->executer($montant, [
                'email' => $reservation->email(),
            ]),
            IssueDannulation::REMBOURSEMENT => $this->rembourser($reservation, $montant),
            IssueDannulation::REPORT => null,
        };
    }

    /**
     * Un remboursement nul n'appelle personne : le client garde son acompte
     * retenu, et rien ne lui est réclamé (SPEC-ADMIN-06 AC-7).
     */
    private function rembourser(Reservation $reservation, int $montant): ?string
    {
        if ($montant > 0 && $reservation->estConfirmee()) {
            $this->prestataire->rembourser($reservation->reference(), $montant);
        }

        return null;
    }
}
