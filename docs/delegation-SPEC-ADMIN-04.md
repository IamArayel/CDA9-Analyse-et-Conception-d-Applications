# Plan de délégation - `SPEC-ADMIN-04`

- **Spécification :** horaires d'ouverture et jours de fermeture
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-08`, `CASE-ADMIN-09`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-ADMIN-01` livrée, et `SPEC-BOOKING-02` pour l'offre de créneaux, dont cette spécification modifie les entrées.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-04` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.
- `specs/booking.md`, section `SPEC-BOOKING-02`, en lecture seule.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et la politique d'offre de créneaux, livrée par `SPEC-BOOKING-02`, et les réservations existantes.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Présenter le 25 décembre et le 1er janvier comme jours de fermeture dès la première ouverture de la section, puis permettre d'ajouter et de retirer une date, avec effet immédiat sur les créneaux proposés | `CASE-ADMIN-08` | le socle commun ci-dessus | la génération des créneaux, les réservations |
| 2 | Accepter l'ajout d'un jour de fermeture sur une date portant des réservations payées, **en les listant au gérant sans en annuler ni en rembourser aucune** | `CASE-ADMIN-09` | le socle, plus la tâche 1 | l'annulation et le remboursement, qui appartiennent au domaine CANCEL |

**Découpage retenu :** une gestion de dates, un cas limite. Le second est le seul qui compte vraiment : c'est un effet de bord que le client n'avait pas envisagé.

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

- **Des réservations annulées automatiquement quand leur date devient fermée.** C'est la réaction logique, et elle est fausse : le client n'a jamais demandé cela, et l'annulation reste une décision du gérant. L'outil liste, il ne décide pas.
- Un remboursement déclenché depuis cet écran : aucun appel au prestataire de paiement n'a sa place ici.
- Les deux dates par défaut traitées comme des données à saisir : elles doivent être présentes sans que personne les ajoute.
