<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Parametre;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Les réglages de l'espace de gestion, en une seule ligne.
 *
 * S'il n'y en a aucune, une ligne aux valeurs par défaut est créée : l'outil
 * doit fonctionner avant que le gérant n'ait ouvert l'écran des horaires.
 */
final class ParametreRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function reglages(): Parametre
    {
        $parametre = $this->entites->getRepository(Parametre::class)->findOneBy([]);

        if ($parametre === null) {
            $parametre = new Parametre();
            $this->entites->persist($parametre);
            $this->entites->flush();
        }

        return $parametre;
    }

    public function enregistrer(Parametre $parametre): void
    {
        $this->entites->persist($parametre);
        $this->entites->flush();
    }
}
