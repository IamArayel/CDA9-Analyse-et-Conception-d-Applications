# Plan de délégation - `SPEC-BOOKING-11`

- **Spécification :** parcours de réservation bilingue français et anglais
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-35`, `CASE-BOOKING-36`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose le parcours de réservation livré. Ne suppose pas les messages automatiques, traités par `SPEC-NFR-02`.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-11` uniquement.
- Les deux fichiers de cas cités.
- `specs/non-fonctionnel.md`, section `SPEC-NFR-02`, en lecture seule.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code métier livré pour les autres spécifications : traduire n'est pas modifier une règle.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Appliquer le français par défaut quand le client n'exprime aucun choix, sans détection du navigateur, et conserver les données déjà saisies lors d'un changement de langue | `CASE-BOOKING-36` | le socle commun ci-dessus | le domaine, les règles de réservation |
| 2 | Traduire l'intégralité du parcours, écrans, boutons et **messages de validation compris**, sans laisser un libellé en français en version anglaise | `CASE-BOOKING-35` | le socle, plus la tâche 1 | les montants, qui restent en euros dans les deux langues |

**Découpage retenu :** le comportement d'abord, la traduction ensuite. L'inverse laisserait passer la perte de saisie, qui est le vrai défaut d'usage.

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

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Les messages d'erreur de validation oubliés.** C'est là que les libellés non traduits se logent, parce qu'on ne les voit qu'en se trompant.
- Une détection automatique de la langue du navigateur, que le client n'a pas demandée et qui rendrait le comportement imprévisible.
- Une conversion de devise : le tarif reste en euros quelle que soit la langue.
