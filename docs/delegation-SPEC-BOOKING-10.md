# Plan de délégation - `SPEC-BOOKING-10`

- **Spécification :** saisie d'un code d'avoir au paiement
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-19`, `CASE-BOOKING-33`, `CASE-BOOKING-34`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-09` livrée : les deux dispositifs partagent la mécanique de déduction et la règle de non-cumul.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-10` uniquement.
- Les trois fichiers de cas cités.
- `specs/admin.md`, section `SPEC-ADMIN-06`, en lecture seule : c'est elle qui
  émet les avoirs.
- `docs/mcd-mld.md` §5, sur le choix de deux tables distinctes.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code livré pour `SPEC-BOOKING-09`, et l'émission d'un avoir, qui appartient à `SPEC-ADMIN-06`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Déduire un code d'avoir du montant total et exiger le solde par carte, **quel que soit le type de sortie réservé** | `CASE-BOOKING-33` | le socle commun ci-dessus | l'émission de l'avoir, la table `bon_cadeau` |
| 2 | Refuser un code déjà utilisé et un code émis il y a plus d'un an. Règle pure, l'instant est un paramètre | `CASE-BOOKING-34` | le socle, plus la tâche 1 | la déduction écrite en 1 |
| 3 | Vérifier que le non-cumul avec un bon cadeau vaut dans les deux sens, et que le second code saisi n'est pas consommé | `CASE-BOOKING-19` | le socle, plus la tâche 1 et le code livré pour `SPEC-BOOKING-09` | la contrainte de base, déjà posée |

**Découpage retenu :** une déduction, une validité, une vérification croisée. La tâche 3 ne produit presque pas de code : elle vérifie que la règle écrite côté bon cadeau tient aussi dans l'autre sens.

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

- **Un avoir rattaché à un type de sortie.** Un avoir n'en a aucun, contrairement à ce qu'un agent pourrait déduire par symétrie avec l'ancienne règle du bon cadeau.
- **Un second code consommé alors qu'il est refusé.** Le client doit pouvoir le réutiliser ailleurs.
- Une duplication de la logique de déduction au lieu d'un service partagé : deux tables, mais une seule règle d'imputation.
