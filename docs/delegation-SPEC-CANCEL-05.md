# Plan de délégation - `SPEC-CANCEL-05`

- **Spécification :** message de rappel automatisé avant la sortie
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-21` à `CASE-CANCEL-24`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-CANCEL-04` livrée pour la trace des envois, qui n'est pas à recréer, et `SPEC-BOOKING-07` pour disposer de réservations confirmées.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-05` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `docs/adr/ADR-005-horloge-injectable.md`, indispensable : tout ici est une
  question d'horaire.
- `docs/adr/ADR-004-envoi-des-sms.md`.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et la trace des envois, livrée par `SPEC-CANCEL-04`, et les transitions d'état.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Envoyer le message type à l'horaire configuré, par défaut 24 heures avant le départ, sur les deux canaux, sans action du gérant | `CASE-CANCEL-21` | le socle commun ci-dessus | la trace des envois, le contenu rédactionnel |
| 2 | Appliquer un horaire modifié aux envois à venir, sans rejouer ceux déjà partis | `CASE-CANCEL-22` | le socle, plus la tâche 1 | l'envoi lui-même, figé |
| 3 | Envoyer immédiatement le rappel d'une réservation confirmée après l'horaire programmé, au lieu de ne jamais l'envoyer | `CASE-CANCEL-23` | le socle, plus la tâche 1 | les autres chemins d'envoi |
| 4 | N'envoyer aucun rappel pour un créneau annulé, et isoler l'échec d'un canal de sorte que l'autre parte quand même | `CASE-CANCEL-24` | le socle, plus les tâches 1 à 3 | tout le reste |

**Découpage retenu :** un envoi programmé, une reconfiguration, un rattrapage, deux cas d'exclusion. La tâche 3 est celle qu'on oublie : elle protège les réservations de dernière minute.

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
| 1 | `repris` | aucune table ne stockait la prévision météo saisie par le gérant, que le cas exige dans le message. Table `prevision_meteo` ajoutée au MLD |
| 2 | `conforme` |  |
| 3 | `conforme` |  |
| 4 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un rappel qui n'est jamais envoyé aux réservations tardives.** Les créneaux de 7h et 10h se réservent jusqu'à midi la veille, donc après l'horaire de rappel : sans la tâche 3, ces clients ne reçoivent rien, et c'est fréquent.
- **Un rappel envoyé pour un créneau annulé**, alors que ces clients ont déjà reçu un message d'annulation.
- Un horaire figé à la confirmation de la réservation au lieu d'être lu au moment de l'envoi : une modification par le gérant n'aurait alors aucun effet.
- Une interrogation d'un service météo externe. La prévision est saisie par le gérant, l'application n'appelle personne.
