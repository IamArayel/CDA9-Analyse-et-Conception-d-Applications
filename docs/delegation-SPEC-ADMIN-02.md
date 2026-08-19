# Plan de délégation - `SPEC-ADMIN-02`

- **Spécification :** modification des tarifs
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-04`, `CASE-ADMIN-05`, et `CASE-BOOKING-32` livré par `SPEC-BOOKING-06`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-ADMIN-01` livrée pour la session, et `SPEC-BOOKING-06` livrée pour le calcul du montant. **`CASE-BOOKING-32` est déjà couvert par la tâche 2 de `SPEC-BOOKING-06` : il n'est pas à refaire ici.**

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-02` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.
- `specs/booking.md`, section `SPEC-BOOKING-06`, en lecture seule.
- `docs/mcd-mld.md` §5, paragraphe « `reservation` ne référence pas `tarif` ».

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le calcul du montant, livré par `SPEC-BOOKING-06`, et le forfait de privatisation, porté par la table `bateau`.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Modifier un tarif depuis l'espace de gestion et l'appliquer aux réservations créées ensuite, sans changer le montant d'une réservation déjà payée | `CASE-ADMIN-04` | le socle commun ci-dessus | le calcul du montant, le récapitulatif client |
| 2 | Refuser à la saisie un tarif négatif ou nul, et laisser la grille inchangée | `CASE-ADMIN-05` | le socle, plus la tâche 1 | la modification écrite en 1 |

**Découpage retenu :** une modification, un refus. Deux tâches seulement, parce que la règle la plus délicate, le montant figé, est livrée avec la tarification et non ici.

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

- **Une propagation du nouveau tarif aux réservations existantes.** C'est ce que produit une jointure vers `tarif` au moment de l'affichage, et cela change le montant d'une réservation déjà payée.
- Un tarif à 0 € accepté : le client n'a jamais prévu de sortie gratuite, et le refus est une décision d'équipe assumée.
- Un historique des tarifs successifs, que personne n'a demandé : seule la valeur en cours est conservée.
