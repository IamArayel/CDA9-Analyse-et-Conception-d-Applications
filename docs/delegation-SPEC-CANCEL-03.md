# Plan de délégation - `SPEC-CANCEL-03`

- **Spécification :** répercussion en temps réel côté client
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-19`, `CASE-CANCEL-20`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-CANCEL-02` livrée pour l'annulation, et `SPEC-CANCEL-06` pour l'état d'alerte, dont cette spécification affiche les deux effets opposés.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-03` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `docs/adr/ADR-003-concurrence-derniere-place.md`, pour l'interruption d'un
  paiement en cours.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les transitions d'état elles-mêmes, livrées par `SPEC-CANCEL-02` et `SPEC-CANCEL-06`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Retirer un créneau annulé des créneaux réservables, et laisser un créneau en alerte proposé avec le risque signalé, la mise à jour se faisant sans rechargement | `CASE-CANCEL-19` | le socle commun ci-dessus | les décisions d'annulation et d'alerte |
| 2 | Refuser la validation d'une réservation sur un créneau annulé, et interrompre un paiement en cours **sans débiter le client**, en libérant ses places immobilisées | `CASE-CANCEL-20` | le socle, plus la tâche 1 | le parcours de paiement nominal, livré par `SPEC-BOOKING-07` |

**Découpage retenu :** l'affichage d'abord, l'interruption ensuite. Les deux effets sont opposés et c'est le cœur de la spécification : l'alerte laisse vendre, l'annulation retire.

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

- **Un créneau en alerte masqué comme s'il était annulé.** Le client a explicitement demandé qu'il reste réservable avec un signalement : le masquer reviendrait à annuler sans le dire.
- **Un client débité alors que son créneau vient d'être annulé.** C'est le pire résultat possible de cette spécification, et il se produit si l'interruption arrive après l'encaissement.
- Des places immobilisées non libérées après une annulation : elles resteraient indisponibles jusqu'à leur expiration, sur un créneau qui n'existe plus.
