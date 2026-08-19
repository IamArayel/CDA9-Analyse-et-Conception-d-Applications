# Plan de délégation - `SPEC-BOOKING-07`

- **Spécification :** paiement en ligne intégral par carte
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-09` à `CASE-BOOKING-13`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-03` et `SPEC-BOOKING-06` livrées : on ne paie pas un montant qui n'est pas calculé, ni une place qui n'est pas immobilisée. Dernière brique du parcours « réserver et payer ».

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-07` uniquement.
- Les cinq fichiers de cas cités.
- `docs/adr/ADR-001-stack.md` §5, pour le prestataire retenu.
- `docs/adr/ADR-003-concurrence-derniere-place.md`, dont les conséquences
  décrivent le cas résiduel de la tâche 5.
- `docs/architecture.md` §5 et §6.
- `docs/strategie-de-test.md` §9, pour la doublure de paiement et ses quatre
  réponses.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, le code livré pour les spécifications précédentes, et **le schéma de la base**, qui n'a aucune colonne à recevoir pour le paiement.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Demander l'encaissement de la totalité du montant, puis, sur confirmation du prestataire, passer la réservation à « confirmée » et décompter les places, **dans la même transaction** | `CASE-BOOKING-09` | le socle commun ci-dessus | le calcul du montant, l'immobilisation, la grille tarifaire |
| 2 | Traiter un paiement refusé : rien n'est confirmé, aucune place décomptée, l'immobilisation continue de courir | `CASE-BOOKING-10` | le socle, plus la tâche 1 | le chemin nominal écrit en 1 |
| 3 | Rendre l'encaissement idempotent : deux soumissions du même paiement ne produisent qu'un débit et qu'une réservation | `CASE-BOOKING-11` | le socle, plus les tâches 1 et 2 | les deux chemins précédents |
| 4 | Confirmer sans appeler le prestataire quand le montant restant dû est nul | `CASE-BOOKING-12` | le socle, plus la tâche 1 | l'application des codes, qui appartient à `SPEC-BOOKING-09` |
| 5 | Traiter le paiement abouti après expiration de l'immobilisation : reprendre la place si elle est libre, sinon refuser **et rembourser sans intervention du client** | `CASE-BOOKING-13` | le socle, plus les tâches 1 à 4 | tout le reste |

**Découpage retenu :** un chemin nominal, deux chemins d'échec, un cas de
montant nul, un cas résiduel. La tâche 5 est la dernière parce qu'elle
suppose les quatre autres, et c'est la seule qui déclenche un remboursement.

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
| 2 | `repris` | même correction que sur `SPEC-BOOKING-03` : l'absence de débit se vérifie sur le client concerné, pas sur l'ensemble du créneau |
| 3 | `conforme` |  |
| 4 | `conforme` |  |
| 5 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Une donnée de carte qui atterrit quelque part.** Numéro, date d'expiration, cryptogramme, y compris dans un journal technique ou un message d'erreur. `CASE-NFR-03` le vérifie, mais c'est ici que la faute se commet, et une trace dans un log ne se rattrape pas.
- **Une idempotence écrite en « je vérifie puis j'écris ».** Deux requêtes simultanées passent entre les deux, et le client est débité deux fois. La garantie doit venir d'une clé d'idempotence ou d'une contrainte, pas d'une lecture préalable.
- **Une confirmation écrite hors de la transaction de décompte.** Si les deux ne sont pas atomiques, une panne entre les deux laisse une réservation confirmée sans place, ou l'inverse.
- **La tâche 5 déclenche un remboursement sans que le client demande quoi que ce soit.** Un agent aura tendance à afficher un message d'erreur et à s'arrêter là. Le client a déjà payé : le laisser réclamer serait le pire résultat possible du parcours.
