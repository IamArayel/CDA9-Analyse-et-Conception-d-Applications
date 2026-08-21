# Planning - équipe `Le Trio`

**Projet :** Ti Baleine, réservation de sorties en mer
**Fenêtre :** J1 lundi 10/08 à J10 vendredi 21/08, deux semaines
**Membres :** Chloe Baisse, Arnaud Maxime, Anthony Dégeilh

Ce document dit **ce que nous nous engageons à produire, et quel jour**. Les
colonnes « Réalisé » se remplissent au fil de l'eau : ce qui compte à J10
n'est pas que le plan ait été tenu à la lettre, mais que l'écart entre la
prévision et le résultat soit lisible et expliqué.

---

## 1. Ce qui nous est imposé

Le reste s'organise autour de ces points fixes (`README.md` §6bis, §7 et §8).

| Quand | Quoi |
|---|---|
| Tous les jours, 16h15 | entrée au journal et push. Un dépôt sans push du jour vaut journée sans production |
| J2 | ce planning |
| J5, vendredi 15h00 | jalon de fin de semaine 1 : cahier des charges, specs, UML, MCD/MLD, architecture, ADR. Verdicts à 16h15 |
| J7 | `docs/delegation-<SPEC>.md`, **avant** de confier la première tâche à l'agent |
| J9 | revue croisée |
| J10, dépôt gelé à 11h30 | rendu global et présentation devant la promo |

Les rendez-vous client s'ajoutent à ce calendrier sans être annoncés à
l'avance : le README prévient que « le client peut revenir en cours de
mission ». Nous réservons pour cela une marge quotidienne, voir §4.

## 2. Ordre de travail

La chaîne descend toujours dans le même ordre, et rien ne commence avant que
le maillon précédent existe :

```text
échange client → cahier des charges → specs → UML → modèle de données
              → cas de test → tests → code
```

Un changement du client n'entre pas dans cette chaîne par le milieu : il
passe d'abord par un compte rendu, puis par une analyse d'impact, puis
redescend. C'est la règle que nous nous appliquons depuis `impact-CR-001`.

## 3. Semaine 1 : comprendre, spécifier, concevoir

| Jour | Objectif | Livrables prévus | Réalisé |
|---|---|---|---|
| **J1** lun 10/08 | Rencontrer le client, poser le dépôt | `CR-01`, squelette du dépôt, `ADR-001` engagé | `CR-01`, `ADR-001`, gabarits d'architecture et d'analyse d'impact posés |
| **J2** mar 11/08 | Transformer l'entretien en exigences | v1 du cahier des charges, planning, `ADR-001` validé | cahier des charges v1 puis v2 après l'échange oral du 11/08 ; `ADR-001` adopté |
| **J3** mer 12/08 | Spécifier, et absorber le troisième entretien | specs des quatre domaines, matrice de traçabilité | `CR-02` et `CR-03` formalisés, `impact-CR-001`, cahier des charges v3, les 4 fichiers de specs, matrice générée |
| **J4** jeu 13/08 | Concevoir | `use-cases.puml`, `domain.puml`, 3 séquences | les trois diagrammes, plus `CR-04`, `impact-CR-002` et cahier des charges v4 après un retour client non prévu |
| **J5** ven 14/08 | Être présentable au jalon | MCD/MLD, `architecture.md`, `ADR-002` | MCD/MLD, architecture v1 puis v2, `ADR-002` et `ADR-003`, `CR-05`, `impact-CR-003`, cahier des charges v5, specs et UML redescendus |

**Écart de la semaine 1.** Trois retours client non prévus au calendrier,
aux J3, J4 et J5, chacun ayant imposé une analyse d'impact et une descente
complète de la chaîne. Le travail de conception initialement prévu à J4 a
donc été fait à J4 **et** repris à J5. Le jalon a été tenu.

## 4. Semaine 2 : tester, générer, livrer

