<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Bateau;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La flotte (SPEC-ADMIN-05).
 *
 * Aucune suppression : retirer un bateau déjà engagé sur des sorties passées
 * effacerait le planning. Le client ne l'a pas demandé, et ce n'est pas prévu.
 */
final class BateauRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function parNom(string $nom): ?Bateau
    {
        return $this->entites->getRepository(Bateau::class)->findOneBy(['nom' => $nom]);
    }

    /** @return list<string> */
    public function noms(): array
    {
        return array_map(
            static fn (Bateau $bateau): string => $bateau->nom(),
            $this->entites->getRepository(Bateau::class)->findAll(),
        );
    }

    public function enregistrer(Bateau $bateau): void
    {
        $this->entites->persist($bateau);
        $this->entites->flush();
    }
}
