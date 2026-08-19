<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domaine\Horloge;
use App\Domaine\Notificateur;
use App\Domaine\PrestataireDePaiement;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\Doublures\HorlogeFigee;
use App\Tests\Doublures\PaiementSimule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Socle commun des tests de niveau application.
 *
 * Il monte les trois doublures de docs/strategie-de-test.md §9, et elles
 * seules : l'horloge figée, le prestataire de paiement et les envois. **Tout le
 * reste est réel, base de données comprise**, parce que c'est elle qui porte
 * deux règles métier, l'unicité du naturaliste et le non-cumul des codes.
 *
 * Chaque cas s'exécute dans une transaction annulée à la fin : il repart d'une
 * base propre sans qu'aucun test n'ait à nettoyer derrière lui, et sans le coût
 * d'une reconstruction du schéma.
 */
abstract class CasDapplication extends KernelTestCase
{
    protected HorlogeFigee $horloge;
    protected EnvoisEnregistres $messages;
    protected PaiementSimule $paiement;
    protected MondeDeTest $monde;
    protected EntityManagerInterface $entites;

    /** L'instant où commence le cas, « Et que nous sommes le … à …h… ». */
    abstract protected function instantInitial(): string;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $conteneur = static::getContainer();

        $this->horloge = new HorlogeFigee($this->instantInitial());
        $this->messages = new EnvoisEnregistres();
        $this->paiement = new PaiementSimule();

        // Les trois seuls points où la production est remplacée.
        $conteneur->set(Horloge::class, $this->horloge);
        $conteneur->set(Notificateur::class, $this->messages);
        $conteneur->set(PrestataireDePaiement::class, $this->paiement);

        $this->entites = $conteneur->get(EntityManagerInterface::class);
        $this->entites->getConnection()->beginTransaction();

        $this->monde = new MondeDeTest($conteneur, $this->entites, $this->horloge);
        $this->monde->chargerLeJeuDeReference();
    }

    /**
     * Le service de cas d'usage sous test, pris dans le conteneur.
     *
     * Les cas ne l'instancient pas eux-mêmes : un service applicatif reçoit ses
     * dépôts, et un `new` dans un test figerait une liste de dépendances que le
     * cas n'a aucune raison de connaître.
     *
     * @template T of object
     *
     * @param class-string<T> $service
     *
     * @return T
     */
    protected function service(string $service): object
    {
        return static::getContainer()->get($service);
    }

    protected function tearDown(): void
    {
        if (isset($this->entites) && $this->entites->getConnection()->isTransactionActive()) {
            $this->entites->getConnection()->rollBack();
        }

        parent::tearDown();
    }
}
