# Plan de délégation - socle technique

- **Spécification :** aucune. **C'est la particularité de ce plan.**
- **Date :** J8 (2026-08-19), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** aucun directement, les 76

C'est une prévision, pas un compte rendu.

## Pourquoi ce plan n'a pas d'identifiant de spécification

Le README §6bis impose un plan avant la première tâche confiée à l'agent **sur
la spécification désignée**. Le socle n'est pas une spécification : ni ports, ni
énumérations, ni entités Doctrine ne portent de règle métier, et aucune ligne du
cahier des charges ne s'y rattache. Rien n'obligeait donc à écrire ce plan.

Nous l'écrivons quand même, pour deux raisons. La première est que le socle
**fige l'API que les 76 tests appellent** : c'est le moment du projet où une
erreur se propage le plus loin. La seconde est qu'un socle produit sans plan,
au milieu de 25 plans, se lirait comme un oubli plutôt que comme une décision.

Le fichier ne s'appelle donc pas `delegation-SPEC-...` et n'apparaît pas dans la
matrice de traçabilité, qui ne connaît que les spécifications.

## Le critère de fin n'est pas un test au vert

Les 24 autres plans nomment, pour chaque tâche, le cas de test qui doit changer
d'état. **Ici, aucun test ne passe au vert** : le socle ne contient aucune règle,
il ne fait que rendre les classes appelables. Le critère est donc un
**déplacement de l'erreur**, qui se vérifie aussi mécaniquement :

```bash
vendor/bin/phpunit 2>&1 | grep -oE 'App\\(Domaine|Application)\\[A-Za-z\\]+' | sort -u
```

La liste des classes introuvables doit se réduire au fur et à mesure des lots, et
ne plus contenir que des services applicatifs et des politiques, c'est-à-dire ce
qui reste à déléguer aux 24 autres plans.

---

## Ce que l'agent reçoit dans tous les cas

- Les fichiers de test qui appellent les types à écrire. **Ils sont la source :**
  les signatures s'y lisent, elles ne s'inventent pas.
- `docs/architecture.md` §2 et §4, pour les couches et l'arborescence.
- `docs/adr/ADR-001-stack.md` §5, pour les versions épinglées.
- `docs/adr/ADR-005-horloge-injectable.md`.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`.

**Et, propre à ce plan :** aucun fichier de `tests/`, à la seule exception du
lot 3, qui ne porte que sur `tests/CasDapplication.php` et `tests/MondeDeTest.php`.
Les 76 fichiers de test sont le contrat ; un socle qui les modifie pour se
faciliter la tâche est un socle faux.

---

## Avant - le découpage

| # | Tâche | Ce qui doit changer d'état | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire les **19 types de contrat** : 3 ports, 4 énumérations, 2 exceptions, 5 résultats, 5 vues. Aucune logique, aucune décision, aucun accès à l'heure ni à la base | plus aucune erreur ne nomme l'un des 19 types | le socle commun, plus `tests/Doublures/` et les tests qui appellent ces types | tout `tests/`, et les politiques et services applicatifs, qui appartiennent aux 24 autres plans |
| 2 | Installer le squelette **Symfony 7 par-dessus le `composer.json` existant**, ajouter Doctrine ORM et Migrations, écrire les **12 entités** du MLD et la première migration | `php bin/console doctrine:schema:validate` passe | le socle, plus la tâche 1, plus `docs/mcd-mld.md` §6 et §7 | la cartographie PSR-4 existante, déjà conforme à Symfony ; tout `tests/` |
| 3 | Rebrancher les tests sur la base : `CasDapplication` en `KernelTestCase` avec transaction et rollback, `MondeDeTest` sur le conteneur, et le jeu de référence du §7 chargé une fois | les erreurs applicatives ne nomment plus que des services de cas d'usage | le socle, plus les tâches 1 et 2, plus `docs/strategie-de-test.md` §7 et §9 | les 76 fichiers de test, dont aucune assertion ne bouge |

**Découpage retenu :** un lot de contrat, un lot de persistance, un lot de
branchement. Les trois sont séquentiels et non parallélisables, c'est assumé :
c'est le goulet de la journée, et c'est pourquoi les dix règles pures de
`tests/Domaine/`, qui ne dépendent ni du lot 2 ni du lot 3, sont lancées en
parallèle sur les autres membres de l'équipe.

**Sur la tâche 2.** Trois éléments du MLD ne sortiront pas de
`doctrine:migrations:diff` et sont à écrire à la main dans la migration :

1. la colonne générée `sortie.creneau_baleines` et son **index unique**, qui
   porte à elle seule la règle du naturaliste ;
2. la contrainte `CHECK` de non-cumul sur `reservation`, et les unicités sur
   `bon_cadeau_id` et `avoir_id` ;
3. les `CHECK` de positivité sur `bateau.capacite`,
   `bateau.forfait_privatisation` et les tarifs.

Le diff ne les invente pas. S'ils manquent, deux règles métier disparaissent
sans qu'aucun test de niveau domaine ne s'en aperçoive.

---

## Après - ce qui s'est passé

Complété au rituel de 16h15, le même jour.

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |
| 3 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

---

## Ce que nous surveillons particulièrement ici

- **Un type de contrat qui se met à décider.** Un `ResultatDeReservation` qui
  choisirait lui-même son motif de refus, ou un `VueDeCreneau` qui calculerait
  les places restantes, serait une règle métier écrite hors de toute
  spécification et hors de tout plan. C'est le risque principal du lot 1, et il
  est d'autant plus insidieux que le code paraîtrait juste.
- **Une entité Doctrine qui lit l'heure système.** `architecture.md` §2 le range
  dans la colonne « ce qui n'a rien à y faire », et `ADR-005` en fait un défaut
  de revue. Un `date_creation` initialisé par `new DateTimeImmutable()` dans un
  constructeur suffit à rendre huit règles horaires intestables.
- **Une migration amputée de ses trois éléments manuels.** Voir la note sur la
  tâche 2. À relire ligne à ligne contre `mcd-mld.md` §7, sans faire confiance
  au diff.
- **Un agent qui modifie un test pour le faire passer.** Les 76 tests sont le
  contrat de la journée. Le contrôle est mécanique : `git diff --stat tests/`
  ne doit montrer, sur ce plan, que `CasDapplication.php` et `MondeDeTest.php`.
- **Le squelette Symfony qui réécrit `composer.json`.** La cartographie PSR-4
  existante est déjà celle de Symfony ; un `composer.json` régénéré casserait
  `App\Tests\ → tests/` et rendrait les 76 tests introuvables.
