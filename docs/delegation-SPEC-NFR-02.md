# Plan de délégation - `SPEC-NFR-02`

- **Spécification :** site bilingue français et anglais
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-NFR-01`, `CASE-NFR-02`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-11` livrée pour le parcours de réservation, et les trois messages automatiques livrés par `SPEC-CANCEL-04`, `05` et `06`. C'est la dernière spécification à déléguer du parcours client : elle traverse tout ce qui a été écrit avant.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/non-fonctionnel.md`, section `SPEC-NFR-02` uniquement.
- Les deux fichiers de cas cités.
- `specs/booking.md`, section `SPEC-BOOKING-11`, en lecture seule.
- `docs/architecture.md` §2 et §4.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les règles métier : traduire n'est jamais modifier un comportement.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Envoyer les trois messages automatiques dans la langue enregistrée sur la réservation, le français s'appliquant quand le client n'a exprimé aucun choix | `CASE-NFR-01` | le socle commun ci-dessus | le déclenchement des messages, leur contenu métier, les règles d'envoi |
| 2 | Garantir que les deux catalogues de traduction portent exactement les mêmes clés, sans valeur vide, gabarits des messages compris | `CASE-NFR-02` | le socle, plus la tâche 1 | les libellés eux-mêmes, qui relèvent de la rédaction et non du code |

**Découpage retenu :** un comportement, une garantie structurelle. La tâche 2 ne traduit rien : elle installe le garde-fou qui fera échouer la construction le jour où un contenu sera ajouté sans sa traduction.

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

- **La langue lue sur la requête plutôt que sur la réservation.** Un message part parfois plusieurs heures après, sans navigateur en face : la seule source valable est la langue enregistrée au moment de réserver.
- **L'espace de gestion traduit.** Le gérant est l'unique utilisateur et il est francophone : c'est un coût sans bénéficiaire.
- Des libellés écrits en dur dans les gabarits, qui échapperaient au contrôle de la tâche 2.
- Une conversion de devise : les montants restent en euros dans les deux langues.
