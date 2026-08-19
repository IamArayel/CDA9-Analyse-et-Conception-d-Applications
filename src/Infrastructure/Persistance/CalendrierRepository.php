<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\JourDeFermeture;
use App\Domaine\FuseauDexploitation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Les jours où rien n'est proposé (SPEC-ADMIN-04).
 *
 * Une fermeture marquée récurrente vaut pour toutes les années : le 25 décembre
 * ferme aussi bien en 2026 qu'en 2027. C'est une hypothèse d'équipe, le client
 * n'ayant pas été interrogé là-dessus.
 */
final class CalendrierRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function jourEstFerme(string $jour): bool
    {
        $moisEtJour = FuseauDexploitation::instant($jour)->format('m-d');

        foreach ($this->joursDeFermeture() as $fermeture) {
            if ($fermeture->estRecurrentAnnuel()) {
                if ($fermeture->date()->format('m-d') === $moisEtJour) {
                    return true;
                }

                continue;
            }

            if ($fermeture->date()->format('Y-m-d') === FuseauDexploitation::instant($jour)->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    /** @return list<JourDeFermeture> */
    public function joursDeFermeture(): array
    {
        return $this->entites->getRepository(JourDeFermeture::class)->findBy([], ['date' => 'ASC']);
    }

    public function parDate(string $jour): ?JourDeFermeture
    {
        return $this->entites->getRepository(JourDeFermeture::class)
            ->findOneBy(['date' => FuseauDexploitation::instant($jour)]);
    }

    public function ajouter(JourDeFermeture $jour): void
    {
        $this->entites->persist($jour);
        $this->entites->flush();
    }

    public function retirer(JourDeFermeture $jour): void
    {
        $this->entites->remove($jour);
        $this->entites->flush();
    }
}
