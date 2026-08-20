<?php

declare(strict_types=1);

use App\Tests\BoutEnBout\ContexteDeParcours;
use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;

/*
 * Troisième niveau de test, cf. docs/strategie-de-test.md §9.
 *
 * Les trois scénarios de bout en bout sont écrits mais **non exécutables** :
 * leurs étapes n'ont aucune implémentation, faute de couche HTTP à piloter. Ce
 * que cette configuration apporte aujourd'hui n'est donc pas une exécution,
 * c'est une vérification : le Gherkin des trois cas est analysé, et une faute
 * de syntaxe s'y verrait. Le jour où le socle sera déployé, les définitions
 * d'étapes viendront dans ContexteDeParcours, sans que rien d'autre ne bouge.
 *
 * Behat 4 ne lit plus de YAML : sa configuration est ce fichier PHP.
 */
return (new Config())->withProfile(
    (new Profile('default'))->withSuite(
        (new Suite('bout_en_bout'))
            ->withPaths('%paths.base%/tests/BoutEnBout')
            ->withContexts(ContexteDeParcours::class),
    ),
);
