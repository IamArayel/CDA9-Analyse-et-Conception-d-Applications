<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterUnCode;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-10 - usage d'un code d'avoir.
 *
 * Un avoir n'est rattaché à aucun type de sortie : émis à la suite d'une sortie
 * dauphins, il s'applique sans réserve à une sortie baleines. Il se comporte
 * pour le reste comme un bon cadeau.
 */
final class AvoirTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-06-20 09:00';
    }

    /**
     * AC-1, AC-2 et AC-4 : un code d'avoir se déduit du montant, quel que soit
     * le type de sortie, et la différence reste à payer par carte.
     */
    public function test_CASE_BOOKING_33_avoir_deduit_quel_que_soit_le_type_de_sortie(): void
    {
        // Émis un mois plus tôt, à la suite d'une annulation demandée par le
        // client sur une sortie dauphins.
        $avoir = $this->monde->avoirEmis(Reference::euros(130), '2026-06-20');

        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_BALEINES,
        );

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
            enfants: 1,
        );

        $application = ($this->service(AppliquerUnCode::class))->executer($reservation, $avoir);

        self::assertTrue($application->estAccepte());
        self::assertSame(
            Reference::euros(40),
            $application->montantRestantDu(),
            '170 € moins 130 € : 40 € restent à payer par carte',
        );

        ($this->service(ConfirmerLePaiement::class))->executer($reservation);

        self::assertSame(
            Reference::euros(40),
            $this->paiement->montantEncaisse($reservation),
            'le montant demandé au prestataire est 40 €',
        );
        self::assertFalse(
            ($this->service(ConsulterUnCode::class))->executer($avoir)->estUtilisable(),
            'le code est marqué utilisé',
        );
    }
}
