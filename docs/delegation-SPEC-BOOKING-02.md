# Plan de délégation - `SPEC-BOOKING-02`

- **Spécification :** créneaux et types de sortie proposés selon la saison
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-24`, `CASE-BOOKING-25`, `CASE-BOOKING-26`

C'est une prévision, pas un compte rendu.

**Pourquoi cette spécification en premier.** La stratégie de test ordonne les cas par risque, `SPEC-BOOKING-03` d'abord. Le code, lui, suit les dépendances : on ne peut pas immobiliser une place sur un créneau qui n'existe pas. `SPEC-BOOKING-02` est la fondation, et c'est aussi la plus simple, donc un bon premier essai pour calibrer le découpage.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-02` uniquement.
- Les trois fichiers de cas cités, en lecture.
- `docs/architecture.md` §2 et §4, pour les couches et l'arborescence.
- `docs/adr/ADR-005-horloge-injectable.md`, parce que la saison est une règle
  de date.
- `docs/strategie-de-test.md` §9, pour savoir où écrire et sous quel nom.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et toute spécification autre que celle-ci. Les cas de test sont écrits par l'équipe : un agent qui modifie un cas pour faire passer son code a inversé la chaîne.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire la politique de saison : une date et un type de sortie en entrée, accepté ou refusé en sortie. Bornes du 15 juin et du 31 octobre incluses. L'instant est un paramètre, pas une lecture d'horloge | `CASE-BOOKING-24` | le socle commun ci-dessus | la base de données, l'interface, les jours de fermeture |
| 2 | Écrire la politique des jours de fermeture et l'assemblage des trois créneaux d'une date donnée | `CASE-BOOKING-25` | le socle, plus le résultat de la tâche 1 | la politique de saison écrite en 1, la base de données |
| 3 | Brancher le refus d'une sortie baleines hors saison **à l'enregistrement**, et non seulement à l'affichage | `CASE-BOOKING-26` | le socle, plus les résultats des tâches 1 et 2 | les deux politiques, qui sont désormais figées |

**Découpage retenu :** deux règles pures puis un branchement. La tâche 3 est la seule à toucher la couche application, ce qui isole le seul endroit où une erreur de couche peut apparaître.

---

## Après - ce qui s'est passé

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

## Ce que nous surveillons particulièrement ici

- Un appel direct à l'heure système dans la politique de saison. C'est le premier endroit où la règle de `ADR-005` peut céder, et le plus discret.
- Une politique de saison qui irait lire les jours de fermeture, ou l'inverse : ce sont deux règles distinctes, et les mélanger rendrait la tâche 2 intestable seule.
