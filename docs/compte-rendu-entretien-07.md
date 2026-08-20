# Compte rendu d'entretien n° 7

- **Date :** 2026-08-20
- **Durée :** …
- **Interlocuteur :** le commanditaire (armateur, Ti Baleine)
- **Présents pour l'équipe :** Chloé et Anthony
- **Source brute :** échange oral en trois passes. L'équipe soumet les huit
questions restées ouvertes au §8 de
[`compte-rendu-entretien-06.md`](./compte-rendu-entretien-06.md), le client y
répond en ajoutant un dispositif non demandé, puis deux reformulations lèvent
les contradictions que ses réponses avaient créées.

> ⚠️ **Statut particulier de ce compte rendu.** Comme les trois précédents, il
> ne s'appuie sur aucune source brute écrite. Les identifiants `CR-07/Qnn` sont
> utilisables dès à présent par le cahier des charges, mais ce document doit
> être relu par la personne qui a mené l'échange.

> **Cet entretien clôt `CR-06` et l'étend.** Les huit questions ouvertes sont
> répondues, deux hypothèses d'équipe sont confirmées, une réponse antérieure
> est renversée, et **un quatrième message automatique apparaît** : le lien de
> paiement du solde, que personne n'avait demandé et que le client considérait
> comme allant de soi.

---

## 1. Ce que le client a dit

Le client commence par un point que l'équipe n'avait pas posé, et qui éclaire
tout le reste :

> Une fois que le client réserve sa place et règle son acompte, **un lien lui
> est envoyé par mail la veille de la sortie** pour régler en ligne le solde
> restant. Il peut alors soit régler en ligne, soit payer par carte le jour
> même de la sortie.

Ce lien n'était mentionné dans aucun document. Il explique rétrospectivement la
réponse `CR-06/Q17`, « il se débrouille » : le client considérait qu'un lien
avait déjà été envoyé, et qu'aucune **relance** n'était donc nécessaire. La
distinction entre l'envoi initial et la relance n'avait jamais été faite.

Sur le plan des factures, il invoque une contrainte qu'il n'avait pas
mentionnée : un acompte perçu doit faire l'objet d'une **facture d'acompte**
portant la TVA exigible à l'encaissement, suivie d'une **facture de solde**
faisant référence à la première.

---

## 2. Questions posées et réponses obtenues

**Chaque question reçoit un identifiant `Qnn`.** `CR-07/Q03` désigne la
question 3 de ce compte rendu. La numérotation est définitive, on n'insère pas,
on ajoute à la suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | La fenêtre de paiement en ligne du solde court-elle de 24 heures avant le départ jusqu'à l'heure de fermeture du créneau, comme l'équipe l'avait déduit ? | Oui. **Révisée ensuite par `Q12`** : la fenêtre s'ouvre avec l'envoi du mail, et non 24 heures avant le départ |
| Q02 | Une réservation prise à moins de 24 heures du départ interdit-elle le paiement en ligne du solde ? | Non. Le client garde le droit de payer en ligne jusqu'à la fermeture du créneau, **et le mail lui est envoyé dès le règlement de l'acompte** |
| Q03 | Quel taux s'applique à une annulation à moins de 24 heures, et à un client absent au départ ? | Un client absent **ne récupère rien**. Pour une annulation à moins de 24 heures, il récupère **50 % du prix total s'il a soldé**, et **rien s'il n'a versé que l'acompte** |
| Q04 | La « boutique » est-elle le lieu d'embarquement ou un point de vente distinct ? | Le **lieu d'embarquement**, sans horaire propre |
| Q05 | Un client ayant soldé en ligne puis annulant relève-t-il du barème sur le prix total ? | Oui. Et il ne peut avoir soldé que peu avant le départ, donc il relève de la tranche basse : **50 % du prix total** |
| Q06 | Le barème de remboursement s'applique-t-il aussi aux privatisations ? | Oui, à l'identique |
| Q07 | La facture unique doit-elle être émise par l'outil, puisqu'un solde encaissé au quai est invisible du prestataire ? | **Deux factures, pas une** : une facture d'acompte à l'encaissement, une facture de solde ensuite, référençant la première. Obligation légale et fiscale, TVA exigible à l'encaissement de l'acompte |
| Q08 | Un client qui annule au-delà de 48 heures récupère-t-il la part de son acompte qui excède la commission ? | **Question jugée mal posée, reformulée en `Q11`** |
| Q09 | À quelle heure part le mail portant le lien de paiement ? | À **7h du matin la veille**, quel que soit le créneau du lendemain |
| Q10 | Ce mail est-il tracé comme les trois autres messages automatiques ? | Oui, « puisque les 3 autres le sont autant rester sur le même fonctionnement » |
| Q11 | Sur une sortie à 100 € avec 30 € d'acompte : au-delà de 7 jours, et entre 7 jours et 48 heures, que récupère le client ? | **La totalité de son acompte** au-delà de 7 jours, et **75 % de son acompte** entre 7 jours et 48 heures. Un client absent ayant soldé ne récupère **rien** |
| Q12 | Le mail de 7h arrive avant l'ouverture de la fenêtre pour les créneaux de 10h et 14h. Faut-il ouvrir la fenêtre avec le mail, ou laisser le lien inactif quelques heures ? | **La fenêtre s'ouvre avec le mail** |

