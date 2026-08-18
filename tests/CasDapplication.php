<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\Doublures\HorlogeFigee;
use App\Tests\Doublures\PaiementSimule;
use PHPUnit\Framework\TestCase;

/**
 * Socle commun des tests de niveau application.
 *
 * Il monte les trois doublures de docs/strategie-de-test.md §9, et elles
 * seules : l'horloge figée, le prestataire de paiement et les envois. Tout le
 * reste est réel.
 *
 * Tant que le socle technique n'est pas livré, cette classe étend TestCase.
 * Elle étendra KernelTestCase le jour où la base de données entrera dans le
 * jeu : c'est le seul fichier à changer, les cas eux-mêmes n'en savent rien.
 */
abstract class CasDapplication extends TestCase
{
    protected HorlogeFigee $horloge;
    protected EnvoisEnregistres $messages;
    protected PaiementSimule $paiement;
    protected MondeDeTest $monde;

    /** L'instant où commence le cas, « Et que nous sommes le … à …h… ». */
    abstract protected function instantInitial(): string;

    protected function setUp(): void
    {
        parent::setUp();

        $this->horloge = new HorlogeFigee($this->instantInitial());
        $this->messages = new EnvoisEnregistres();
        $this->paiement = new PaiementSimule();
        $this->monde = new MondeDeTest($this->horloge, $this->messages, $this->paiement);
    }
}
