# Plan de délégation - `SPEC-CANCEL-04`

- **Spécification :** information et remboursement des clients d'un créneau annulé
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-CANCEL-10` à `CASE-CANCEL-13`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose `SPEC-CANCEL-02` livrée. **C'est cette spécification qui crée la trace des envois** (`notification`) : `SPEC-CANCEL-05` et `SPEC-CANCEL-06` la réutilisent et ne la recréent pas.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/cancel.md`, section `SPEC-CANCEL-04` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §3 et §4.
- `docs/mcd-mld.md` §6 et §7, pour les tables `sortie`, `reservation` et
  `notification`.
- `docs/adr/ADR-004-envoi-des-sms.md`, pour le double canal et le point de
  défaillance commun.
- `docs/strategie-de-test.md` §9, pour la doublure d'envoi.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et les transitions d'état, livrées par `SPEC-CANCEL-02`, et le tunnel de paiement nominal.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Prévenir par écrit chaque client d'un créneau annulé, sur les deux canaux, et déclencher le remboursement intégral de ce qu'il a payé, sans aucune intervention téléphonique | `CASE-CANCEL-10` | le socle commun ci-dessus | l'état de la sortie, le calcul des montants |
| 2 | Garantir qu'aucun écran ne propose de choix entre report, avoir et remboursement à la suite d'une annulation décidée par le gérant | `CASE-CANCEL-11` | le socle, plus la tâche 1 | l'enregistrement d'une annulation client, qui appartient à `SPEC-ADMIN-06` |
| 3 | Ne déclencher aucun remboursement pour une réservation non payée, et libérer son immobilisation | `CASE-CANCEL-12` | le socle, plus la tâche 1 | le chemin nominal écrit en 1 |
| 4 | Créer la trace des envois : type, canal, date et statut de chaque message, y compris les échecs | `CASE-CANCEL-13` | le socle, plus `mcd-mld.md` §7 sur la table `notification` | les envois eux-mêmes, écrits en tâche 1 |

**Découpage retenu :** une action complète, une garantie d'absence, un cas limite, une trace. La tâche 4 est livrée en dernier mais conditionne les deux spécifications suivantes.

---

## Après - ce qui s'est passé

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |
| 3 | | |
| 4 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un choix entre report, avoir et remboursement proposé au client.** C'est écrit dans `CR-02/Q04`, que l'agent peut lire, et c'est faux depuis la correction du 2026-08-14. La tâche 2 existe uniquement pour attraper cette régression.
- **Un remboursement demandé pour une réservation non payée**, donc d'un montant nul envoyé au prestataire.
- Une trace qui n'enregistre que les envois réussis. L'échec est précisément ce qu'on veut pouvoir montrer à un client qui affirme n'avoir rien reçu.
- Un appel téléphonique suggéré quelque part dans le parcours : le gérant ne téléphone plus.
