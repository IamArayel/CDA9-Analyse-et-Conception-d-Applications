# Compte rendu d'entretien n° 6

**Date :** 2026-08-19
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …
**Source brute :** échange oral. Le client formule d'abord une demande
nouvelle, en quatre points sur le paiement et un cinquième sur le
remboursement, puis répond à dix-huit questions préparées par l'équipe à
partir de la relecture des exigences de paiement et d'annulation.

> ⚠️ **Statut particulier de ce compte rendu.** Comme
> [`compte-rendu-entretien-04.md`](./compte-rendu-entretien-04.md) et
> [`compte-rendu-entretien-05.md`](./compte-rendu-entretien-05.md), il ne
> s'appuie sur aucune source brute écrite. La colonne « formulation du
> client » du [§5](#5-règles-métier-découvertes) reproduit les propos **tels
> que rapportés par l'équipe**. Les identifiants `CR-06/Qnn` sont utilisables
> dès à présent par le cahier des charges, mais ce document doit être relu
> par la personne qui a mené l'échange avant d'être considéré comme
> définitif.

> ⚠️ **Cet échange renverse une exigence, il ne l'ajuste pas.** `REQ-017` dit
> aujourd'hui mot pour mot : « Le paiement de la totalité du montant est exigé
> en ligne, par carte bancaire, au moment de la réservation. **Aucun acompte**,
> et aucun autre moyen de paiement n'est accepté en ligne. » Le client demande
> exactement l'inverse. C'est le changement de besoin le plus profond depuis le
> début de la mission, et il arrive à J8, une fois le code écrit et les
> 76 tests au vert.

---

## 1. Ce que le client a dit

Quatre points sur le paiement, un cinquième sur le remboursement.

1. Pour les réservations de sorties dauphins et baleines, le client paie un
   **acompte de 30 % fixe** à la réservation. Le reste se paie soit en ligne
   la veille, soit par carte bancaire sur place le jour de la sortie.
2. Pour les **privatisations de bateau, l'acompte est de 50 %**.
3. **Pas de changement** pour les bons cadeaux, ni pour les avoirs.
4. Si la réservation est prise **entre 24 heures et l'heure de fin de
   réservation**, le client paie 30 % d'acompte en ligne, puis le reste par
   carte à la boutique, faute de pouvoir payer le complément « la veille ».
5. Sur le remboursement, le client raisonne sur un exemple : pour une sortie
   à 100 €, un client qui annule entre 48 heures et 24 heures avant le départ
   pouvait jusqu'à présent percevoir 50 % du prix total payé, soit 50 € pour
   le gérant et 50 € pour le client. Désormais il a déjà versé 30 %
   d'acompte, « perdus » pour lui, et « doit » encore 20 € en théorie, que le
   gérant ne récupérera pas.

Le mot **boutique** apparaît pour la première fois dans la mission. Aucun
document antérieur ne mentionne de point de vente physique.

---

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la
trace de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-06/Q03` désigne la question 3 de ce
compte rendu. La numérotation est définitive, on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | Pour une sortie à 100 € annulée entre 48h et 24h, le barème retient 50 € mais le client n'a versé que 30 €. Que fait-on des 20 € manquants ? | La retenue est **plafonnée à ce qui a été encaissé**. Le gérant garde les 30 €, ne réclame rien et ne rembourse rien. |
| Q02 | Jusqu'à quand le paiement en ligne du solde reste-t-il ouvert ? | **Réponse de l'équipe, non du client** : « on a déduit que la fenêtre était entre 24h et 12h ». Formulation ambiguë, précisée en `Q05`. |
| Q03 | Un client qui a versé son acompte occupe-t-il vraiment sa place, avant d'avoir payé le solde ? | Oui. **L'acompte confirme la réservation** : la place est décomptée, le client compte dans les 6 inscrits du seuil de maintien. |
| Q04 | L'encaissement du solde sur place passe-t-il par l'outil ? | Non. Le gérant encaisse sur son terminal habituel et **pointe le solde comme réglé** dans l'espace de gestion. |
| Q05 | Comment lit-on « entre 24h et 12h » ? | De **24 heures avant le départ jusqu'à l'heure de fermeture des réservations du créneau**, celle qui existe déjà. **Déduction d'équipe**, à faire valider par le client. |
| Q06 | Comment un bon cadeau ou un avoir s'articule-t-il avec l'acompte ? | **Un code paie tout ou rien.** Une réservation réglée par code n'a pas d'acompte : soit le code couvre le prix, soit la différence est due en totalité à la réservation. |
| Q07 | Après une annulation météo ou faute de 6 inscrits, que veut dire « remboursé intégralement » quand le client n'a versé qu'un acompte ? | **Tout ce que le client a versé**, donc l'acompte seul. Le gérant ne verse que ce qu'il a encaissé. |
| Q08 | Les taux de 30 % et 50 % sont-ils figés ou réglables depuis l'espace de gestion ? | **Figés dans l'outil.** |
| Q09 | L'acompte et le solde sont-ils deux transactions distinctes, ou une empreinte de carte avec débit différé ? | **Deux transactions différentes.** |
| Q10 | Comment arrondit-on l'acompte ? | **Au centime**, deux chiffres après la virgule. |
| Q11 | Une facture d'acompte puis une de solde, ou une seule facture ? | **Une seule facture, acquittée à la fin.** |
| Q12 | Le solde d'une privatisation suit-il la même fenêtre que celui d'une sortie ? | Oui, **même fenêtre H-24**. |
| Q13 | Le planning d'embarquement doit-il distinguer qui a soldé de qui doit encore payer ? | Oui, **pour éviter de faire monter à bord des gens qui n'ont pas soldé**. |
| Q14 | Le pointage du solde est-il réversible, et faut-il en garder une trace ? | Oui aux deux : **réversible, et tracé**. |
| Q15 | Un client qui ne se présente pas doit-il être distingué d'un client qui annule ? | Non, **on traite les deux pareil**. |
| Q16 | Le message de rappel doit-il annoncer le solde restant dû et son montant ? | Non. |
| Q17 | Faut-il relancer un client dont le solde n'est pas payé à l'approche de la fermeture ? | Non, **il se débrouille**. |
| Q18 | En cas de report, l'acompte suit-il la nouvelle réservation, et que se passe-t-il si le client reporte puis annule ? | **L'acompte suit la réservation.** En cas de report puis annulation, **on reste sur le taux de remboursement initial**. |

---

## 3. Ce que nous avons compris

Le paiement cesse d'être un événement unique pour devenir **une suite de deux
encaissements**, dont le second peut ne jamais passer par l'application. C'est
la conséquence la plus lourde de l'échange, et elle touche trois endroits que
rien ne reliait jusqu'ici : le parcours de réservation, le planning
d'embarquement du gérant, et le calcul de tout remboursement.

**Une réservation n'a plus un montant, elle a un état de paiement.** Jusqu'à
aujourd'hui, une réservation était payée ou ne l'était pas, et le mot
« confirmée » suffisait à tout dire. Il faut désormais distinguer ce qui est
dû, ce qui a été versé, et par quel canal le reste le sera.

**Le barème dégressif devient un plafond.** `Q01` ne le supprime pas, mais le
rend inatteignable dans sa tranche la plus sévère : entre 48 heures et
24 heures, la commission de 50 % excède l'acompte de 30 %, et le gérant garde
donc 30 % au lieu de 50 %. Le client l'a accepté explicitement. Ce que
l'échange n'a pas couvert, c'est le sens du plafond dans les **deux autres
tranches**, où l'acompte excède au contraire la commission. Voir le §6.

**Le gérant assume une perte sèche** de 20 % du prix sur chaque annulation
tardive, en échange d'un parcours de réservation moins coûteux pour le client.
C'est un arbitrage commercial, pas une contrainte technique, et il est de son
ressort.

**Le paiement sur place sort du périmètre logiciel** mais entre dans le
périmètre du planning : l'outil n'encaisse rien au quai, mais il doit dire au
gérant qui reste à encaisser. C'est exactement le rôle que `Q13` lui donne.

---

## 4. Parties prenantes identifiées

| Partie prenante | Rôle dans ce changement |
|---|---|
| Le client final | verse un acompte, puis un solde, par deux canaux possibles |
| Le gérant | encaisse le solde sur place, pointe le règlement, lit l'état du solde avant d'embarquer |
| Le prestataire de paiement | traite deux transactions indépendantes rattachées à une même réservation |
| L'équipe | porte la déduction de `Q05`, seule réponse de ce compte rendu qui ne vienne pas du client |

---

## 5. Règles métier découvertes

Rappel du statut de ce compte rendu : la colonne de formulation reproduit les
propos **tels que rapportés par l'équipe**, et non une transcription.

| # | Règle | Formulation rapportée du client | Sûre ? |
|---|---|---|---|
| 1 | Une réservation de sortie dauphins ou baleines exige un acompte de 30 % du montant total à la réservation | « le client paie un acompte de 30 % fixe à la réservation » | oui |
| 2 | Une privatisation de bateau exige un acompte de 50 % du forfait | « pour les privatisations de bateau, l'acompte est de 50 % » | oui |
| 3 | Le solde se paie en ligne entre 24 heures avant le départ et l'heure de fermeture des réservations du créneau, ou par carte sur place le jour de la sortie | « soit en ligne la veille, soit par CB sur place le jour de la sortie » (`Q05`) | **non** : la borne est une déduction d'équipe, à faire valider |
| 4 | Une réservation prise à moins de 24 heures du départ règle son solde sur place | « faute de pouvoir payer le complément la veille » | oui sur l'intention, **non** sur la portée, voir §6 |
| 5 | L'acompte confirme la réservation : la place est décomptée et le client compte dans le seuil de 6 inscrits | `Q03` | oui |
| 6 | La retenue en cas d'annulation par le client est plafonnée au montant encaissé ; aucun solde n'est réclamé | `Q01` | oui pour la tranche 48h-24h, **non** pour les autres tranches, voir §6 |
| 7 | Une réservation portant un bon cadeau ou un avoir n'a pas d'acompte : le code solde la réservation, ou la différence est due en totalité à la réservation | `Q06` | oui |
| 8 | « Remboursé intégralement » signifie la totalité de ce que le client a versé | `Q07` | oui |
| 9 | L'acompte et le solde sont deux transactions distinctes chez le prestataire | `Q09` | oui |
| 10 | Le montant de l'acompte est arrondi au centime | `Q10` | oui |
| 11 | Une seule facture est émise, acquittée une fois le solde réglé | `Q11` | oui |
| 12 | Le solde encaissé sur place est pointé par le gérant, sans transaction dans l'outil ; le pointage est réversible et tracé | `Q04`, `Q14` | oui |
| 13 | Le planning d'embarquement distingue les réservations soldées de celles qui ne le sont pas | `Q13` | oui |
| 14 | Un client absent au départ est traité comme un client qui annule | `Q15` | oui sur le principe, **non** sur le taux applicable, voir §6 |
| 15 | Aucun message n'annonce le solde dû, et aucune relance n'est envoyée | `Q16`, `Q17` | oui |
| 16 | En cas de report, l'acompte suit la nouvelle réservation ; une annulation après report applique le taux initial | `Q18` | oui |
| 17 | Les taux de 30 % et 50 % ne sont pas réglables par le gérant | `Q08` | oui |

---

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « soit en ligne la veille » (point 1) | (a) la veille au sens calendaire, quelle que soit l'heure de départ (b) une fenêtre relative au départ, bornée par l'heure de fermeture du créneau | **partiellement, par `Q05`** : lecture (b) retenue, mais **c'est une déduction d'équipe et non une réponse du client**. À valider explicitement |
| 2 | « faute de pouvoir payer le complément la veille » (point 4) | (a) une réservation prise à moins de 24h perd le droit au paiement en ligne, le solde est nécessairement réglé sur place (b) le point 4 ne décrit que le cas courant, et le client garde le droit de solder en ligne jusqu'à la fermeture | **non** : les deux lectures sont compatibles avec ce qui a été dit, et elles ne produisent pas le même parcours. Voir la note ci-dessous |
| 3 | « la retenue est plafonnée à l'acompte » (`Q01`) | (a) le plafond joue dans les trois tranches du barème, ce qui donne un remboursement partiel au-delà de 48h (b) l'acompte est perdu quelle que soit la tranche | **non** : `Q01` ne portait que sur la tranche 48h-24h, la seule où le plafond mord. Arithmétique au §7 |
| 4 | « on traite les deux pareil » (`Q15`) | (a) un client absent relève de la tranche du barème correspondant à l'heure du départ (b) un client absent perd son acompte, sans référence au barème | **non** : le barème de `R-05` **n'a aucune tranche en deçà de 24 heures**, donc aucune des deux lectures ne s'appuie sur une règle existante |
| 5 | « à la boutique » (point 4) | (a) simple synonyme du lieu d'embarquement (b) point de vente physique distinct, avec ses propres horaires | **non** : le mot est nouveau dans la mission, aucun document antérieur ne mentionne de boutique |

**Sur l'ambiguïté 2.** Le client suppose qu'une réservation tardive prive du
paiement en ligne. Or la fenêtre déduite en `Q05` reste ouverte jusqu'à
l'heure de fermeture : un client qui réserve un départ de 7h à 8h la veille a
encore quatre heures pour solder en ligne. Sous la lecture (b), le point 4 du
client ne décrit donc presque aucun cas réel, ce qui est un signal qu'il
pensait à la lecture (a).

---

## 7. Contraintes évoquées

**Arithmétique du plafond, par tranche du barème de `R-05`**, sur une sortie
à 100 € avec 30 € d'acompte versé :

| Délai avant le départ | Commission prévue | Encaissé | Retenue plafonnée | Rendu au client |
|---|---|---|---|---|
| au-delà de 7 jours | 0 € | 30 € | 0 € | **30 €** |
| entre 7 jours et 48 heures | 25 € | 30 € | 25 € | **5 €** |
| entre 48 heures et 24 heures | 50 € | 30 € | 30 € | **0 €** |
| moins de 24 heures | *aucune tranche* | 30 € | ? | ? |

Le client n'a raisonné que sur la troisième ligne. Les deux premières
produisent un remboursement partiel qu'il n'a pas évoqué, et la quatrième
n'existe dans aucune règle écrite.

**Sur le prestataire de paiement.** Deux transactions distinctes rattachées à
une même réservation supposent que le prestataire les relie, ou que
l'application tienne elle-même ce lien. `ADR-001` n'a pas instruit ce point.

**Sur la traçabilité du pointage.** `Q14` exige qu'un pointage réversible
laisse une trace. C'est une écriture de plus, comparable à celle des envois de
messages, et qui n'existe dans aucune table.

**Sur la facture unique.** `Q11` demande une facture acquittée à la fin, alors
que `REQ-018` délègue la facturation au prestataire de paiement, qui n'aura
connaissance que de la transaction en ligne. Un solde encaissé sur place lui
échappe entièrement.

---

## 8. Questions à poser au prochain entretien

| # | Question | Pourquoi elle bloque |
|---|---|---|
| 1 | Une réservation prise à moins de 24 heures du départ interdit-elle le paiement en ligne du solde, ou celui-ci reste-t-il ouvert jusqu'à la fermeture du créneau ? | ambiguïté 2. Les deux lectures donnent deux parcours différents, et la seconde vide le point 4 de son contenu |
| 2 | La fenêtre « de 24 heures avant le départ jusqu'à l'heure de fermeture du créneau » est-elle bien ce que vous entendiez par « la veille » ? | `Q05` est la seule réponse de ce compte rendu produite par l'équipe et non par le client |
| 3 | Un client qui annule à plus de 48 heures récupère-t-il la part de son acompte qui excède la commission, soit 5 € sur une sortie à 100 € annulée à 5 jours, et 30 € au-delà de 7 jours ? | ambiguïté 3. Sans réponse, le plafond de `Q01` n'est défini que dans une tranche sur trois |
| 4 | Quel taux de retenue s'applique à un client absent au départ, ou qui annule à moins de 24 heures ? | ambiguïté 4. Le barème n'a jamais eu de tranche en deçà de 24 heures, et la question ne se posait pas tant que tout était payé d'avance |
| 5 | La « boutique » est-elle le lieu d'embarquement, ou un point de vente distinct avec ses propres horaires ? | ambiguïté 5. Si les horaires diffèrent, un client peut se trouver dans l'impossibilité matérielle de solder |
| 6 | Un client ayant soldé en ligne puis annulant relève-t-il du barème sur le prix total, comme aujourd'hui ? | le plafond de `Q01` ne joue plus, mais rien ne le dit explicitement |
| 7 | Le barème de remboursement s'applique-t-il aussi aux privatisations, dont l'acompte est de 50 % ? | `REQ-019` ne distingue pas les formules, et personne n'a vérifié que le client le voulait |
| 8 | La facture unique doit-elle être émise par l'outil plutôt que par le prestataire, puisqu'un solde encaissé sur place lui est invisible ? | contredit `REQ-018`, qui délègue la facturation au prestataire |

---

## 9. Ce que nous n'avons pas abordé

- **Le sort comptable du solde encaissé sur place.** L'outil ne le traite pas,
  mais le gérant devra bien le rapprocher de sa caisse.
- **La TVA et les mentions de la facture unique**, déjà en attente depuis la
  question 3 du §11 du cahier des charges.
- **Le cas d'un groupe qui solderait partiellement**, plusieurs personnes
  payant chacune leur part sur place.
- **Le délai de conservation de la trace des pointages**, alors que
  `SPEC-NFR-04` fixe trois mois pour les données personnelles.
- **Ce que devient une réservation dont le solde n'est jamais réglé et dont le
  client s'est présenté**, cas que `Q15` ne couvre pas puisqu'il ne parle que
  de l'absent.
