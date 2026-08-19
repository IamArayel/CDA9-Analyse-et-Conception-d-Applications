# Plan de délégation - `SPEC-CANCEL-06`

- **Spécification :** alerte météo préventive
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-01` à `CASE-CANCEL-09`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-CANCEL-02` livrée pour l'annulation et `SPEC-CANCEL-04` pour la trace des envois. C'est le différenciant du projet, et la spécification la plus étendue du domaine.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-06` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `docs/adr/ADR-005-horloge-injectable.md` et `ADR-004`.
- `docs/strategie-de-test.md` §9, pour les doublures d'horloge et d'envoi.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et l'annulation elle-même, la trace des envois, et le parcours de réservation.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Mettre un créneau en alerte depuis l'espace de gestion, créneau par créneau, en couvrant les deux bateaux du créneau et sans toucher aux autres créneaux du jour | `CASE-CANCEL-01` | le socle commun ci-dessus | l'annulation, les envois |
| 2 | Garantir qu'aucune alerte ne se déclenche sans action du gérant, y compris à l'approche de l'horaire d'envoi | `CASE-CANCEL-02` | le socle, plus la tâche 1 | la mise en alerte écrite en 1 |
| 3 | Envoyer le message d'alerte la veille à 18h, sur les deux canaux, aux clients inscrits | `CASE-CANCEL-03` | le socle, plus la tâche 1 | la trace des envois, livrée ailleurs |
| 4 | N'envoyer aucun second message lorsque la sortie est maintenue : le silence vaut maintien | `CASE-CANCEL-04` | le socle, plus la tâche 3 | les envois écrits en 3 |
| 5 | Envoyer le message de confirmation d'annulation 2 heures avant l'heure de départ prévue | `CASE-CANCEL-05` | le socle, plus les tâches 3 et 4 | la décision d'annuler, livrée par `SPEC-CANCEL-02` |
| 6 | Laisser un créneau en alerte réservable jusqu'à son heure de fermeture habituelle, l'alerte courant elle jusqu'à l'heure de départ | `CASE-CANCEL-06` | le socle, plus la tâche 1 | la règle de fermeture, livrée par `SPEC-BOOKING-04` |
| 7 | Envoyer la confirmation d'annulation à tout client inscrit **au moment de l'annulation**, y compris à celui qui a réservé après l'alerte | `CASE-CANCEL-07` | le socle, plus la tâche 5 | les autres chemins d'envoi |
| 8 | Envoyer immédiatement une alerte posée après l'horaire programmé, au lieu de la reporter au lendemain | `CASE-CANCEL-08` | le socle, plus la tâche 3 | le chemin nominal écrit en 3 |
| 9 | Rendre l'heure d'envoi de l'alerte et le délai de confirmation réglables depuis l'espace de gestion, les envois à venir suivant les nouvelles valeurs | `CASE-CANCEL-09` | le socle, plus les tâches 3 et 5 | tout le reste |

**Découpage retenu :** une transition d'état, une garantie de non-déclenchement, quatre chemins d'envoi, une règle de réservabilité, un réglage. Neuf tâches pour neuf cas, comme sur `SPEC-BOOKING-03` : c'est la seconde spécification la plus exposée du projet.

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
| 4 | `repris` | l'assertion comptait **tous** les messages, alors que le fichier de cas exclut explicitement le rappel, « qui part indépendamment » |
| 5 | `conforme` |  |
| 6 | `conforme` |  |
| 7 | `conforme` |  |
| 8 | `conforme` |  |
| 9 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un message envoyé quand la sortie est maintenue.** L'agent voudra rassurer le client. Le client a répondu deux fois que non : le silence vaut maintien, et un message de plus contredirait la règle.
- **Une alerte qui annule.** L'alerte ne décide rien, elle prévient. C'est `SPEC-CANCEL-02` qui annule, sur décision du gérant.
- **Une confirmation envoyée aux seuls destinataires de l'alerte.** Un client inscrit après l'alerte doit la recevoir : la liste se calcule au moment de l'annulation, pas au moment de l'alerte.
- Une alerte posée à 21h qui attendrait le lendemain 18h pour partir.
- Une alerte qui ne couvrirait qu'un bateau sur les deux du créneau : la météo ne distingue pas les bateaux.
