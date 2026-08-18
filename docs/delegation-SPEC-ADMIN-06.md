# Plan de délégation - `SPEC-ADMIN-06`

- **Spécification :** enregistrement d'une annulation client et émission d'un avoir
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-13`, `CASE-ADMIN-14`, `CASE-ADMIN-15`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-ADMIN-01` livrée, `SPEC-BOOKING-07` pour le remboursement, et `SPEC-CANCEL-06` pour l'état d'alerte dont dépend la tâche 3. **C'est la seule origine d'un code d'avoir** : `SPEC-BOOKING-10` en consomme le produit.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-06` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.
- `specs/booking.md`, section `SPEC-BOOKING-10`, en lecture seule.
- `docs/adr/ADR-005-horloge-injectable.md`, pour l'expiration à un an.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et l'usage du code au paiement, livré par `SPEC-BOOKING-10`, et l'annulation décidée par le gérant, qui ne propose aucun choix.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Enregistrer, pour une réservation donnée, l'issue convenue par téléphone parmi report, avoir et remboursement, et produire un code d'avoir unique portant le montant saisi par le gérant et une expiration à un an | `CASE-ADMIN-13` | le socle commun ci-dessus | l'usage du code, le barème de retenue |
| 2 | Ne produire aucun code pour une issue « report » ou « remboursement », et refuser une seconde issue sur une réservation déjà annulée | `CASE-ADMIN-14` | le socle, plus la tâche 1 | l'émission écrite en 1 |
| 3 | Proposer un remboursement intégral, sans retenue, lorsque le créneau concerné a été mis en alerte, y compris si la sortie a finalement lieu | `CASE-ADMIN-15` | le socle, plus la tâche 1 | l'état d'alerte, livré par `SPEC-CANCEL-06` |

**Découpage retenu :** une émission, deux garanties. La tâche 3 traite la rencontre de deux règles contradictoires, le barème dégressif et le droit ouvert par l'alerte.

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

- **Un barème de retenue calculé automatiquement.** Le gérant l'applique à la main depuis toujours et n'a jamais demandé son automatisation : l'outil garde la trace de sa décision, il ne la prend pas à sa place.
- **Un code produit pour une issue « report ».** Seul l'avoir en produit un.
- Une retenue appliquée malgré l'alerte : le risque vient du gérant, donc le remboursement est intégral, et il reste acquis même si la sortie part.
- Une confusion avec l'annulation décidée par le gérant, qui elle ne propose **aucun** choix et rembourse toujours en totalité.
