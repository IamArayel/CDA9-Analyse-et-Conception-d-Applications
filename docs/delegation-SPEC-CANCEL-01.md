# Plan de délégation - `SPEC-CANCEL-01`

- **Spécification :** visualisation des clients inscrits avant décision
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-14`, `CASE-CANCEL-15`

C'est une prévision, pas un compte rendu.

**Dépendance.** Première spécification du domaine CANCEL à déléguer : elle ne fait que lire. Suppose `SPEC-BOOKING-03` livrée, pour disposer de sorties et de réservations.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-01` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `specs/non-fonctionnel.md`, section `SPEC-NFR-04`, en lecture seule, pour
  les données affichables.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et toute écriture, quelle qu'elle soit : cette spécification est en lecture seule.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Afficher, pour un créneau à venir, la liste de ses clients inscrits avec nom, contact et nombre de participants. Une réservation non payée n'y figure pas | `CASE-CANCEL-14` | le socle commun ci-dessus | l'état de la sortie, les envois, toute écriture en base |
| 2 | Afficher une liste vide sans erreur sur un créneau sans inscrit, et indiquer la date d'envoi de l'alerte sur un créneau déjà alerté | `CASE-CANCEL-15` | le socle, plus la tâche 1 | la mise en alerte elle-même, qui appartient à `SPEC-CANCEL-06` |

**Découpage retenu :** deux tâches de lecture. Le découpage sépare le chemin normal des deux cas limites, parce que ce sont eux qui trahissent une requête mal filtrée.

---

## Après - ce qui s'est passé

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un effet de bord sur la consultation.** Le cas 1 l'exige explicitement : consulter ne déclenche ni alerte, ni annulation, ni envoi. Un agent qui en profite pour marquer la sortie « vue » a écrit dans une tâche de lecture.
- Une liste vide traitée comme une erreur. Hors saison, c'est le cas normal.
- Des réservations non payées listées comme inscrits : elles ne réservent aucune place et n'ont personne à prévenir.
