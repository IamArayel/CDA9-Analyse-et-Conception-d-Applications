# Plan de délégation - `SPEC-BOOKING-01`

- **Spécification :** formulaire et validité d'une réservation standard
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-20` à `CASE-BOOKING-23`

C'est une prévision, pas un compte rendu.

**Dépendance.** Cette spécification suppose que l'offre de créneaux existe, donc que `SPEC-BOOKING-02` soit livrée. Elle ne suppose ni le calcul du montant, ni le paiement, ni la capacité : une réservation naît à l'état « en attente de paiement » et rien de plus.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-01` uniquement.
- Les quatre fichiers de cas cités, en lecture.
- `docs/architecture.md` §2 et §4.
- `docs/mcd-mld.md` §6 et §7, pour la table `reservation` et ses contraintes.
- `docs/strategie-de-test.md` §7 et §9.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code
livré pour `SPEC-BOOKING-02`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire la règle de composition d'un groupe : au moins un participant, et au moins un adulte dès qu'un enfant est déclaré. Règle pure, aucune persistance | `CASE-BOOKING-21` | le socle commun ci-dessus | la base de données, l'interface, la validation des coordonnées |
| 2 | Écrire la validation des coordonnées : expression régulière sur l'e-mail, contrôle du mobile, et **normalisation du numéro** par retrait des points, tirets et espaces avant enregistrement | `CASE-BOOKING-23` | le socle, plus le résultat de la tâche 1 | la règle de composition, figée |
| 3 | Créer la réservation à l'état « en attente de paiement » à partir d'un formulaire complet, sans calculer de montant ni décompter de place | `CASE-BOOKING-20` | le socle, plus les tâches 1 et 2 | les deux règles précédentes, la table `sortie`, le calcul du montant |
| 4 | Afficher l'avertissement d'interdiction aux enfants de moins de 4 ans avant la validation du formulaire, **sans aucun contrôle bloquant** | `CASE-BOOKING-22` | le socle, plus la tâche 3 | tout le code métier écrit en 1, 2 et 3 |

**Découpage retenu :** deux règles pures, une écriture, un affichage. La
tâche 4 est volontairement seule : elle ne doit produire qu'un texte, et
aucune ligne de règle métier.

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
| 4 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **La tâche 4 est un piège tendu à l'agent.** La spécification dit que la limite des 4 ans est affichée et non contrôlée, parce qu'aucun âge n'est collecté. Un agent qui ajoute un champ d'âge ou un contrôle bloquant aura produit du code conforme à son intuition et contraire au client, qui a explicitement refusé toute information supplémentaire.
- Un contrôle de coordonnées écrit dans le contrôleur plutôt que dans le domaine : c'est la première ligne de la colonne « ce qui n'a rien à y faire » de `architecture.md` §2.
- Une réservation créée avec un montant : le montant relève de `SPEC-BOOKING-06`, il n'a rien à faire ici.
