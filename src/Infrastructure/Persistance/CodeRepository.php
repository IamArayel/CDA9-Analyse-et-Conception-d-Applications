<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Avoir;
use App\Domaine\Entite\BonCadeau;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Accès aux bons cadeaux et aux avoirs.
 *
 * Deux tables, une seule porte d'entrée : depuis la v4, les deux dispositifs ne
 * diffèrent plus que par leur origine, et un code se cherche sans savoir lequel
 * on tient. Les deux tables sont maintenues par précaution si le gérant fait
 * évoluer son produit (mcd-mld.md §5, question 8 du §11).
 */
final class CodeRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function bonCadeau(string $code): ?BonCadeau
    {
        return $this->entites->getRepository(BonCadeau::class)->findOneBy(['code' => $code]);
    }

    public function avoir(string $code): ?Avoir
    {
        return $this->entites->getRepository(Avoir::class)->findOneBy(['code' => $code]);
    }

    public function enregistrer(BonCadeau|Avoir $code): void
    {
        $this->entites->persist($code);
        $this->entites->flush();
    }

    /** Un code lisible, sans ambiguïté à la dictée. */
    public function codeNeuf(): string
    {
        return strtoupper(bin2hex(random_bytes(6)));
    }

    /** @return list<BonCadeau> */
    public function tousLesBonsCadeaux(): array
    {
        return $this->entites->getRepository(BonCadeau::class)->findAll();
    }

    /** @return list<Avoir> */
    public function tousLesAvoirs(): array
    {
        return $this->entites->getRepository(Avoir::class)->findAll();
    }
}
