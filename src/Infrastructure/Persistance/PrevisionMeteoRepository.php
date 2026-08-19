<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\PrevisionMeteo;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La prévision météo d'une journée, saisie par le gérant.
 *
 * Absente le plus souvent : le message de rappel part quand même, sans la ligne
 * de prévision. Un rappel muet vaut mieux qu'un rappel qui n'part pas.
 */
final class PrevisionMeteoRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function pourLeJour(DateTimeImmutable $jour): ?PrevisionMeteo
    {
        return $this->entites->getRepository(PrevisionMeteo::class)
            ->findOneBy(['date' => $jour->setTime(0, 0)]);
    }

    public function enregistrer(PrevisionMeteo $prevision): void
    {
        $this->entites->persist($prevision);
        $this->entites->flush();
    }
}
