# Cahier des charges — Ti Baleine

**Équipe :** `Le Trio`
- [Chloe Baisse](mailto:baissechloe@gmail.com)
- [Arnaud Maxime](mailto:arnaudmaxime.bidel@gmail.com)
- [Anthony Dégeilh](mailto:anthony.degeilh@gmail.com)

**Version :** v2 — 2026-08-11
**Sources :** [`compte-rendu-entretien-01.md`](./compte-rendu-entretien-01.md) (CR-01), [`compte-rendu-entretien-02.md`](./compte-rendu-entretien-02.md) (CR-02), échange oral du 2026-08-11.

Ce document formalise le **problème compris**, pas la solution. Aucun nom de
technologie, aucun nom de framework, aucune structure de base de données ici
— ces choix sont documentés séparément par l'équipe, une fois le problème
validé.

Aucune exigence n'a été ajoutée au-delà de ce que le client a confirmé. Les
sujets évoqués mais non tranchés (réponse floue, question restée sans
réponse, ambiguïté non levée) ne sont volontairement **pas** transformés en
exigence : ils sont listés à part, au [§11](#11-questions-restées-ouvertes).

---

## 1. Contexte

Ti Baleine propose des sorties en mer sur plusieurs créneaux par jour, avec
deux bateaux à capacité limitée (un naturaliste, obligatoire à bord pour les
sorties baleines, n'est disponible qu'en un seul exemplaire). Les
réservations sont aujourd'hui gérées à la main par le gérant, par téléphone
et par WhatsApp : il suit lui-même les annulations de dernière minute, les
changements de taille de groupe et les reports liés à la météo, sans outil
dédié. Il n'existe à ce jour ni site internet, ni logiciel de comptabilité ou
de caisse, ni prestataire de paiement en ligne — seul un terminal de paiement
classique est utilisé sur place. *([CR-01, §1](./compte-rendu-entretien-01.md#1-ce-que-le-client-a-dit) ; [CR-01, §7-3](./compte-rendu-entretien-01.md#7-contraintes-évoquées))*

## 2. Problème

Le gérant de Ti Baleine ne dispose d'aucun outil pour recevoir et suivre ses
réservations : tout passe par le téléphone et WhatsApp, ce qui l'oblige à
tenir manuellement la jauge de chaque créneau, les annulations et les
reports. Les clients n'ont aucune visibilité sur les places disponibles et
ne peuvent pas payer en ligne. Le gérant veut un outil qui prenne en charge
la réservation et le paiement en ligne, sans reproduire la charge de suivi
manuel qu'il connaît aujourd'hui pour les annulations météo et les
changements de dernière minute.

## 3. Objectifs

Ce que le client veut obtenir, pas ce que l'application doit faire.

| # | Objectif | Comment on saura que c'est atteint |
|---|---|---|
| 1 | Ne plus prendre les réservations par téléphone : que le client réserve et paie seul, en ligne | Les nouvelles réservations arrivent exclusivement via le site, avec paiement carte bancaire validé au moment de la réservation |
| 2 | Ajuster les tarifs et suivre le planning sans ressaisie manuelle | Les tarifs sont modifiables depuis l'espace de gestion ; le planning des réservations est exportable dans un format imprimable |
| 3 | Ne plus gérer les annulations météo à la main, créneau par créneau | Le gérant peut visualiser les clients inscrits sur un créneau avant de décider une annulation météo, et enregistrer le choix (report, avoir, remboursement) de chacun |

## 4. Parties prenantes

| Partie prenante | Rôle | Ce qu'elle attend | Utilise l'application ? |
|---|---|---|---|
| Gérant / armateur | Seul utilisateur prévu de l'outil à ce stade ; modifie les tarifs, décide seul des annulations météo, contacte les clients concernés, valide les reports, avoirs ou remboursements | Ne plus suivre les réservations à la main | oui |
| Client / passager | Réserve et paie en ligne, choisit sa sortie (dauphins/baleines) parmi les places disponibles affichées, peut être recontacté par téléphone en cas d'annulation météo, peut reporter sa réservation | Réserver et payer simplement en ligne, être informé en cas de changement | oui |
| Naturaliste | Présence obligatoire à bord pour une sortie baleines ; un seul naturaliste est disponible, ce qui limite à un bateau à la fois pour ce type de sortie | N'utilise pas l'outil, mais sa disponibilité contraint le nombre de sorties baleines proposées en simultané | non |
| Prestataire de paiement en ligne (tiers) | Traite le paiement par carte bancaire et les données sensibles associées | Aucune attente propre ; permet à l'outil de ne stocker aucune donnée de paiement sensible | non (usage en coulisse) |

*([CR-01, §4](./compte-rendu-entretien-01.md#4-parties-prenantes-identifiées) ; [CR-02, §4](./compte-rendu-entretien-02.md#4-parties-prenantes-identifiées))*

## 5. Personas

### Le gérant — administrateur unique de l'outil

- Contexte d'usage : consulte l'espace de gestion depuis un ordinateur pour ajuster les tarifs et suivre le planning ; décide seul d'une annulation météo, souvent dans l'urgence,
  puis appelle chaque client concerné.
- Objectif : garder une vue d'ensemble des réservations et des tarifs sans
  ressaisie manuelle.
- Ce qui le bloque aujourd'hui : les réservations arrivent par téléphone et
  WhatsApp, sans registre centralisé ni suivi automatisé des annulations et
  des reports.

### Le client — passager réservant une sortie

- Contexte d'usage : réserve depuis son téléphone, sa tablette ou son
  ordinateur, souvent dans les jours précédant la sortie, parfois la veille.
- Objectif : réserver et payer une sortie en quelques minutes, en sachant
  combien de places restent disponibles.
- Ce qui le bloque aujourd'hui : doit appeler ou écrire sur WhatsApp pour
  réserver, sans visibilité sur les places restantes ni possibilité de payer
  en ligne.

## 6. Périmètre

### Dans le périmètre

- Réserver et payer une sortie en ligne (dauphins/baleines, forfait standard
  ou privatisation).
- Modifier les tarifs et suivre le planning des réservations, depuis un
  espace de gestion réservé au gérant.
- Annuler un créneau pour raison météo et en informer les clients concernés.

### Hors périmètre

Aussi important que la liste précédente. Chaque ligne cite la raison ou la
réponse du client.

| Élément écarté | Motif |
|---|---|
| Modification de groupe en autonomie par le client | Laissée à la discrétion du gérant, traitée au cas par cas par téléphone — REQ-013 §14 point 1 en amont, aucune règle formalisée (délai, frais) fournie par le client, y compris après relance ([CR-01, §6-2](./compte-rendu-entretien-01.md#6-ambiguïtés-détectées) ; reconfirmé le 2026-08-11) |
| Vente en ligne des suppléments personnalisables (ex. champagne à bord) | Le client les réserve uniquement par téléphone ([CR-01/Q04](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-01.md#5-règles-métier-découvertes)) |
| Annulation, report en autonomie par le client sur le site | Organisés par téléphone avec le gérant ([CR-01/Q13](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q17](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q20](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues)) |
| Modification du contenu client, de la flotte ou des créneaux depuis l'espace de gestion | Non demandé par le client pour cette version ([CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes)) |
| Répartition automatique des passagers entre les deux bateaux | Le gérant la décide manuellement, sans règle automatique demandée (précisé le 2026-08-11, répond à l'ambiguïté [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§6-1](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées)) |
| Comptes multi-utilisateurs (salariés, capitaines) | Le gérant est l'unique utilisateur prévu à ce stade ([CR-02, §7-3](./compte-rendu-entretien-02.md#7-contraintes-évoquées) ; arrivée de salariés non anticipée, confirmé le 2026-08-11) |

## 7. Contraintes

| # | Contrainte | Nature | Source |
|---|---|---|---|
| 1 | Aucun prestataire de paiement en ligne n'existe actuellement ; seul un terminal de paiement classique est utilisé sur place | technique | [CR-01, §7-3](./compte-rendu-entretien-01.md#7-contraintes-évoquées) |
| 2 | La saison des sorties baleines est limitée du 15 juin au 31 octobre | métier | [CR-01, §7-4](./compte-rendu-entretien-01.md#7-contraintes-évoquées) |
| 3 | Le gérant est, à ce jour, l'unique utilisateur prévu de l'outil (aucun salarié, aucun accès distinct pour un capitaine) | humaine | [CR-02, §7-3](./compte-rendu-entretien-02.md#7-contraintes-évoquées) ; confirmé hors scope le 2026-08-11 |
| 4 | Il n'existe aujourd'hui aucun site internet (seulement une page Facebook, confirmée à jour par le client mais qui ne sera plus utilisée pour la gestion des réservations) ni logiciel de comptabilité ou de caisse ; la comptabilité est tenue manuellement | technique | [CR-02, §7-4](./compte-rendu-entretien-02.md#7-contraintes-évoquées), [Q13](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q14](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; actualité confirmée par le client le 2026-08-11 |
| 5 | Le client dispose d'un logo mais pas d'une charte graphique définie | technique | [CR-02/Q15](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| 6 | Budget d'hébergement et budget total non validés avec le client à ce jour | budget | Question préparée par l'équipe, non encore posée au client — voir [§11](#11-questions-restées-ouvertes) |
| 7 | Réglementation RGPD applicable aux données clients collectées (nom, coordonnées, historique de réservation) | réglementaire | Note interne de l'équipe, durée de conservation non validée avec le client — voir [§11](#11-questions-restées-ouvertes) |

## 8. Règles métier

Les règles telles que le client les a énoncées, avant toute mise en forme de
spécification.

| # | Règle | Source |
|---|---|---|
| R-01 | Une réservation est possible à partir de 2 personnes | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| R-02 | Une sortie n'est maintenue qu'à partir de 6 personnes inscrites ; ce seuil est contrôlé 24 heures avant le départ | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-1](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; [CR-02/Q16](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| R-03 | Un seul bateau peut être engagé sur une sortie baleines à la fois, faute d'un second naturaliste | [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| R-04 | Le tarif enfant s'applique de 4 à 11 ans, le tarif adulte à partir de 12 ans ; accès interdit avant 4 ans | [CR-02/Q02](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| R-05 | Barème de remboursement dégressif selon le délai avant départ : 100 % au-delà de 7 jours, 25 % de commission entre 7 jours et 48 heures, 50 % de commission entre 48 heures et 24 heures | [CR-01/Q13](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| R-06 | Les réservations en ligne ferment 2 heures avant le départ pour le créneau de 14h, et la veille à midi pour les créneaux de 7h et 10h | [CR-01/Q09](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; précisé le 2026-08-11 |
| R-07 | Le paiement de la totalité du montant est exigé en carte bancaire au moment de la réservation ; aucun acompte, aucun autre moyen de paiement en ligne | [CR-01/Q10](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [Q12](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| R-08 | La répartition des passagers entre les deux bateaux, quand plusieurs sont disponibles, est décidée manuellement par le gérant | Précisé le 2026-08-11 |
| R-09 | Toute nouvelle réservation client passe exclusivement par le site ; le gérant n'en prend plus par téléphone | Précisé le 2026-08-11 |

## 9. Exigences fonctionnelles

Une ligne par exigence. L'identifiant `REQ-xxx` est définitif : il est cité
par les spécifications et ne se renumérote jamais.

**Rappel :** trois cas d'usage sont retenus comme prioritaires par le client
(réserver et payer une sortie en ligne ; modifier les tarifs et suivre le
planning ; annuler un créneau météo et informer les clients concernés) — cf.
[§3](#3-objectifs). La quasi-totalité des exigences « Must » ci-dessous s'y
rattache.

### 9.1 Réservation en ligne

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-001 | Le client peut réserver une sortie en ligne à partir de 2 personnes. | Must | Client | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| REQ-036 | Toute nouvelle réservation client se fait exclusivement via le site de réservation en ligne ; le gérant ne prend plus de nouvelle réservation par téléphone ou WhatsApp (ces canaux restent utilisés pour l'annulation, le report et le suivi, cf. REQ-019, REQ-020). | Must | Client | *Précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [CR-02/Q12](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§6-2](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |
| REQ-002 | Une sortie n'est maintenue qu'à partir de 6 personnes inscrites sur le créneau ; ce nombre est vérifié 24 heures avant le départ. | Must | Client | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-1](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; [CR-02/Q16](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-003 | Si le nombre de 6 personnes n'est pas atteint au moment du contrôle, la sortie est annulée et les clients déjà inscrits sont remboursés. | Must | Client | [CR-02/Q16](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-004 | Au moment de réserver, le client voit le nombre de places encore disponibles sur chaque type de sortie. | Must | Client | [CR-01/Q08](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| REQ-005 | Les réservations en ligne ferment 2 heures avant le départ pour le créneau de 14h (soit à midi le jour même), et la veille à midi pour les créneaux de 7h et de 10h. | Must | Client | [CR-01/Q09](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; *précisé par le client le 2026-08-11* — répond à l'ambiguïté relevée en [CR-02, §6-3](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |
| REQ-006 | Le client peut réserver une privatisation, qui bloque un bateau entier sur un créneau, aussi bien le matin (brunch) que l'après-midi (coucher de soleil). | Must | Client | [CR-01/Q03](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q01](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-007 | Un seul bateau à la fois peut être engagé sur une sortie baleines, faute d'un second naturaliste disponible ; les deux bateaux peuvent en revanche être utilisés en même temps pour des sorties dauphins. | Must | Client | [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-037 | Lorsque plusieurs bateaux sont disponibles pour un même type de sortie, la répartition des passagers entre les deux bateaux est décidée manuellement par le gérant ; aucune règle de répartition automatique n'est demandée pour cette version. | Won't | Gérant | *Précisé par le client le 2026-08-11* — répond à l'ambiguïté relevée en [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§6-1](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |
| REQ-008 | L'accès à une sortie est interdit aux enfants de moins de 4 ans. | Must | Client | [CR-02/Q02](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-009 | Lors de la réservation, le client fournit : nom, prénom, e-mail, numéro de téléphone, nombre d'adultes et d'enfants, date et heure du créneau choisi, et type de sortie correspondant à la saison. Aucune information supplémentaire n'est demandée. | Must | Client | [CR-02/Q18](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) (le client a exclu toute information particulière) ; liste précise des champs *déduite* par l'équipe — ce sont les informations minimales pour identifier le client, le contacter et composer le créneau |
| REQ-035 | Le site de réservation s'adapte à la taille de l'écran utilisé par le client, qu'il réserve depuis un ordinateur, une tablette ou un téléphone mobile. | Must | Client | *Déduit* — le client vise une réservation « directement en ligne » ([CR-01, §1](./compte-rendu-entretien-01.md#1-ce-que-le-client-a-dit)) sans avoir précisé les appareils visés ; l'équipe retient un accès utilisable sur les principaux types d'appareils, non discuté explicitement avec le client |
| REQ-010 | Trois créneaux de départ sont proposés chaque jour, à 7h, 10h et 14h, pour des sorties d'une durée d'environ 3 heures. | Must | Client | [CR-01/Q05](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| REQ-011 | Du 15 juin au 31 octobre, les créneaux proposent des sorties dauphins ainsi que des sorties baleines ; en dehors de cette période, les mêmes créneaux proposent uniquement des sorties dauphins. | Must | Client | [CR-01/Q05](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§7-4](./compte-rendu-entretien-01.md#7-contraintes-évoquées) |
| REQ-012 | Deux formules sont proposées à la réservation : un forfait standard et une privatisation (sans tarif préférentiel). | Must | Client | [CR-01/Q04](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-2](./compte-rendu-entretien-01.md#5-règles-métier-découvertes), [§5-3](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| REQ-013 | Les suppléments personnalisables (par exemple le champagne à bord) ne sont pas proposés à la réservation en ligne ; ils restent vendus uniquement par téléphone. | Won't | Client | [CR-01/Q04](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |

### 9.2 Tarification

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-014 | Les tarifs appliqués sont : sortie baleines **65 € par adulte** et **40 € par enfant** ; sortie dauphins **50 € par adulte** et **30 € par enfant** ; privatisation **600 € pour le Ti Kap** et **1100 € pour Le Grand Bleu**. | Must | Client | [CR-01/Q07](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| REQ-015 | Le tarif enfant s'applique de 4 à 11 ans, le tarif adulte à partir de 12 ans ; il n'existe pas de tarif pour les moins de 4 ans, l'accès leur étant interdit. | Must | Client | [CR-02/Q02](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-016 | Le gérant peut modifier les tarifs lui-même, en général une fois par an. | Must | Gérant | [CR-01/Q07](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |

### 9.3 Paiement

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-017 | Le paiement de la totalité du montant est exigé en ligne, par carte bancaire, au moment de la réservation. Aucun acompte, et aucun autre moyen de paiement (espèces, virement, chèque) n'est accepté en ligne. | Must | Client | [CR-01/Q10](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [Q12](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| REQ-018 | Le paiement en ligne est délégué à un prestataire de paiement tiers : aucune donnée de paiement sensible n'est stockée ni traitée directement par l'outil. Ce prestataire a été retenu par l'équipe après comparaison de plusieurs solutions, sur le critère du coût le plus bas ; le client n'imposait aucune préférence par principe. | Must *(préalable au reste)* | Client | Critère de choix (coût le plus bas, absence de préférence du client) : [CR-01/Q11](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q19](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-02.md#5-règles-métier-découvertes). Choix précis du prestataire : *déduit* — décision technique de l'équipe, documentée séparément (hors cahier des charges), non redemandée au client |

### 9.4 Annulation et report à l'initiative du client

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-019 | En cas d'annulation par le client, le montant remboursé suit un barème dégressif : 100 % au-delà de 7 jours avant le départ ; 25 % de commission retenue entre 7 jours et 48 heures avant le départ ; 50 % de commission retenue entre 48 heures et 24 heures avant le départ. La demande d'annulation et son remboursement sont organisés par téléphone avec le gérant, et non en autonomie sur le site ; un éventuel canal e-mail ne sert qu'à notifier une annulation déjà décidée par téléphone, jamais à en faire la demande. | Must | Client | [CR-01/Q13](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; [CR-02/Q20](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-9](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) ; *précisé par le client le 2026-08-11* — répond à l'ambiguïté relevée en [CR-02/Q04](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§6-4](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |
| REQ-020 | Le client peut reporter sa réservation à une autre date, y compris à moins de 24 heures du départ, sous réserve de disponibilité. Ce report est organisé par téléphone avec le gérant, et non en autonomie sur le site. | Must | Client | [CR-02/Q17](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) ; [Q20](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-9](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |

### 9.5 Annulation météo à l'initiative du gérant

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-021 | La décision d'annuler un créneau pour raison météo appartient uniquement au gérant ; elle n'est pas déclenchée automatiquement. | Must | Gérant | [CR-02/Q04](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q05](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-022 | Avant de valider une annulation météo, le gérant doit pouvoir visualiser la situation du créneau concerné (clients inscrits) ; l'annulation n'a lieu qu'après cette validation. | Must | Gérant | [CR-02/Q05](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-023 | Une fois l'annulation météo décidée, le gérant contacte par téléphone chaque client concerné pour lui proposer un report, un avoir ou un remboursement, et enregistre son choix. | Must | Gérant | [CR-02/Q04](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-024 | Si un nouveau créneau est proposé au client à la suite d'une annulation météo, cette proposition tient compte des disponibilités et de la météo, et est communiquée par téléphone. Si la première proposition ne convient pas au client, gérant et client s'accordent directement par téléphone sur un remplacement adapté ; aucune procédure automatisée n'est prévue en cas de désaccord. | Must | Gérant | [CR-02/Q06](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* — répond au point resté sans réponse en [CR-02, §9](./compte-rendu-entretien-02.md#9-ce-que-nous-navons-pas-abordé) |
| REQ-026 | Les clients sont prévenus par téléphone en cas d'annulation ou de modification. | Must | Gérant | [CR-02/Q09](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |

### 9.6 Espace de gestion (usage réservé au gérant)

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-028 | L'espace de gestion permet de modifier les tarifs. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-029 | L'espace de gestion permet d'obtenir le planning des réservations dans un format imprimable. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-030 | L'espace de gestion ne permet pas de modifier le contenu présenté aux clients (messages de la page d'accueil ou autre contenu), ni la composition de la flotte, ni les créneaux. | Won't | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-031 | L'accès à l'espace de gestion est réservé à un compte unique, celui du gérant. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-033 | La flotte comprend deux bateaux à fond de verre : le Ti Kap (12 places) et Le Grand Bleu (24 places). Cette composition n'est pas modifiable depuis l'espace de gestion pour cette version. | Must | Gérant | [CR-01/Q01](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-034 | La connexion à l'espace de gestion se fait avec une adresse e-mail et un mot de passe. Le mot de passe doit compter au moins 8 caractères, dont au moins une majuscule, une minuscule, un chiffre et un caractère spécial. | Must | Gérant | *Déduit* — aucune règle de sécurité pour l'accès au compte unique de gestion (REQ-031) n'a été discutée avec le client ; règle minimale retenue par l'équipe |

### 9.7 Communication - compléments

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-025 | Un message de rappel est envoyé au client 24 heures avant sa sortie, avec les conditions météo prévues et la liste des affaires à prévoir. | Should | Client | [CR-02/Q08](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-027 | Le nouvel outil de réservation est le canal de communication principal ; WhatsApp reste utilisé comme canal de secours, en complément et non à sa place. | Should | Gérant, Client | [CR-02/Q07](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* — répond à l'ambiguïté relevée en [§6-5](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |

### 9.8 Exploitation - élément de contexte

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-032 | À ce stade, l'outil est utilisé au quotidien par le gérant uniquement. | Must *(dimensionne les autres exigences)* | Gérant | [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§7-3](./compte-rendu-entretien-02.md#7-contraintes-évoquées) |

## 10. Exigences non fonctionnelles

Trop souvent oubliées, et c'est là que les projets se cassent. Chacune doit
être vérifiable, et sourcée comme les autres.

| ID | Exigence | Comment on la vérifie | Source |
|---|---|---|---|
| REQ-100 | Volumétrie et pics de charge : usage attendu très faible (un seul gérant, deux bateaux, trois créneaux par jour), avec un pic de réservations en saison haute (15 juin - 31 octobre). | Pas de dégradation perceptible avec quelques dizaines de réservations simultanées en période de pointe. | *Déduit* — aucune volumétrie chiffrée fournie par le client ; hypothèse d'équipe basée sur la taille de la flotte (REQ-033) |
| REQ-101 | Support et conditions réseau : le site reste utilisable sur les principaux navigateurs desktop et mobiles, y compris en connexion mobile standard. | Test de réservation complet sur ordinateur, tablette et mobile, en 4G. | *Déduit*, lié à REQ-035 |
| REQ-102 | Langues : site en français uniquement pour cette version, à défaut de connaître la proportion de clientèle non francophone. | Absence de contenu traduit dans la première version livrée. | *Déduit* — question posée à l'équipe mais non encore soumise au client, voir [§11](#11-questions-restées-ouvertes) |
| REQ-103 | Coût d'hébergement : solution à faible coût recherchée par l'équipe, le budget du client n'étant pas encore validé. | Coût d'hébergement mensuel documenté et validé par le client avant mise en production. | *Déduit* — voir [§11](#11-questions-restées-ouvertes) |
| REQ-104 | Sécurité et contrôle d'accès : l'espace de gestion est protégé par un compte unique (REQ-031) avec une règle de mot de passe minimale (8 caractères, majuscule, minuscule, chiffre, caractère spécial). | Tentative de création d'un mot de passe non conforme rejetée. | *Déduit* — aucune règle de sécurité demandée par le client (cf. REQ-034) |
| REQ-105 | Données personnelles et durée de conservation : seules les informations minimales du REQ-009 sont collectées ; aucune donnée de paiement sensible n'est stockée (REQ-018) ; une durée de conservation minimale de 3 mois avant suppression est envisagée. | Suppression ou anonymisation effective des données passé le délai retenu. | Note interne de l'équipe (RGPD), point de départ du délai non validé — voir [§11](#11-questions-restées-ouvertes) |
| REQ-106 | Déploiement : fréquence de mise à jour et environnement de recette non discutés avec le client. | À définir avec le client. | Non abordé — voir [§11](#11-questions-restées-ouvertes) |
| REQ-107 | Maintenance après livraison : responsable et durée de la maintenance après livraison non discutés avec le client. | À définir avec le client. | Non abordé — voir [§11](#11-questions-restées-ouvertes) |

## 11. Questions restées ouvertes

Une question sans réponse n'interdit pas d'avancer, à condition que
l'hypothèse soit écrite. Une hypothèse non écrite est une erreur en attente.

| # | Question | Posée le | Réponse | Hypothèse retenue en attendant |
|---|---|---|---|---|
| 1 | Quelles sont les règles précises (délai, frais, mode de validation) pour une modification de la taille d'un groupe après réservation ? | CR-01, reconfirmée le 2026-08-11 | Laissée « à la discrétion de l'armateur », aucune règle formalisée, y compris après relance | Traité au cas par cas par téléphone avec le gérant, sans automatisation dans l'outil |
| 2 | Quel est le contenu exact du message de rappel envoyé à J-1 ? | CR-02 | Le client souhaite le définir lors du prochain rendez-vous | REQ-025 reste au statut « Should » ; le contenu (météo, affaires à prévoir) est esquissé mais le texte précis n'est pas figé |
| 3 | Existe-t-il des jours de fermeture de l'entreprise ? Si oui, sont-ils modifiables depuis l'espace de gestion, et par qui ? | Non posée | en attente | Aucun jour de fermeture pour cette version ; les trois créneaux quotidiens sont proposés tous les jours |
| 4 | Quel est le budget d'hébergement et le budget total du projet ? | Non posée | en attente | L'équipe recherche une solution d'hébergement à faible coût, sans engager le client au-delà de ce qui sera validé |
| 5 | Quelle est la diversité linguistique de la clientèle ? Une version multilingue du site est-elle souhaitée ? | Non posée | en attente | Site en français uniquement pour cette version (REQ-102) |
| 6 | Sous quel format la facture doit-elle être transmise au client (e-mail, PDF téléchargeable), avec quelles mentions légales ? | Non posée | en attente | Facture générée automatiquement par le prestataire de paiement (REQ-018) et transmise par e-mail au client |
| 7 | Quelle est la durée de conservation des données clients attendue par le client, et son point de départ (depuis la sortie ? depuis le dernier contact ?) ? | Non posée | en attente | Conservation minimale de 3 mois après la sortie (REQ-105), à confirmer |
| 8 | Les modalités précises de connexion à l'espace de gestion (identifiant, règle de mot de passe) conviennent-elles au gérant ? | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) | Compte unique confirmé ; règle de mot de passe non discutée | Connexion par e-mail et mot de passe (REQ-034/REQ-104), à reconfirmer avec le client |

## 12. Validation client

| Version | Date | Présentée au client | Retour |
|---|---|---|---|
| v1 | — | non | Rédigée à partir de CR-01 et CR-02, sans relecture client formelle |
| v2 | 2026-08-11 | non | Intègre les réponses orales du client du 2026-08-11 (compte-rendu à formaliser) et reformate le document selon le template d'équipe |

## 13. Glossaire

Définitions en langage courant, telles qu'utilisées par le client.

- **Créneau** : un horaire de départ fixe (7h, 10h ou 14h) proposé pour une sortie.
- **Sortie** : la prestation en mer proposée à un horaire donné, sur un bateau donné (baleines ou dauphins selon la saison).
- **Forfait** : la formule de réservation standard.
- **Privatisation** : réservation qui bloque un bateau entier sur un créneau, sans réduction de tarif.
- **Report** : déplacement d'une réservation existante vers une autre date, à la différence d'une annulation qui met fin à la réservation.
- **Avoir** : montant crédité au client, utilisable pour une réservation future, proposé comme alternative au remboursement.
- **Espace de gestion** : la partie de l'outil réservée au gérant, pour modifier les tarifs et obtenir le planning des réservations.

## 14. Traçabilité

Chaque exigence de ce document cite sa source sous la forme `CR-0n/Qnn` (une
question posée en entretien) ou `CR-0n/§5-n` (une règle métier reformulée
dans le compte rendu correspondant). Cinq exigences sont marquées « déduit »,
faute d'avoir été discutées explicitement avec le client, chacune avec sa
justification dans la colonne Source :

- `REQ-009` — la liste précise des champs du formulaire de réservation (le
  client a seulement exclu toute information particulière, CR-02/Q18) ;
- `REQ-018` — le choix précis du prestataire de paiement (le client a
  seulement demandé de retenir la solution la moins chère, sans imposer
  laquelle ; le choix lui-même est une décision technique documentée hors de
  ce cahier des charges) ;
- `REQ-034` / `REQ-104` — la règle de mot de passe de l'espace de gestion ;
- `REQ-035` — l'adaptation du site aux différents écrans ;
- `REQ-100` à `REQ-103`, `REQ-105` — les exigences non fonctionnelles pour
  lesquelles aucune donnée chiffrée ou aucune décision n'a été confirmée par
  le client (voir [§11](#11-questions-restées-ouvertes)).

Toutes les autres exigences ont été dites explicitement par le client.
`./tools/traceability.sh`, s'il est ajouté au projet, devra vérifier que
chaque `REQ` a une source et que l'échange cité existe réellement dans les
comptes rendus.
