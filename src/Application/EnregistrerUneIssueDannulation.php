<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\ChoixAnnulation;
use App\Domaine\Entite\Reservation;
use App\Domaine\Horloge;
use App\Domaine\IssueDannulation;
use App\Domaine\PrestataireDePaiement;
use App\Domaine\ResultatDissue;
use App\Infrastructure\Persistance\ChoixAnnulationRepository;
use App\Infrastructure\Persistance\CodeRepository;
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
 * Le montant est celui que le gérant saisit : **le barème dégressif n'est nulle
 * part dans le code**, il reste à son appréciation. Sans saisie, la totalité est
 * proposée, ce qui satisfait la règle de l'AC-4 sans qu'aucune branche ne soit
 * nécessaire : après une alerte météo, le gérant ne saisit rien et le client est
 * remboursé intégralement, le risque venant du gérant et non de lui.
 */
final class EnregistrerUneIssueDannulation
{
    public function __construct(
        private readonly Horloge $horloge,
        private readonly EntityManagerInterface $entites,
        private readonly ReservationRepository $reservations,
        private readonly ChoixAnnulationRepository $choix,
        private readonly CodeRepository $codes,
        private readonly PrestataireDePaiement $prestataire,
        private readonly EmettreUnAvoir $emettreUnAvoir,
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
     * Sans montant saisi, la totalité.
     *
     * Aucune branche n'est nécessaire pour l'alerte météo, et c'est volontaire :
     * le barème dégressif n'est **pas** dans le code, le gérant l'applique en
     * saisissant un montant. La règle « l'alerte interdit toute retenue » se
     * traduit donc par « le gérant ne saisit rien », et la totalité est
     * proposée. Y coder une branche laisserait croire que le barème existe
     * quelque part.
     */
    private function montantParDefaut(Reservation $reservation): int
    {
        return $reservation->montant();
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

    private function rembourser(Reservation $reservation, int $montant): ?string
    {
        if ($reservation->estConfirmee()) {
            $this->prestataire->rembourser($reservation->reference(), $montant);
        }

        return null;
    }
}
