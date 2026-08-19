<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\Domaine\Entite\Bateau;
use App\Domaine\Entite\Creneau;
use App\Domaine\Entite\Sortie;
use App\Domaine\NaturalisteIndisponible;
use App\Domaine\FuseauDexploitation;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Accès aux sorties, aux créneaux et aux bateaux.
 *
 * `enregistrer()` porte une responsabilité particulière : **traduire en refus
 * métier l'échec de la contrainte d'unicité** qui porte la règle du naturaliste
 * (architecture.md §3). C'est la base qui décide, pas un contrôle applicatif
 * que deux demandes simultanées contourneraient.
 */
final class SortieRepository
{
    public function __construct(private readonly EntityManagerInterface $entites)
    {
    }

    public function parReference(string $reference): ?Sortie
    {
        return $this->entites->find(Sortie::class, (int) $reference);
    }

    public function bateau(string $nom): ?Bateau
    {
        return $this->entites->getRepository(Bateau::class)->findOneBy(['nom' => $nom]);
    }

    /** Le créneau existant, ou un créneau neuf s'il n'y en avait pas. */
    public function creneauOuNouveau(string $jour, string $heure): Creneau
    {
        $creneau = $this->creneau($jour, $heure);

        if ($creneau === null) {
            $creneau = new Creneau($this->instant($jour), $this->instant('1970-01-01 '.$heure));
            $this->entites->persist($creneau);
        }

        return $creneau;
    }

    public function creneau(string $jour, string $heure): ?Creneau
    {
        return $this->entites->createQueryBuilder()
            ->select('c')
            ->from(Creneau::class, 'c')
            ->where('c.date = :jour')
            ->andWhere('c.heureDeDepart = :heure')
            ->setParameter('jour', $this->instant($jour))
            ->setParameter('heure', $this->instant('1970-01-01 '.$heure))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Les sorties d'un créneau, une par bateau engagé.
     *
     * @return list<Sortie>
     */
    public function sortiesDuCreneau(string $jour, string $heure): array
    {
        $creneau = $this->creneau($jour, $heure);

        if ($creneau === null) {
            return [];
        }

        return $this->entites->getRepository(Sortie::class)->findBy(['creneau' => $creneau]);
    }

    /**
     * Toutes les sorties d'une journée, quel que soit leur créneau.
     *
     * @return list<Sortie>
     */
    public function sortiesDuJour(string $jour): array
    {
        return $this->entites->createQueryBuilder()
            ->select('s')
            ->from(Sortie::class, 's')
            ->join('s.creneau', 'c')
            ->where('c.date = :jour')
            ->setParameter('jour', $this->instant($jour))
            ->getQuery()
            ->getResult();
    }

    /**
     * Les heures de départ d'une journée dont au moins une sortie est annulée.
     *
     * @return list<string>
     */
    public function heuresAnnulees(string $jour): array
    {
        $sorties = $this->entites->createQueryBuilder()
            ->select('s')
            ->from(Sortie::class, 's')
            ->join('s.creneau', 'c')
            ->where('c.date = :jour')
            ->setParameter('jour', $this->instant($jour))
            ->getQuery()
            ->getResult();

        $heures = [];

        foreach ($sorties as $sortie) {
            if ($sortie->estAnnulee()) {
                $heures[] = $sortie->creneau()->heureDeDepart();
            }
        }

        return array_values(array_unique($heures));
    }

    /**
     * Les sorties dont le départ tombe dans une fenêtre donnée.
     *
     * L'heure de départ n'est pas une colonne : elle se compose de la date du
     * créneau et de son heure. La sélection large se fait donc en base, et le
     * tri fin en mémoire, ce que la volumétrie attendue rend sans conséquence.
     *
     * @return list<Sortie>
     */
    public function sortiesQuiPartentEntre(DateTimeImmutable $debut, DateTimeImmutable $fin): array
    {
        $candidates = $this->entites->createQueryBuilder()
            ->select('s')
            ->from(Sortie::class, 's')
            ->join('s.creneau', 'c')
            ->where('c.date BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut->modify('-1 day'))
            ->setParameter('fin', $fin->modify('+1 day'))
            ->getQuery()
            ->getResult();

        return array_values(array_filter(
            $candidates,
            static function (Sortie $sortie) use ($debut, $fin): bool {
                $depart = $sortie->creneau()->departPrevu();

                return $depart >= $debut && $depart <= $fin;
            },
        ));
    }

    /**
     * @throws NaturalisteIndisponible si une sortie baleines occupe déjà le
     *                                 créneau : c'est l'index unique sur la
     *                                 colonne générée qui le dit, pas nous
     */
    public function enregistrer(Sortie $sortie): void
    {
        $this->entites->persist($sortie);

        try {
            $this->entites->flush();
        } catch (UniqueConstraintViolationException $collision) {
            throw new NaturalisteIndisponible(
                'Une sortie baleines est déjà programmée sur ce créneau.',
                previous: $collision,
            );
        }
    }

    private function instant(string $expression): DateTimeImmutable
    {
        return FuseauDexploitation::instant($expression);
    }
}
