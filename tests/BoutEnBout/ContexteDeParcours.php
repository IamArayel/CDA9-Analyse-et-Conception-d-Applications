<?php

declare(strict_types=1);

namespace App\Tests\BoutEnBout;

use Behat\Behat\Context\Context;

/**
 * Contexte du troisième niveau de test, cf. docs/strategie-de-test.md §9.
 *
 * Il est **vide, et c'est l'état voulu à J9** : les trois scénarios de bout en
 * bout pilotent des écrans, et il n'existe aucune couche HTTP à piloter. Écrire
 * des définitions d'étapes contre les services applicatifs rendrait les
 * scénarios verts en dupliquant les 76 tests de niveau application, sans jamais
 * vérifier ce qu'un cas de bout en bout est censé vérifier.
 *
 * Behat rapporte donc les étapes « undefined », ce qui est le compte rendu
 * honnête de l'état du projet. Ce que sa présence apporte dès aujourd'hui est
 * l'analyse du Gherkin des trois cas : une faute de syntaxe s'y verrait.
 */
final class ContexteDeParcours implements Context
{
}
