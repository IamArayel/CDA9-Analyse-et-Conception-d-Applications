<?php

declare(strict_types=1);

namespace App\Domaine\Service;

use App\Domaine\Entite\Reservation;
use App\Domaine\Politique\Acompte;

/**
 * Où en est une réservation de son règlement (SPEC-BOOKING-07, SPEC-BOOKING-12).
 *
 * Trois montants, et il faut les distinguer pour ne pas les confondre plus loin :
 *
 * - le **montant à couvrir** : le prix, diminué du bon cadeau ou de l'avoir
 *   appliqué. C'est ce qui doit réellement sortir de la poche du client ;
 * - le **versement d'entrée** : ce qu'il règle à la réservation. L'acompte de
 *   `REQ-108`, sauf si un code est en jeu, auquel cas `REQ-116` demande le solde
 *   de la différence en une fois : un acompte sur un reliquat de quelques euros
 *   n'aurait aucun sens ;
 * - le **solde dû** : ce qui reste, une fois retranché ce qui a été versé.
 *
 * Ce calcul vit ici et nulle part ailleurs. Quatre services en ont besoin, et
 * une seconde version qui divergerait ferait payer deux fois ou pas du tout.
 */
final class EtatDuReglement
{
    public function __construct(private readonly Acompte $acompte = new Acompte())
    {
    }

    /** En centimes : le prix, diminué du code appliqué. */
    public function montantACouvrir(Reservation $reservation): int
    {
        $code = $reservation->bonCadeau() ?? $reservation->avoir();

        if ($code === null) {
            return $reservation->montant();
        }

        return max(0, $reservation->montant() - $code->montant());
    }

    /**
     * En centimes : ce qui est demandé au moment de la réservation.
     *
     * Un code en jeu emporte le règlement de la différence en une seule fois
     * (`REQ-116`) ; sinon, l'acompte, au taux du type de sortie.
     */
    public function versementDentree(Reservation $reservation): int
    {
        $aCouvrir = $this->montantACouvrir($reservation);

        if ($reservation->bonCadeau() !== null || $reservation->avoir() !== null) {
            return $aCouvrir;
        }

        return $reservation->sortie()->estPrivatisee()
            ? $this->acompte->pourUnePrivatisation($aCouvrir)
            : $this->acompte->pourUneSortie($aCouvrir);
    }

    /**
     * En centimes : ce qui reste à régler.
     *
     * Vaut zéro pour une réservation annulée : un créneau qui ne partira pas ne
     * réclame plus rien (SPEC-BOOKING-12 AC-6).
     */
    public function soldeDu(Reservation $reservation, int $verse): int
    {
        if ($reservation->estAnnulee()) {
            return 0;
        }

        return max(0, $this->montantACouvrir($reservation) - $verse);
    }
}
