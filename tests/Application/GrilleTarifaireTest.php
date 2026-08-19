<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterUneReservation;
use App\Application\ModifierUnTarif;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-02 - modification de la grille tarifaire.
 *
 * Le montant est recopié sur la réservation, il n'est jamais relu dans la
 * grille : un tarif modifié ne peut donc pas rattraper une réservation déjà
 * payée. Il s'applique à partir de la réservation suivante.
 */
final class GrilleTarifaireTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1 et AC-2 : un tarif modifié ne change pas les réservations déjà
     * payées, et s'applique aux suivantes.
     */
    public function test_CASE_ADMIN_04_tarif_modifie_epargne_les_reservations_payees(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $dejaPayee = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        ($this->service(ModifierUnTarif::class))->executer(
            Reference::SORTIE_DAUPHINS,
            prixAdulte: Reference::euros(55),
            prixEnfant: Reference::DAUPHINS_PRIX_ENFANT,
        );

        self::assertSame(
            Reference::euros(100),
            $this->reservation($dejaPayee)->montantDu(),
            'la réservation déjà payée reste à 100 €, à l\'euro près',
        );

        $suivante = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
        );
        self::assertSame(
            Reference::euros(110),
            $this->reservation($suivante)->montantDu(),
            'le nouveau tarif s\'applique à partir de la réservation suivante',
        );
    }

    private function reservation(string $reference): VueDeReservation
    {
        return ($this->service(ConsulterUneReservation::class))->executer($reference);
    }
}