---

## 3. Ce que nous avons compris

**Le lien de paiement était le chaînon manquant.** Sans lui, `CR-06`
décrivait un solde que le client devait penser à payer sans que rien ne le lui
rappelle, ce que l'équipe avait signalé comme incohérent. Le client n'avait pas
répondu à côté : il avait un envoi en tête depuis le début et ne l'avait pas
énoncé, parce qu'il lui paraissait évident.

**La fenêtre de paiement change de repère.** Elle ne part plus de l'heure de
départ mais de **7h du matin la veille**, heure fixe, et se ferme à l'heure de
fermeture des réservations du créneau. Elle devient donc plus longue pour les
créneaux tardifs, et le lien envoyé fonctionne toujours au moment où il arrive.

**Le barème de remboursement n'est pas uniforme, et c'est délibéré.** Deux
formules coexistent, chacune confirmée séparément par le client :

- **au-delà de 48 heures**, la commission s'applique à ce que le client a
  **versé** ;
- **en deçà de 48 heures**, elle s'applique au **prix total** puis est
  plafonnée par le versé.

Le client a choisi la première en connaissant l'alternative, après que l'équipe
lui a montré l'écart chiffré. Ce n'est donc pas une inadvertance, et la règle
ne doit pas être « harmonisée » par quiconque la trouverait bancale.

**Les deux factures ne sont plus une préférence.** `CR-06/Q11` demandait une
facture unique ; le client la remplace par deux, en invoquant l'exigibilité de
la TVA à l'encaissement de l'acompte. Cela **résout** la tension avec
`REQ-018` : le prestataire ne peut pas émettre la facture de solde d'un
paiement encaissé au comptoir, donc c'est l'outil qui émet les deux.

---

## 4. Parties prenantes identifiées

| Partie prenante | Rôle dans ce changement |
|---|---|
| Le client final | reçoit un lien de paiement, règle en ligne ou au quai, reçoit deux factures |
| Le gérant | pointe le solde encaissé au quai, ce qui déclenche la seconde facture |
| L'administration fiscale | impose la facture d'acompte avec TVA exigible à l'encaissement |
| L'équipe | voit deux de ses hypothèses confirmées, `Q01` et `Q02` |

---

## 5. Règles métier découvertes

| # | Règle | Formulation rapportée du client | Sûre ? |
|---|---|---|---|
| 1 | Un mail portant un lien de paiement du solde est envoyé à 7h du matin la veille de la sortie | « un lien lui est envoyé par mail la veille de la sortie pour régler en ligne le solde restant » (`Q09`) | oui |
| 2 | Pour une réservation prise à moins de 24 heures du départ, ce mail part dès le règlement de l'acompte | `Q02` | oui |
| 3 | La fenêtre de paiement en ligne du solde s'ouvre avec l'envoi du mail et se ferme à l'heure de fermeture du créneau | `Q12` | oui |
| 4 | Ce mail est un quatrième message automatique, tracé comme les trois autres | `Q10` | oui |
| 5 | Au-delà de 7 jours, le client récupère la totalité de ce qu'il a versé | `Q11` | oui |
| 6 | Entre 7 jours et 48 heures, il récupère 75 % de ce qu'il a versé | `Q11` | oui |
| 7 | En deçà de 48 heures, il récupère 50 % du prix total s'il a soldé, et rien s'il n'a versé que l'acompte | `Q03`, `Q05` | oui, **mais la formule diffère de celle des règles 5 et 6**, voir §6 |
| 8 | Un client absent au départ ne récupère rien, qu'il ait soldé ou non | `Q03`, `Q11` | oui |
| 9 | Le barème s'applique aux privatisations comme aux sorties | `Q06` | oui |
| 10 | Deux factures sont émises par l'outil : une d'acompte à l'encaissement, une de solde ensuite, référençant la première | `Q07` | oui |
| 11 | Le lieu d'embarquement fait office de boutique, sans horaire propre | `Q04` | oui |

