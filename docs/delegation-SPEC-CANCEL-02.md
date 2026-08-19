# Plan de délégation - `SPEC-CANCEL-02`

- **Spécification :** annulation d'un créneau décidée par le gérant
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-16`, `CASE-CANCEL-17`, `CASE-CANCEL-18`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-CANCEL-01` livrée pour la consultation préalable. Ne suppose pas l'alerte : l'annulation ne l'exige pas.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-02` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `docs/adr/ADR-005-horloge-injectable.md`, pour la comparaison à l'heure de
  départ.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les envois de messages et les remboursements, qui appartiennent à `SPEC-CANCEL-04`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Faire passer une sortie à l'état « annulée » sur décision du gérant, qu'elle ait été mise en alerte auparavant ou non | `CASE-CANCEL-16` | le socle commun ci-dessus | les messages, les remboursements, l'alerte |
| 2 | Garantir qu'aucune sortie ne passe à « annulée » sans action du gérant, et qu'une alerte laissée sans suite n'annule rien à l'expiration | `CASE-CANCEL-17` | le socle, plus la tâche 1 | l'alerte, qui appartient à `SPEC-CANCEL-06` |
| 3 | Rendre l'annulation d'une sortie déjà annulée sans effet, et refuser l'annulation d'une sortie déjà passée | `CASE-CANCEL-18` | le socle, plus les tâches 1 et 2 | tout le reste |

**Découpage retenu :** une transition d'état, une garantie de non-déclenchement, deux cas limites. La tâche 2 vérifie une absence, elle produira peu de code et beaucoup de valeur.

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
| 3 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Une alerte qui annule à l'expiration.** C'est l'intuition la plus naturelle et elle est fausse : le silence vaut maintien. Un agent qui programme une annulation automatique à l'heure de départ contredit la règle que le client a posée deux fois.
- **Une double annulation qui rejoue les effets.** Le cas 18 le vise directement : un second message et un second remboursement coûtent de l'argent réel et abîment la confiance du client.
- Une règle météo automatisée, sous quelque forme que ce soit. La décision appartient au gérant, seule l'annulation au seuil de 6 inscrits est automatique, et elle vit ailleurs.
