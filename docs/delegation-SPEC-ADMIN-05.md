# Plan de délégation - `SPEC-ADMIN-05`

- **Spécification :** création d'un nouveau bateau
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-10`, `CASE-ADMIN-11`, `CASE-ADMIN-12`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-ADMIN-01` livrée, et `SPEC-BOOKING-03` pour le décompte des places, dont le nouveau bateau hérite.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-05` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.
- `docs/mcd-mld.md` §5, paragraphe sur le forfait de privatisation porté par
  `bateau`, colonne **nullable**.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les bateaux existants, qui ne sont ni renommables ni recalibrables, et la règle du naturaliste unique.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Créer un bateau à partir d'un nom et d'une capacité, et le faire apparaître dans les créneaux proposés côté client avec sa capacité, pour tous les types de sortie | `CASE-ADMIN-10` | le socle commun ci-dessus | les bateaux existants, le décompte des places |
| 2 | Ne pas proposer la privatisation d'un bateau tant qu'aucun forfait n'est saisi pour lui, et la proposer dès qu'il l'est | `CASE-ADMIN-11` | le socle, plus la tâche 1 | la privatisation elle-même, livrée par `SPEC-BOOKING-05` |
| 3 | Refuser un nom déjà porté par un bateau de la flotte, une capacité nulle ou négative, et une capacité non entière | `CASE-ADMIN-12` | le socle, plus la tâche 1 | la création écrite en 1 |

**Découpage retenu :** une création, une règle d'indisponibilité, trois refus regroupés. La tâche 2 traite une contradiction relevée en revue : la privatisation est tarifée par bateau, or le formulaire de création n'en demande pas.

---

## Après - ce qui s'est passé

**Rempli au rituel de 16h15 du J8.**

Le découpage prévoyait **une tâche par cas de test, confiée séparément**. Dans
les faits, le code a été produit **spécification par spécification** : un même
service applicatif satisfait plusieurs cas, et le scinder en autant de tâches
aurait produit du code jetable entre deux passages. Les tests attendus sont
passés au vert dans l'ordre prévu par les dépendances, mais **les tâches n'ont
pas été des unités de délégation distinctes**. C'est l'écart principal de la
journée, il vaut pour les vingt-six plans, et il tient au découpage que nous
avons écrit, pas à l'agent.

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | `conforme` |  |
| 2 | `conforme` |  |
| 3 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un champ de types de sorties compatibles ajouté au formulaire.** L'hypothèse d'équipe est l'inverse : tout bateau créé est habilité à tous les types de sortie, faute d'avoir cette information même pour les deux bateaux existants.
- **Un bateau créé proposé à la privatisation avec un forfait nul ou vide.** Il doit simplement ne pas être proposé.
- Une modification ou une suppression d'un bateau existant, hors périmètre depuis le début.
