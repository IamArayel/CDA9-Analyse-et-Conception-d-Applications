# Plan de délégation - `SPEC-BOOKING-05`

- **Spécification :** privatisation d'un bateau
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-29`, `CASE-BOOKING-30`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-03` livrée : une privatisation bloque des places, donc elle s'appuie sur le décompte.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-05` uniquement.
- Les deux fichiers de cas cités.
- `docs/mcd-mld.md` §5, paragraphe sur le forfait porté par `bateau`.
- `docs/architecture.md` §2 et §4.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code livré pour `SPEC-BOOKING-03` et `SPEC-BOOKING-06`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Bloquer toutes les places d'un bateau sur un créneau et facturer le forfait du bateau, indépendant du nombre de participants. Le second bateau du créneau reste réservable | `CASE-BOOKING-29` | le socle commun ci-dessus | le calcul du montant standard, la base de tarifs |
| 2 | Refuser une privatisation sur un bateau portant déjà des places vendues au même créneau, sans annuler ni déplacer les réservations existantes | `CASE-BOOKING-30` | le socle, plus la tâche 1 | le blocage écrit en 1, la répartition des passagers, hors périmètre |

**Découpage retenu :** un chemin nominal, un refus. Les deux tiennent en deux tâches parce que la règle est simple, mais le refus mérite sa tâche : c'est lui qui protège les clients déjà inscrits.

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

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Une privatisation facturée au nombre de participants.** Le client a explicitement exclu tout tarif préférentiel : c'est un forfait par bateau, 600 € ou 1 100 €.
- Un forfait lu dans la table `tarif` au lieu de la table `bateau`. Le modèle le porte sur le bateau, et un bateau créé sans forfait n'est pas privatisable.
- Une privatisation qui annulerait ou déplacerait les réservations existantes pour se faire de la place.
