<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Sortie;
use App\Infrastructure\Persistance\BateauRepository;
use App\Infrastructure\Persistance\ReservationRepository;
use App\Infrastructure\Persistance\SortieRepository;
use InvalidArgumentException;

/**
 * Les formules proposées sur un bateau à un créneau donné (SPEC-BOOKING-05,
 * SPEC-ADMIN-05).
 *
 * La privatisation n'apparaît qu'à deux conditions : le bateau a un forfait, et
 * aucune place n'a encore été vendue sur ce créneau. Un bateau déjà entamé ne
 * se privatise plus, et le gérant ne réattribue rien automatiquement.
 */
final class ConsulterLesFormules
{
    public const INDIVIDUELLE = 'individuelle';
    public const PRIVATISATION = 'privatisation';

    public function __construct(
        private readonly SortieRepository $sorties,
        private readonly ReservationRepository $reservations,
        private readonly BateauRepository $bateaux,
    ) {
    }

    /** @return list<string> */
    public function pour(string $jour, string $heure, string $bateau): array
    {
        $navire = $this->bateaux->parNom($bateau);

        if ($navire === null) {
            throw new InvalidArgumentException(sprintf('Aucun bateau nommé « %s ».', $bateau));
        }

        if (!$navire->estPrivatisable() || $this->desPlacesSontVendues($jour, $heure, $bateau)) {
            return [self::INDIVIDUELLE];
        }

        return [self::INDIVIDUELLE, self::PRIVATISATION];
    }

    /** En centimes, ou null si le bateau n'est pas privatisable. */
    public function forfaitDePrivatisation(string $bateau): ?int
    {
        return $this->bateaux->parNom($bateau)?->forfaitDePrivatisation();
    }

    private function desPlacesSontVendues(string $jour, string $heure, string $bateau): bool
    {
        $sortie = $this->sortieDuBateau($jour, $heure, $bateau);

        if ($sortie === null) {
            return false;
        }

        return $this->reservations->inscrits($sortie) !== [];
    }

    private function sortieDuBateau(string $jour, string $heure, string $bateau): ?Sortie
    {
        foreach ($this->sorties->sortiesDuCreneau($jour, $heure) as $sortie) {
            if ($sortie->bateau()->nom() === $bateau) {
                return $sortie;
            }
        }

        return null;
    }
}
