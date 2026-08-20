# Plan de délégation - `SPEC-ADMIN-07`

- **Spécification :** pointage d'un solde encaissé sur place
- **Date :** J9 (2026-08-20), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-18`, `CASE-ADMIN-19`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose l'acompte livré et la table `PAIEMENT` migrée : on ne
pointe que ce qui reste dû après un acompte.

**C'est la seule spécification du projet où l'outil n'agit pas.** Il enregistre
un fait que le gérant lui rapporte. Tout le découpage tourne autour de cette
particularité, et c'est elle qui rend la délégation risquée.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-07` uniquement.
- Les deux fichiers de cas cités, et les deux tests rouges correspondants dans
  `tests/Application/PointageDuSoldeTest.php`.
- `docs/mcd-mld.md` §6 et §7, en particulier la note sur `paiement.annule` et
  sur `paiement.canal`.
- `docs/adr/ADR-006-paiement-en-deux-temps.md`.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, **et l'interface
`PrestataireDePaiement`**, qui n'a rien à faire dans cette spécification.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Enregistrer un pointage : une ligne `PAIEMENT` de canal `SUR_PLACE`, **sans aucun appel au prestataire**, et sans effet si la réservation est déjà soldée | `CASE-ADMIN-18` | le socle commun ci-dessus | tout ce qui touche au paiement en ligne |
| 2 | Porter l'état du solde sur le planning d'embarquement, une ligne soldée se distinguant d'une ligne restant à encaisser | `CASE-ADMIN-18` | le socle, plus la tâche 1, plus `SPEC-ADMIN-03` | le contenu du planning, livré par `SPEC-ADMIN-03` |
| 3 | Rendre le pointage réversible : **marquer la ligne annulée plutôt que la supprimer**, refuser le pointage d'une réservation annulée, et exposer l'historique | `CASE-ADMIN-19` | le socle, plus la tâche 1 | le pointage nominal écrit en 1 |

**Découpage retenu :** un enregistrement, une restitution, une réversibilité.
La tâche 3 est séparée parce que c'est la seule qui décide de la **forme du
stockage**, et qu'une colonne booléenne y passerait les tests nominaux tout en
détruisant l'historique.

---

## Après - ce qui s'est passé

Complété au rituel de 16h15, le même jour.

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | | |
| 2 | | |
| 3 | | |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

---

## Ce que nous surveillons particulièrement ici

- **Un appel au prestataire de paiement.** C'est le risque numéro un, et le
  plus coûteux. Un agent lit « le solde est réglé » et appelle `encaisser()`,
  ce qui débiterait une seconde fois un client qui vient de payer au comptoir.
  Le test le voit, la production ne pardonnerait pas. C'est pourquoi
  l'interface est explicitement hors périmètre.
- **Une suppression de ligne au lieu d'un marquage.** Annuler un pointage en
  effaçant sa ligne passe `CASE-ADMIN-19` sur la réversibilité et échoue sur
  l'historique. `REQ-113` exige les deux gestes, pas l'état final.
- **Un contrôle du montant encaissé.** L'outil ne connaît pas ce que le gérant
  a réellement pris sur son terminal. Un agent voudra vérifier que la somme
  correspond, et inventera une donnée que personne ne lui fournit.
- **Un refus de pointer après le départ.** Cela paraît logique et c'est faux :
  un jour chargé, le gérant régularise au retour. Cas limite 6 de la
  spécification, et une critique de l'IA déjà refusée à J8.
- **Un message envoyé au client quand son solde est pointé.** Rien ne le
  demande, et `CR-06/Q16` ferme la porte à tout message parlant du solde.
