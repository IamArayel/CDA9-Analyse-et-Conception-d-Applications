# Impact de CR-07 sur le dépôt

**Entretien** : [CR-07](./compte-rendu-entretien-07.md), 2026-08-20 (J9)
**Version du cahier des charges** : v6 → **v7**

CR-07 ne rouvre pas le sujet de l'acompte, tranché en CR-06. Il ferme les douze questions
que la descente de CR-06 avait laissées ouvertes, et deux de ces réponses
**renversent** ce que la v6 avait écrit.

---

## 1. Ce qui change, et pourquoi

| # | Ce qui change | Nature | Ce qui l'a déclenché |
|---|---|---|---|
| 1 | La fenêtre de règlement en ligne s'ouvre **avec le lien**, à 7h la veille, et non 24 heures avant le départ | précision qui déplace une borne | [CR-07/Q12](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues) |
| 2 | **Deux factures** au lieu d'une : acompte, puis solde | **renversement** de REQ-119 | [CR-07/Q07](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues) |
| 3 | Le barème de remboursement est **chiffré** : acompte entier au-delà de 7 jours, 75 % de l'acompte entre 7 jours et 48 heures, commission de 50 % du prix total en deçà | comblement d'un trou | [CR-07/Q11](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues), [Q03](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues) |
| 4 | Un **quatrième message automatique** apparaît : le lien de règlement, envoyé à 7h la veille et tracé comme les trois autres | ajout | [CR-07/Q09](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues), [Q02](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues) |
| 5 | La « boutique » est le lieu d'embarquement, sans horaire propre | confirmation d'une hypothèse d'équipe | [CR-07/Q04](./compte-rendu-entretien-07.md#2-questions-posées-et-réponses-obtenues) |

### La divergence de sept heures, et comment elle a été tranchée

La v6 posait deux règles qui se contredisaient sans qu'on l'ait vu : le lien part
à 7h la veille, et le solde est réglable à partir de 24 heures avant le départ.
Pour les créneaux de 7h et de 10h les deux dates coïncident à quelques heures
près. Pour celui de **14h**, elles divergent de sept heures, et la v6 aurait
laissé un client détenir un lien qu'il n'avait pas encore le droit d'utiliser.

Le client a tranché en une phrase : **la fenêtre s'ouvre avec le lien**. C'est la
seule lecture qui ne demande pas au client de comprendre pourquoi son lien ne
fonctionne pas encore.

### Le barème n'est pas uniforme, et c'est voulu

Les deux premières tranches portent sur **l'acompte**, la troisième sur le **prix
total**. Rapproché, cela donne pour une sortie à 100 € avec 30 € d'acompte :

| Délai avant le départ | Ce que le client récupère | Assiette |
|---|---|---|
| plus de 7 jours | 30,00 € | l'acompte, en entier |
| entre 7 jours et 48 heures | 22,50 € | 75 % de l'acompte |
| moins de 48 heures | 0,00 € | l'acompte moins 50 % du prix total, plafonné à zéro |

La troisième ligne mange l'acompte en entier et le plafond de `R-25` empêche de
réclamer les 20 € manquants. C'est cohérent avec ce que le gérant décrivait en
CR-06 point 5 : à ce stade, il garde l'acompte et n'en demande pas plus.

---

## 2. Ce que la descente a effectivement touché

| Artefact | Ce qui y change |
|---|---|
| [cahier-des-charges.md](./cahier-des-charges.md) | REQ-111 précisée, **REQ-119 renversée**, REQ-120 et REQ-121 ajoutées, R-27 corrigée, R-31 à R-33 ajoutées, questions 16, 18, 20 et 21 fermées, ligne v7 au §12 |
| [specs/booking.md](../specs/booking.md) | `SPEC-BOOKING-12` v2 : la fenêtre s'ouvre avec le lien |
| [specs/admin.md](../specs/admin.md) | `SPEC-ADMIN-06` v3 : le barème chiffré, en tableau |
| [tests/cases/CASE-BOOKING-41.md](../tests/cases/CASE-BOOKING-41.md) | bascule du créneau de 7h à celui de **14h**, le seul où les deux règles divergent |
| [tests/cases/CASE-ADMIN-16.md](../tests/cases/CASE-ADMIN-16.md) | les trois tranches, 30 €, 22,50 €, 0 € |
| `src/Domaine/Politique/` | `Acompte`, `FenetreDeReglement`, `RetenueDannulation` |
| `src/Domaine/Entite/Paiement.php` + mapping + migration | le journal des versements |
| [specs/cancel.md](../specs/cancel.md) | `SPEC-CANCEL-07` **neuve** : le lien de règlement, quatrième message automatique |
| [tests/cases/CASE-CANCEL-25.md](../tests/cases/CASE-CANCEL-25.md) | **neuf** : 7h la veille, courriel seul, rien pour un solde nul |
| `src/Application/` | `SolderUneReservation`, `PointerLeSolde`, `ConsulterLesPaiements`, `EnregistrerUneAbsence` ; `ConfirmerLePaiement`, `AnnulerCreneau`, `ControlerSeuilDeMaintien`, `EnregistrerUneIssueDannulation`, `ExporterLePlanning`, `ConsulterUneReservation` repris ; `Envoi\LienDeReglement` neuf, branché sur `ConfirmerLePaiement` et sur la tâche programmée |

---

## 3. Ce qui n'est pas codé, et qui est déclaré

Une seule exigence de CR-07 reste sans code, et il vaut mieux l'écrire ici que la
laisser découvrir :

- **REQ-119**, les deux factures. Ni celle d'acompte, ni celle de solde n'est
  produite. L'exigence est *Should*, et la facturation touche la fiscalité : une
  facture à moitié juste vaut moins qu'aucune facture. Quatre points restent
  d'ailleurs ouverts au §8 de `CR-07`, dont la date à porter sur la facture d'un
  solde encaissé au comptoir.

**REQ-120 et REQ-121, en revanche, sont implémentées** : `SPEC-CANCEL-07` a été
écrite à J9, `CASE-CANCEL-25` la couvre, et `Envoi\LienDeReglement` envoie le
lien à 7h la veille par courriel seul. La trace est gratuite, `EnvoyerUnMessage`
la posant déjà pour les trois autres messages. Ce qui manque à cette
spécification est son **plan de délégation** : elle a été écrite directement, et
c'est déclaré.

Le détail est dans [traceability-trous.md](./traceability-trous.md).

---

## 4. Ce qui reste à demander au client

Cinq questions ouvertes par cet entretien, listées au §8 de
[CR-07](./compte-rendu-entretien-07.md) : l'heure d'envoi du lien doit-elle être
réglable comme celle de l'alerte, que contient le message, le lien expire-t-il,
quelle date porte la facture d'un solde encaissé au comptoir, et que devient la
facture d'acompte d'une réservation annulée.
