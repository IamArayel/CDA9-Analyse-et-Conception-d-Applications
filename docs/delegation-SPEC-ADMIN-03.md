# Plan de délégation - `SPEC-ADMIN-03`

- **Spécification :** export du planning des réservations
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-06`, `CASE-ADMIN-07`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-ADMIN-01` livrée, et `SPEC-BOOKING-07` pour disposer de réservations payées.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-03` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les réservations elles-mêmes : cet export est en lecture seule, il ne modifie rien.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Produire un document PDF imprimable pour une période donnée, réservations regroupées par créneau dans l'ordre chronologique, en excluant les réservations non payées | `CASE-ADMIN-06` | le socle commun ci-dessus | toute écriture en base, l'état des réservations |
| 2 | Produire un document lisible et sans erreur pour une période sans aucune réservation, en indiquant explicitement l'absence de réservation | `CASE-ADMIN-07` | le socle, plus la tâche 1 | la génération écrite en 1 |

**Découpage retenu :** un export nominal, un cas vide. Le second mérite sa tâche parce que c'est le comportement que l'on code le moins spontanément.

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
| 1 | `repris` | le service produit le **contenu** du document, pas un PDF : la mise en page appartient à la couche Interface, qui n'existe pas. Le cas ne fait pas la différence, c'est déclaré dans les trous |
| 2 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Une erreur affichée pour une période sans réservation.** Hors saison, c'est le cas normal : le gérant doit pouvoir imprimer une journée vide sans se demander si l'outil a échoué.
- Des réservations non payées listées au planning : elles n'embarquent personne.
- Un export qui permettrait de modifier une réservation : cette spécification ne fait que lire.
