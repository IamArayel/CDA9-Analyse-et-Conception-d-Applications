# Plan de délégation - `SPEC-BOOKING-04`

- **Spécification :** fermeture des réservations en ligne selon le créneau
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-27`, `CASE-BOOKING-28`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-02` livrée pour l'offre de créneaux, et `SPEC-BOOKING-03` pour l'immobilisation, dont la tâche 2 dépend directement.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-04` uniquement.
- Les deux fichiers de cas cités.
- `docs/adr/ADR-005-horloge-injectable.md` et `ADR-003`, dont la tâche 2 est une conséquence directe.
- `docs/architecture.md` §2 et §4.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code livré pour `SPEC-BOOKING-02` et `SPEC-BOOKING-03`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire la règle de fermeture : le créneau de 14h ferme à 12h00 le jour même, ceux de 7h et 10h à 12h00 la veille. Règle pure, l'instant est un paramètre, bornes **à partir de** 12h00 et non après | `CASE-BOOKING-27` | le socle commun ci-dessus | la base de données, l'immobilisation, l'affichage |
| 2 | Apprécier la fermeture **à la validation du formulaire** et non à l'encaissement, de sorte qu'une réservation validée à 11h55 reste payable pendant ses 15 minutes | `CASE-BOOKING-28` | le socle, plus la tâche 1 | la règle écrite en 1, le paiement lui-même |

**Découpage retenu :** une règle pure, puis le choix du moment où elle s'applique. La seconde tâche est la seule qui touche au parcours.

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

- **Une fermeture appréciée à l'encaissement.** C'est l'intuition naturelle, et elle refuserait un client validant à 11h59 après qu'il a saisi sa carte, exactement ce que `ADR-003` cherche à éviter.
- Un test écrit avec `>` au lieu de `>=` sur midi : 12h00 pile doit refuser, 11h59 accepter.
- Une lecture de l'heure système dans la règle, au lieu du paramètre.
