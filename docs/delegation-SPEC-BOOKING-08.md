# Plan de délégation - `SPEC-BOOKING-08`

- **Spécification :** accessibilité multi-support
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-37`, au statut `manuel assumé`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose le parcours de réservation livré de bout en bout, donc `SPEC-BOOKING-01` à `07`.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-08` uniquement.
- Le cas `CASE-BOOKING-37` et son protocole de vérification.
- `docs/strategie-de-test.md` §4, qui motive l'absence d'automatisation.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et tout le code métier : cette spécification ne porte que sur la présentation.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Rendre le parcours utilisable sans défilement horizontal sur une largeur de 320 pixels, et sur les trois familles d'appareils | `CASE-BOOKING-37` | le socle commun ci-dessus | le domaine, l'application, la base de données |

**Découpage retenu :** une seule tâche, et un seul cas, vérifié à la main. Le plan existe quand même : une tâche confiée à l'agent sans cadrage écrit reste une tâche non cadrée, même quand elle est unique.

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
| 1 | `non exécutée` | spécification à vérification manuelle, sur trois appareils avant la présentation. Aucune tâche ne lui a été confiée aujourd'hui |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Aucune règle métier ne doit apparaître dans cette tâche.** Un agent qui touche au domaine pour arranger un affichage a franchi une couche.
- Une vérification déclarée faite sans les trois captures prévues au protocole du cas : sans preuve, la vérification n'a pas eu lieu.
