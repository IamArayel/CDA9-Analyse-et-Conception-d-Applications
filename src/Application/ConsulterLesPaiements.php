<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\VueDePaiement;
use App\Infrastructure\Persistance\PaiementRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use InvalidArgumentException;

/**
 * Le journal des versements d'une réservation (SPEC-ADMIN-07 AC-3).
 *
 * **Les écritures rétractées y figurent.** C'est tout l'intérêt : une vue qui
 * ne montrerait que ce qui compte encore ne permettrait pas de reconstituer ce
 * qui s'est passé au quai, et c'est justement ce qu'on demandera au gérant en
 * cas de contestation.
 */
final class ConsulterLesPaiements
{
    public function __construct(
        private readonly ReservationRepository $reservations,
        private readonly PaiementRepository $paiements,
    ) {
    }

    /** @return list<VueDePaiement> dans l'ordre où les versements ont eu lieu */
    public function pour(string $reference): array
    {
        $reservation = $this->reservations->parReference($reference);

        if ($reservation === null) {
            throw new InvalidArgumentException(sprintf('Aucune réservation « %s ».', $reference));
        }

        return array_map(
            static fn ($paiement): VueDePaiement => new VueDePaiement(
                $paiement->type(),
                $paiement->montant(),
                $paiement->canal(),
                $paiement->datePaiement(),
                $paiement->estAnnule(),
            ),
            $this->paiements->pour($reservation),
        );
    }
}
