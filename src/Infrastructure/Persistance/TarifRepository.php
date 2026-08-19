<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Tarif;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La grille tarifaire, telle qu'elle est au moment où on la lit.
 *
 * Elle change (SPEC-ADMIN-02), et c'est précisément pourquoi le montant d'une
 * réservation est recopié sur elle à sa validation plutôt que relu ici.
 */
final class TarifRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function parTypeDeSortie(string $typeDeSortie): ?Tarif
    {
        return $this->entites->getRepository(Tarif::class)
            ->findOneBy(['typeDeSortie' => $typeDeSortie]);
    }

    /**
     * La grille sous la forme qu'attend le calcul du montant.
     *
     * @return array<string, array{adulte: int, enfant: int}>
     */
    public function grille(): array
    {
        $grille = [];

        foreach ($this->entites->getRepository(Tarif::class)->findAll() as $tarif) {
            $grille[$tarif->typeDeSortie()] = [
                'adulte' => $tarif->prixAdulte(),
                'enfant' => $tarif->prixEnfant(),
            ];
        }

        return $grille;
    }

    public function enregistrer(Tarif $tarif): void
    {
        $this->entites->persist($tarif);
        $this->entites->flush();
    }
}
