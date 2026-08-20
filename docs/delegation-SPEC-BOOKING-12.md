# Plan de délégation - `SPEC-BOOKING-12`

- **Spécification :** règlement du solde après acompte
- **Date :** J9 (2026-08-20), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-BOOKING-40`, `CASE-BOOKING-41`, `CASE-BOOKING-42`

C'est une prévision, pas un compte rendu.

**Dépendance.** Suppose l'acompte livré, donc `SPEC-BOOKING-07` en v2 et la
table `PAIEMENT` migrée : il n'y a pas de solde tant qu'il n'y a pas d'acompte.

**Spécification née de `CR-06`**, reçu le 19 août au soir. Ses trois cas de test
sont **déjà écrits et rouges** : ils fixent l'API avant le code, comme les 76
premiers l'ont fait à J7.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/booking.md`, section `SPEC-BOOKING-12` uniquement.
- Les trois fichiers de cas cités, et les trois tests rouges correspondants
  dans `tests/Application/SoldeDeLaReservationTest.php`.
- `docs/adr/ADR-006-paiement-en-deux-temps.md`, qui fonde les deux
  transactions distinctes.
- `docs/mcd-mld.md` §6 et §7, pour la table `PAIEMENT`.
- `docs/adr/ADR-005-horloge-injectable.md` : la fenêtre est une règle horaire.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le code de
`SPEC-BOOKING-04`, dont la règle de fermeture est **réutilisée** et non
réécrite.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Écrire la fenêtre de règlement comme règle pure : ouverte 24 heures avant le départ, fermée à l'heure de fermeture du créneau. **Réutiliser `Politique\FermetureDesReservations`** pour la borne haute, ne pas la recalculer | `CASE-BOOKING-41` | le socle commun ci-dessus | `FermetureDesReservations`, figée |
| 2 | Encaisser le solde dans une transaction distincte de celle de l'acompte, sur le **montant restant dû** et non sur le total, et rendre l'opération idempotente | `CASE-BOOKING-40` | le socle, plus la tâche 1 | la fenêtre écrite en 1, le calcul de l'acompte |
| 3 | Traiter les deux cas où il n'y a rien à régler : un code couvrant tout le prix, et un créneau annulé dont l'acompte est remboursé | `CASE-BOOKING-42` | le socle, plus les tâches 1 et 2 | `AnnulerCreneau`, livré par `SPEC-CANCEL-02` |

**Découpage retenu :** une règle horaire, un encaissement, deux cas où le solde
est nul. La tâche 1 est séparée parce qu'elle se teste sans base, et parce que
c'est elle qui risque le plus d'être réécrite au lieu d'être réutilisée.

---

## Après - ce qui s'est passé

**Rempli au rituel de 16h15 du J9.**

Comme pour les vingt-six plans de J8, le code a été produit **spécification par
spécification** et non tâche par tâche : `SolderUneReservation` satisfait les
trois cas d'un seul tenant. L'écart de découpage est le même, il est constant, et
il tient à notre façon d'écrire les plans.

L'écart propre à ce plan est ailleurs, et il est plus intéressant : **la tâche 1
décrivait une règle fausse**. Elle demandait une fenêtre ouverte 24 heures avant
le départ. `CR-07/Q12`, reçu le lendemain de l'écriture du plan, a fait ouvrir la
fenêtre avec le lien de règlement, à 7h la veille. Le plan a été suivi, puis la
règle qu'il portait a été remplacée.

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | `redécoupé` | la borne d'ouverture a changé entre l'écriture du plan et celle du code (`CR-07/Q12`). `Politique\FenetreDeReglement` a bien été écrite comme règle pure et réutilise `FermetureDesReservations` pour la borne haute, comme prévu, mais son ouverture n'est plus celle du plan. `CASE-BOOKING-41` a dû basculer du créneau de 7h à celui de **14h** : c'est le seul où les deux formulations divergent, et sur le créneau du matin le test aurait été vert avec la mauvaise règle |
| 2 | `conforme` | l'idempotence tient sans branche dédiée : une réservation soldée a un solde nul, et le service refuse alors pour `RIEN_A_REGLER`. La seconde soumission ne débite rien |
| 3 | `repris` | la doublure `PaiementSimule` a dû être corrigée avant que ce cas ne soit lisible : `montantEncaisse()` rendait le **premier** encaissement, ce qui était sans ambiguïté tant qu'une réservation n'en portait qu'un. Avec l'acompte puis le solde, elle répondait 30 € à une question qui portait sur 70 €. `dernierMontantEncaisse()` a été ajoutée, et l'ancienne méthode documentée comme rendant l'acompte |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

---

## Ce que nous surveillons particulièrement ici

- **Une seconde règle horaire.** L'agent va vouloir écrire « midi la veille »
  dans la fenêtre de règlement, alors que `SPEC-BOOKING-04` le dit déjà. Deux
  règles qui disent la même chose finissent par diverger, et c'est la borne du
  créneau de 14h qui partira la première.
- **Un encaissement sur le montant total.** `reservation.montant` reste le
  montant **dû**, pas le restant. Un agent qui l'encaisse au moment de solder
  débite le client une seconde fois de la totalité.
- **L'idempotence oubliée.** La double soumission est fréquente sur mobile, et
  `SPEC-BOOKING-07` AC-4 a déjà dû la traiter pour l'acompte. Le même défaut
  reviendra sur le solde, parce que c'est un chemin neuf.
- **Une relance « pour aider le client ».** `CR-06/Q16` et `Q17` l'interdisent
  explicitement : aucun message ne mentionne le solde, et personne n'est
  relancé. Un agent trouvera cela dur et voudra corriger le client à sa place.
- **Une exception pour les réservations tardives.** Le point 4 de l'entretien
  laisse croire qu'une réservation prise à moins de 24 heures perdrait le droit
  au paiement en ligne. La fenêtre est **uniforme** ; c'est une hypothèse
  d'équipe, question 20 du §11, et elle ne se code pas autrement.
