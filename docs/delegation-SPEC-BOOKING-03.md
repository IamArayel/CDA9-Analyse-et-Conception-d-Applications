# Plan de délégation - `SPEC-BOOKING-03`

- **Spécification :** capacité, seuil minimal et places disponibles en temps réel
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-01` à `CASE-BOOKING-08`

C'est une prévision, pas un compte rendu. 

**Dépendance.** Suppose `SPEC-BOOKING-02` et `SPEC-BOOKING-01` livrées : il faut une sortie et une réservation avant de compter des places.

**C'est la spécification la plus exposée du projet.** Elle porte trois règles qui coûtent de l'argent si elles cèdent : une place vendue deux fois, une sortie annulée à tort, un client débité pour une place qu'il n'aura pas. Le découpage est volontairement fin, une tâche par test.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-03` uniquement.
- Les huit fichiers de cas cités.
- `docs/adr/ADR-003-concurrence-derniere-place.md`, qui fonde l'immobilisation.
- `docs/adr/ADR-005-horloge-injectable.md`.
- `docs/architecture.md` §2, §3 et §5, dont la citation sur la transaction.
- `docs/mcd-mld.md` §6, §7 et §8.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code
livré pour `SPEC-BOOKING-01`, `SPEC-BOOKING-02` et `SPEC-BOOKING-06`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Compter les places prises d'une sortie, **immobilisations non échues comprises**, et accepter une demande égale au reste | `CASE-BOOKING-01` | le socle commun ci-dessus | le seuil de maintien, le naturaliste, l'affichage |
| 2 | Refuser une demande supérieure aux places restantes, adultes et enfants confondus | `CASE-BOOKING-02` | le socle, plus la tâche 1 | le décompte écrit en 1 |
| 3 | Immobiliser les places 15 minutes **à la validation du formulaire**, dans une transaction qui verrouille la ligne `sortie` | `CASE-BOOKING-03` | le socle, plus les tâches 1 et 2 | le paiement, qui appartient à `SPEC-BOOKING-07` |
| 4 | Rendre les places disponibles à l'expiration, **évaluée à la lecture** et non par une tâche planifiée | `CASE-BOOKING-04` | le socle, plus la tâche 3 | l'immobilisation elle-même, figée |
| 5 | Écrire la règle du seuil de maintien : 6 inscrits suffisent, 5 ne suffisent pas. Règle pure, l'instant est un paramètre | `CASE-BOOKING-06` | le socle | la base de données, les remboursements |
| 6 | Brancher le contrôle des 24 heures : annuler la sortie sous le seuil et déclencher le remboursement intégral de chaque client | `CASE-BOOKING-05` | le socle, plus la tâche 5 | la règle écrite en 5, l'annulation météo de `SPEC-CANCEL-02` |
| 7 | Porter la règle du naturaliste unique **par une contrainte de base**, colonne générée et index unique, et traduire l'échec en refus métier | `CASE-BOOKING-07` | le socle, plus `mcd-mld.md` §7 | tout le reste du code écrit jusqu'ici |
| 8 | Mettre à jour le nombre de places affiché chez les autres clients après confirmation d'un paiement, sans rechargement | `CASE-BOOKING-08` | le socle, plus les tâches 1 à 4 | les règles métier, qui sont toutes figées à ce stade |

**Découpage retenu :** cinq tâches de règle, une tâche de contrainte de base, une tâche d'affichage. Les tâches 5 et 6 sont séparées volontairement : la règle du seuil se teste sans base, son déclenchement non.

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
| 3 | `repris` | l'assertion du cas vérifiait qu'**aucun** encaissement n'avait eu lieu, alors que le montage du monde paie désormais réellement ses réservations. Recentrée sur le second client, seul concerné |
| 4 | `conforme` |  |
| 5 | `conforme` |  |
| 6 | `conforme` |  |
| 7 | `repris` | l'index unique fait **fermer le gestionnaire d'entités** de Doctrine, ce qui rend la suite de la requête inutilisable. Un contrôle applicatif le double maintenant pour rendre un refus propre ; l'index reste la garantie sous concurrence |
| 8 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Le verrou pris au paiement plutôt qu'à la validation.** C'est le comportement d'avant `ADR-003`, et c'est celui qu'un agent produira spontanément, puisque c'est le plus intuitif. Il ferait revenir le défaut que la décision de vendredi a supprimé : un client débité pour une place déjà vendue.
- **Une expiration confiée à une tâche planifiée.** Si les places ne redeviennent disponibles que lorsqu'un traitement passe, une panne du planificateur bloque des ventes. L'expiration doit se lire, pas s'attendre.
- **Un décompte qui oublie les immobilisations non échues.** Le test 1 passe quand même dans la plupart des cas, et le défaut n'apparaît que sous concurrence, c'est-à-dire en pleine saison.
- **La règle du naturaliste écrite en code applicatif.** Deux réservations simultanées la contourneraient. Elle doit vivre dans l'index unique.