---

## 6. Ambiguïtés détectées

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « il perd juste l'acompte » (`Q03`) contre « 75 % de son acompte » (`Q11`) | (a) la commission s'applique au versé dans toutes les tranches, et 48h-24h rendrait 15 € (b) la commission s'applique au versé au-dessus de 48h et au total en dessous, et 48h-24h rend 0 € | **oui, levée par `Q11`** : lecture (b). L'écart de 15 € par annulation a été montré au client, qui a confirmé les deux valeurs séparément |
| 2 | « la veille » (`Q09`) | (a) la veille calendaire à heure fixe (b) 24 heures avant le départ | **oui, levée par `Q09` et `Q12`** : lecture (a), 7h du matin, et la fenêtre s'aligne sur le mail |
| 3 | Portée du mail pour une réservation prise le jour même | (a) aucun mail, le client règle au quai (b) le mail part immédiatement | **oui, levée par `Q02`** : lecture (b) |
| 4 | « ne récupère rien » pour un absent (`Q11`) | (a) il perd son acompte, la part soldée lui revient (b) il perd tout ce qu'il a versé, jusqu'à 100 % du prix | **oui, levée par `Q11`** : lecture (b), y compris pour un client ayant tout payé |

**Sur l'ambiguïté 1.** Le tableau soumis au client, et qu'il a validé :

| Quand il annule | Versé | Commission | Récupère |
|---|---|---|---|
| plus de 7 jours avant | 30 € | 0 % du versé | **30 €** |
| entre 7 jours et 48h | 30 € | 25 % du versé | **22,50 €** |
| entre 48h et 24h, non soldé | 30 € | 50 % du total, plafonnée | **0 €** |
| moins de 24h, non soldé | 30 € | 50 % du total, plafonnée | **0 €** |
| moins de 24h, soldé | 100 € | 50 % du total | **50 €** |
| absent, quel que soit le versé | 30 ou 100 € | totalité | **0 €** |

---

## 7. Contraintes évoquées

**La TVA est exigible à l'encaissement de l'acompte.** C'est la première
contrainte réglementaire citée par le client depuis le début de la mission, et
elle a une conséquence directe : la facture d'acompte ne peut pas attendre le
solde. Elle interdit la facture unique de `CR-06/Q11`.

**La facture de solde doit référencer la facture d'acompte.** Il faut donc que
les deux portent un numéro, et que le lien entre elles soit conservé.

**Le mail de 7h ne suit pas les autres horaires réglables.** L'alerte météo et
le rappel ont leur heure d'envoi paramétrable ; le client n'a rien dit de
tel pour ce lien. En l'absence de réponse, l'heure est figée.

---

## 8. Questions à poser au prochain entretien

| # | Question | Pourquoi elle compte |
|---|---|---|
| 1 | L'heure d'envoi du lien, 7h, doit-elle être réglable depuis l'espace de gestion comme le sont l'alerte et le rappel ? | trois horaires y sont déjà réglables ; en figer un quatrième sans le demander serait une incohérence d'ergonomie |
| 2 | Que contient le mail du lien, et faut-il y rappeler le montant restant dû ? | le contenu rédactionnel des messages n'est toujours pas fourni, et celui-ci porte une somme |
| 3 | Le lien de paiement doit-il expirer, et si oui quand ? | un lien qui reste valide après la fermeture du créneau permettrait un paiement hors fenêtre |
| 4 | La facture de solde doit-elle partir aussi lorsque le solde est encaissé au quai, avec quelle date d'émission ? | `Q07` dit oui, mais la date fiscale d'un encaissement au comptoir n'est pas celle du pointage |
| 5 | Une réservation dont le solde n'est jamais réglé et dont le client était présent laisse-t-elle une facture de solde en attente ? | cas non abordé, et il produit une facture d'acompte orpheline |

---

## 9. Ce que nous n'avons pas abordé

- **La numérotation des factures**, qui est elle aussi réglementée et dont
  aucune règle n'a été donnée.
- **Le sort du lien de paiement si le client change d'adresse e-mail** entre la
  réservation et la veille de la sortie.
- **L'articulation entre le lien de paiement et l'alerte météo** : un client
  peut recevoir à 7h un lien pour payer une sortie mise en alerte à 18h la
  veille, donc déjà signalée comme risquant d'être annulée.
- **Le remboursement d'une facture déjà émise**, qui suppose un avoir au sens
  comptable et non au sens du bon de réduction déjà spécifié.
