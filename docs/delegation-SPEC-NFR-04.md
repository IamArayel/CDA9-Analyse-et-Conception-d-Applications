# Plan de délégation - `SPEC-NFR-04`

- **Spécification :** données personnelles et durée de conservation
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-NFR-03`, `CASE-NFR-04`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-BOOKING-01` livrée pour les champs collectés, `SPEC-BOOKING-07` pour le paiement, et `SPEC-BOOKING-09` pour les bons cadeaux, dont la tâche 2 doit préserver les données.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/non-fonctionnel.md`, section `SPEC-NFR-04` uniquement.
- Les deux fichiers de cas cités.
- `docs/mcd-mld.md` §6 et §7, pour la liste exacte des colonnes.
- `docs/adr/ADR-005-horloge-injectable.md`, pour le calcul du délai.
- `docs/adr/ADR-004-envoi-des-sms.md`, qui ajoute un sous-traitant.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les règles métier de réservation, de paiement et de bon cadeau.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Garantir qu'aucun champ hors du formulaire de réservation n'est stocké, et qu'aucune donnée de carte bancaire n'existe nulle part, journaux techniques compris | `CASE-NFR-03` | le socle commun ci-dessus | le tunnel de paiement, la collecte du formulaire |
| 2 | Supprimer ou anonymiser les données personnelles d'une réservation trois mois après la sortie, **en préservant celles nécessaires à un bon cadeau non consommé** jusqu'à son expiration | `CASE-NFR-04` | le socle, plus la tâche 1 | les bons cadeaux eux-mêmes, la trace des envois |

**Découpage retenu :** une garantie de non-collecte, une purge. La tâche 2 porte une exception qui vient d'une contradiction relevée en revue du modèle, et c'est elle qu'il faut surveiller.

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

- **Une purge qui emporte les données d'un bon cadeau encore valable.** Trois mois de conservation contre un an de validité : sans l'exception, un bon acheté devient inutilisable au bout d'un trimestre. C'est la contradiction trouvée en revue du MCD.
- **Une donnée personnelle écrite dans un journal technique.** Elle échappe alors à la purge, et personne ne la voit passer.
- Une suppression en cascade qui emporterait la trace des envois, dont le gérant a besoin pour répondre à un client.
- Un champ ajouté « pour plus tard » : la liste des colonnes est exactement celle du formulaire, ni plus ni moins.
