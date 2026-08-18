# Plan de délégation - `SPEC-BOOKING-09`

- **Spécification :** achat et usage d'un bon cadeau
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-14` à `CASE-BOOKING-19`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-06` et `SPEC-BOOKING-07` livrées : un bon cadeau se déduit d'un montant et laisse un solde à encaisser.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, sections `SPEC-BOOKING-09` et `SPEC-BOOKING-10`, la
  seconde en lecture seule pour la règle de non-cumul.
- Les six fichiers de cas cités.
- `docs/mcd-mld.md` §5 et §7, dont la contrainte `CHECK` du non-cumul.
- `docs/adr/ADR-005-horloge-injectable.md`, pour l'expiration.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code livré pour `SPEC-BOOKING-06` et `SPEC-BOOKING-07`, et la table `avoir`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Délivrer à l'achat un code unique portant un montant libre et une expiration à un an, sans aucun rattachement à un type de sortie ni à une catégorie de tarif | `CASE-BOOKING-14` | le socle commun ci-dessus | l'usage du code au paiement, la table `avoir` |
| 2 | Déduire le montant du bon du montant total et exiger la différence par carte lorsque le total est supérieur | `CASE-BOOKING-15` | le socle, plus la tâche 1 | l'émission du code, figée |
| 3 | Perdre le surplus lorsque le bon dépasse le montant total : aucun avoir résiduel, aucun remboursement | `CASE-BOOKING-16` | le socle, plus les tâches 1 et 2 | le calcul du solde écrit en 2 |
| 4 | Refuser un code déjà utilisé, avec un message qui ne distingue pas un code inexistant d'un code consommé | `CASE-BOOKING-17` | le socle, plus la tâche 2 | les règles de montant |
| 5 | Écrire la règle d'expiration : acceptée jusqu'à la fin du jour anniversaire, refusée le lendemain. Règle pure, l'instant est un paramètre | `CASE-BOOKING-18` | le socle | la base de données, les règles de montant |
| 6 | Interdire le cumul d'un bon cadeau et d'un code d'avoir sur une même réservation, **par la contrainte de base** doublée d'un message clair | `CASE-BOOKING-19` | le socle, plus `mcd-mld.md` §7 | tout le reste du code écrit jusqu'ici |

**Découpage retenu :** quatre règles de montant et de validité, une règle pure d'expiration, une contrainte de base. La tâche 5 est isolée parce qu'elle se teste sans persistance.

---

## Après - ce qui s'est passé

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |
| 3 | | |
| 4 | | |
| 5 | | |
| 6 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un surplus rendu sous forme d'avoir.** C'est ce qu'un agent proposera pour être aimable, et c'est contraire à ce que le client a posé deux fois : le surplus est perdu.
- **Un bon cadeau rattaché à un type de sortie.** C'était la règle en v3, elle a été inversée en v4. Un agent qui lit une version ancienne de la spécification la réintroduira.
- Un message de refus qui distingue « code inexistant » de « code déjà utilisé » : cela permet de sonder les codes valides.
- Le non-cumul écrit uniquement en code applicatif, alors que la base porte la contrainte.
