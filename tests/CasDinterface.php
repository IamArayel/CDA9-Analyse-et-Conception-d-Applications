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
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Socle commun des tests de niveau interface (`WebTestCase`), même montage que
 * `CasDapplication` : les trois doublures de docs/strategie-de-test.md §9, une
 * transaction annulée en fin de cas, et `MondeDeTest` pour préparer les écrans
 * avec les services applicatifs réels plutôt qu'un raccourci de test.
 */
abstract class CasDinterface extends WebTestCase
{
    protected KernelBrowser $client;
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

        $this->client = static::createClient();
        // Sans cela, chaque requête reboote le noyau : un second appel du même
        // cas de test perdrait la transaction et les doublures posées ici, et
        // reprendrait sur un état de base différent de celui du premier appel.
        $this->client->disableReboot();
        $conteneur = static::getContainer();

        $this->horloge = new HorlogeFigee($this->instantInitial());
        $this->messages = new EnvoisEnregistres();
        $this->paiement = new PaiementSimule();

        $conteneur->set(Horloge::class, $this->horloge);
        $conteneur->set(Notificateur::class, $this->messages);
        $conteneur->set(PrestataireDePaiement::class, $this->paiement);

        $this->entites = $conteneur->get(EntityManagerInterface::class);
        $this->entites->getConnection()->beginTransaction();

        $this->monde = new MondeDeTest($conteneur, $this->entites, $this->horloge);
        $this->monde->chargerLeJeuDeReference();
    }

    protected function tearDown(): void
    {
        if (isset($this->entites) && $this->entites->getConnection()->isTransactionActive()) {
            $this->entites->getConnection()->rollBack();
        }

        parent::tearDown();
    }
}