| Jour | Objectif | Livrables prévus | Réalisé |
|---|---|---|---|
| **J6** lun 17/08 | Rendre les spécifications testables | stratégie de test, gabarit de cas, premiers `CASE-*` sur les deux spécifications les plus exposées | `docs/strategie-de-test.md`, `tests/cases/TEMPLATE.md`, `CASE-BOOKING-01` à `08`, `CASE-CANCEL-01` à `09` |
| **J7** mar 18/08 | Encadrer l'agent avant de le lancer | `docs/delegation-<SPEC>.md` rempli avant la première tâche, premiers tests automatisés | **25 plans de délégation** écrits avant toute génération, `BOOKING-01` à `11`, `CANCEL-01` à `06`, `ADMIN-01` à `06`, `NFR-02` et `04`, plus la reprise du gabarit ; les quatre spécifications sans plan déclarées dans la traçabilité ; PHPUnit et ses deux suites, le socle de test avec le jeu de référence, le monde et les trois doublures ; **76 cas automatisés, tous au rouge**, le code n'existant pas encore |
| **J8** mer 19/08 | Produire le code sous contrôle | code des spécifications `Must` les plus exposées, tests au vert, revue de chaque génération | Matin et début d'après-midi : plan du socle technique, les 24 types de contrat du domaine, Symfony 8 et Doctrine, les douze entités du MLD en PHP nu avec mapping XML, la migration initiale, cinq dépôts, les trois ports liés à leurs adaptateurs, puis le code de `BOOKING`, `CANCEL`, `ADMIN` et `NFR` : **les 76 tests passés au vert**, chaque génération revue, les refus consignés au journal. Second créneau : `CR-06` reçu, son analyse d'impact, cahier **v6**, `SPEC-BOOKING-07` refondue en acompte, `SPEC-BOOKING-12` et `SPEC-ADMIN-07` créées, `ADR-006`, le diagramme d'états et la séquence de réservation, la table `PAIEMENT` au MCD, 21 cas repris ou créés et **21 tests laissés au rouge**, assumés pour J9 |
| **J9** jeu 20/08 | Confronter | revue croisée, corrections, second tableau du plan de délégation rempli le soir même | `docs/compte-rendu-entretien-07.md` et `docs/impact-CR-005.md`, cahier des charges **v7**, `SPEC-BOOKING-12` v2, `SPEC-ADMIN-06` v3, `SPEC-CANCEL-07` neuve, `CASE-CANCEL-25`, les 21 tests rouges laissés à J8 passés au vert soit **87 tests verts**, les deux derniers plans de délégation avec leur tableau « Après », `Interface\Console\DemontrerLeParcoursCommand` et l'environnement `demo`, `Makefile` et dépôt rendu clonable, `README_J10.md`, les **trois scénarios de bout en bout** écrits avec Behat 4 et déclarés non exécutables. Revue croisée : `CASE-NFR-06` exécuté, verdict passé, et les deux échéances intenables de la table des trous corrigées plutôt que reconduites, celle de `CASE-BOOKING-37` et celle des scénarios Behat. **Une sixième question part au client**, le doublement des frais de transaction non chiffré |
| **J10** ven 21/08 | Livrer et défendre | dépôt gelé à 11h30, présentation, chaîne complète démontrable sur au moins un parcours | Procédure de `README_J10.md` rejouée en entier depuis un poste dont le moteur Docker était éteint : `make presentation` **87 tests verts, 360 assertions**, 5 ruptures, deux `[OK]` de mapping ; `make demo` les huit étapes du parcours, chacune annonçant sa spécification et son cas ; `make behat` 3 scénarios et 21 étapes `undefined`, compte inchangé ; les trois `--filter` de repli verts séparément. Les chiffres du §3 vérifiés un par un contre le dépôt : un seul écart, **l'en-tête du cahier des charges annonçait encore v6** alors que son historique, ses exigences et tout le reste du dépôt disent v7, et sa ligne « Sources » ignorait `CR-07` et `impact-CR-005` ; corrigé. Entrée de journal J10 |

**Marge.** Une demi-journée est gardée libre à J8 et à J9. Elle n'est pas un
coussin de confort : elle est là pour absorber un retour client, qui s'est
produit trois fois en semaine 1 et se reproduira.

## 5. Répartition

Le README §2 fixe qui fait quoi entre l'équipe et l'agent. Nous nous y
tenons : l'équipe conduit la découverte, spécifie, conçoit et **définit les
cas de test** ; l'agent produit les tests automatisés et le code, et nous
relisons chaque génération. La revue critique de l'agent intervient avant le
développement, la nôtre après.

Nous rédigeons dans Notion et l'agent met en forme les documents attendus par
le dépôt. Cette organisation n'a pas changé depuis J1.

## 6. Risques identifiés et ce que nous en faisons

| Risque | Effet s'il se produit | Ce que nous faisons |
|---|---|---|
| Un retour client tardif | toute la chaîne à redescendre en un jour | analyse d'impact systématique avant toute modification, et marge en semaine 2 |
| Les règles horaires du projet, sept au total | rien n'est testable si le code lit l'heure système | horloge injectable imposée par la stratégie de test |
| La concurrence sur la dernière place | un client débité pour une place qu'il n'a pas | arbitrée en `ADR-003` avant toute écriture de code |
| Des questions client sans réponse à J10 | des spécifications non défendables | tracées au §11 du cahier des charges et dans `docs/traceability-trous.md`, jamais laissées implicites |
| Le code généré qui déborde de sa couche | perte de la séparation posée en `architecture.md` §2 | la colonne « ce qui n'a rien à y faire » sert de grille de revue |

## 7. Comment nous saurons que nous sommes en retard

Trois signaux, vérifiables sans discussion :

- une journée sans push, ce que le README traite comme une journée sans
  production ;
- une rupture de traçabilité qui reste ouverte deux jours de suite sans
  figurer dans `docs/traceability-trous.md` ;
- une spécification `Must` encore sans cas de test à la fin de J7.
