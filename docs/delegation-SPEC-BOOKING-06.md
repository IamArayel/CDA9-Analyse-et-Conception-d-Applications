# Plan de délégation - `SPEC-BOOKING-06`

- **Spécification :** tarification standard par type de sortie
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-31`, `CASE-BOOKING-32`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-01` livrée, puisque le montant se calcule sur une réservation existante. Ne suppose pas le paiement.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-06` uniquement.
- `specs/admin.md`, section `SPEC-ADMIN-02`, en lecture seule : c'est elle qui pose que le montant d'une réservation payée ne bouge jamais.
- Les deux fichiers de cas cités.
- `docs/mcd-mld.md` §5, paragraphe « `reservation` ne référence pas `tarif` ».
- `docs/strategie-de-test.md` §7, pour la grille de référence.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, le code livré pour `SPEC-BOOKING-01` et `SPEC-BOOKING-02`, et l'écran de modification des tarifs, qui appartient à `SPEC-ADMIN-02`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire le calcul du montant : tarif adulte fois le nombre d'adultes, plus tarif enfant fois le nombre d'enfants, selon le type de sortie. Montants en décimal exact, jamais en flottant | `CASE-BOOKING-31` | le socle commun ci-dessus | la base de données, la privatisation, les codes de réduction |
| 2 | Recopier le montant calculé **sur la réservation** au moment de sa création, de sorte qu'une modification ultérieure de la grille ne le change pas | `CASE-BOOKING-32` | le socle, plus le résultat de la tâche 1 | le calcul écrit en 1, la table `tarif`, l'espace de gestion |

**Découpage retenu :** deux tâches seulement, mais elles portent deux idées opposées. La première calcule à partir de la grille, la seconde **cesse** de la consulter. C'est cette bascule qui protège le client d'un débit différent du montant annoncé.

---

## Après - ce qui s'est passé

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Une clé étrangère de `reservation` vers `tarif`.** C'est la solution que produit spontanément un ORM, et c'est exactement ce que `mcd-mld.md` §5 écarte : elle ferait varier a posteriori le montant d'une réservation déjà payée dès que le gérant change sa grille.
- Un montant stocké en flottant. Le modèle impose un décimal exact, et une erreur d'arrondi sur un paiement se voit chez le client, pas chez nous.
- Un calcul qui gérerait aussi la privatisation ou les codes de réduction : ils relèvent de `SPEC-BOOKING-05`, `09` et `10`, et n'ont rien à faire dans cette tâche.
