# Cahier des charges — Ti Baleine

**Équipe :** `Le Trio`
- [Chloe Baisse](mailto:baissechloe@gmail.com)
- [Arnaud Maxime](mailto:arnaudmaxime.bidel@gmail.com)
- [Anthony Dégeilh](mailto:anthony.degeilh@gmail.com)

**Version :** v4 — 2026-08-13
**Sources :** [`compte-rendu-entretien-01.md`](./compte-rendu-entretien-01.md) (CR-01), [`compte-rendu-entretien-02.md`](./compte-rendu-entretien-02.md) (CR-02), [`compte-rendu-entretien-03.md`](./compte-rendu-entretien-03.md) (CR-03), [`compte-rendu-entretien-04.md`](./compte-rendu-entretien-04.md) (CR-04), échange oral du 2026-08-11, analyses d'impact [`impact-CR-001.md`](./impact-CR-001.md) et [`impact-CR-002.md`](./impact-CR-002.md).

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
| Bénéficiaire d'un bon cadeau | Reçoit un bon cadeau (ex. offert à Noël) et réserve plus tard, seul ou en groupe, en renseignant le code du bon ; peut être une personne distincte de l'acheteur | Utiliser le bon cadeau pour réserver simplement, sans démarche supplémentaire | oui |

*([CR-01, §4](./compte-rendu-entretien-01.md#4-parties-prenantes-identifiées) ; [CR-02, §4](./compte-rendu-entretien-02.md#4-parties-prenantes-identifiées) ; [CR-03, §4](./compte-rendu-entretien-03.md#4-parties-prenantes-identifiées))*

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
  ou privatisation), pour une personne seule ou pour un groupe.
- Modifier les tarifs et suivre le planning des réservations, depuis un
  espace de gestion réservé au gérant.
- Annuler un créneau pour raison météo et en informer les clients concernés.
- Consulter et réserver le site en français ou en anglais *(ajouté en v3,
  [CR-03/Q02](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues))*.
- Gérer les horaires d'ouverture et les jours de fermeture depuis l'espace
  de gestion *(ajouté en v3, [CR-03/Q01](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues))*.
- Créer un nouveau bateau depuis l'espace de gestion, pour qu'il apparaisse
  côté client *(ajouté en v3, [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues))*.
- Acheter et utiliser un bon cadeau, et saisir un code d'avoir au paiement
  *(ajouté en v3, [CR-03/Q05](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues), [Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues))*.

### Hors périmètre

Aussi important que la liste précédente. Chaque ligne cite la raison ou la
réponse du client.

| Élément écarté | Motif |
|---|---|
| Modification de groupe en autonomie par le client | Laissée à la discrétion du gérant, traitée au cas par cas par téléphone — REQ-013 §14 point 1 en amont, aucune règle formalisée (délai, frais) fournie par le client, y compris après relance ([CR-01, §6-2](./compte-rendu-entretien-01.md#6-ambiguïtés-détectées) ; reconfirmé le 2026-08-11) |
| Vente en ligne des suppléments personnalisables (ex. champagne à bord) | Le client les réserve uniquement par téléphone ([CR-01/Q04](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-01.md#5-règles-métier-découvertes)) |
| Annulation, report en autonomie par le client sur le site | Organisés par téléphone avec le gérant ([CR-01/Q13](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q17](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q20](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues)) |
| Modification du contenu client ou des créneaux depuis l'espace de gestion ; modification des bateaux existants (capacité, nom) | Non demandé par le client pour cette version ([CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes)) — *nuancé en v3 : l'**ajout** d'un nouveau bateau devient possible, [REQ-041](#96-espace-de-gestion-usage-réservé-au-gérant), [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues)* |
| Cumul d'un bon cadeau et d'un code d'avoir sur une même réservation | Non abordé par le client ; traité par défaut comme mutuellement exclusifs — hypothèse d'équipe, voir [impact-CR-001, §8](./impact-CR-001.md#8-effets-de-bord-identifiés) |
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
| 8 | Le site doit être livré en français et en anglais, alors que l'hypothèse par défaut retenue jusqu'à la v2 était le français seul (REQ-102) | technique | [CR-03/Q02](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| 9 | La vente de bons cadeaux (codes uniques, durée de validité, spécificité par type de sortie) n'était anticipée dans aucune conception antérieure ; elle introduit une donnée et un mécanisme de paiement mixte nouveaux | technique | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) ; voir [impact-CR-001, §5](./impact-CR-001.md#5-impact--conception) |

## 8. Règles métier

Les règles telles que le client les a énoncées, avant toute mise en forme de
spécification.

| # | Règle | Source |
|---|---|---|
| R-01 | Une réservation est possible pour une personne seule (une place) ou pour un groupe ; aucun minimum de personnes n'est imposé à une réservation individuelle | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; *corrigé par [CR-03/Q04](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) le 2026-08-12 — la lecture « à partir de 2 personnes » retenue en v1/v2 était trop stricte* |
| R-02 | Une sortie n'est maintenue qu'à partir de 6 personnes inscrites ; ce seuil est contrôlé 24 heures avant le départ | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-1](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; [CR-02/Q16](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| R-03 | Un seul bateau peut être engagé sur une sortie baleines à la fois, faute d'un second naturaliste | [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| R-04 | Le tarif enfant s'applique de 4 à 11 ans, le tarif adulte à partir de 12 ans ; accès interdit avant 4 ans | [CR-02/Q02](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| R-05 | Barème de remboursement dégressif selon le délai avant départ : 100 % au-delà de 7 jours, 25 % de commission entre 7 jours et 48 heures, 50 % de commission entre 48 heures et 24 heures | [CR-01/Q13](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| R-06 | Les réservations en ligne ferment 2 heures avant le départ pour le créneau de 14h, et la veille à midi pour les créneaux de 7h et 10h | [CR-01/Q09](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) ; précisé le 2026-08-11 |
| R-07 | Le paiement de la totalité du montant est exigé en carte bancaire au moment de la réservation ; aucun acompte, aucun autre moyen de paiement en ligne | [CR-01/Q10](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [Q12](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-01.md#5-règles-métier-découvertes) |
| R-08 | La répartition des passagers entre les deux bateaux, quand plusieurs sont disponibles, est décidée manuellement par le gérant | Précisé le 2026-08-11 |
| R-09 | Toute nouvelle réservation client passe exclusivement par le site ; le gérant n'en prend plus par téléphone | Précisé le 2026-08-11 |
| R-10 | L'entreprise est fermée le 25 décembre et le 1ᵉʳ janvier ; ces dates et les horaires d'ouverture sont modifiables depuis l'espace de gestion | [CR-03/Q01](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| R-11 | Le site est disponible en français et en anglais, au choix du client | [CR-03/Q02](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| R-12 | L'avoir accordé par le gérant est délivré sous forme d'un code de réduction unique, saisi par le client au moment du paiement d'une réservation future | [CR-03/Q05](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| R-13 | Le gérant peut créer un nouveau bateau depuis l'espace de gestion pour qu'il apparaisse sur l'interface de réservation | [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| R-14 | Un bon cadeau est valable 1 an à compter de son achat, spécifique à un type de sortie (baleines, dauphins ou privatisation), à usage unique, et utilisable uniquement en réservant sur la plateforme ; le client final paie la différence si le prix de la sortie dépasse le montant du bon, et perd le surplus sans remboursement si le bon dépasse le prix | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |

## 9. Exigences fonctionnelles

Une ligne par exigence. L'identifiant `REQ-xxx` est définitif : il est cité
par les spécifications et ne se renumérote jamais.

**Rappel :** trois cas d'usage sont retenus comme prioritaires par le client
(réserver et payer une sortie en ligne ; modifier les tarifs et suivre le
planning ; annuler un créneau météo et informer les clients concernés) — cf.
[§3](#3-objectifs). La quasi-totalité des exigences « Must » ci-dessous s'y
rattache. La vente de bons cadeaux ([§9.9](#99-bons-cadeaux-et-avoirs))
n'appartient à aucun des trois : c'est une contrainte ajoutée en v3, après
que le client l'a introduite de lui-même en cours d'entretien plutôt qu'en
réponse à une question ([CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues)) ;
elle est traitée comme « Must » au même titre, le client l'ayant formulée
comme une décision déjà prise, non comme une option.

### 9.1 Réservation en ligne

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-001 | Le client peut réserver une sortie en ligne pour une personne seule (une place) ou pour un groupe ; aucun minimum de personnes n'est imposé à une réservation individuelle. | Must | Client | [CR-01/Q02](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; *corrigé par [CR-03/Q04](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) le 2026-08-12 — v1/v2 retenaient à tort un minimum de 2 personnes* |
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
| REQ-040 | Le client peut consulter le site et effectuer une réservation en français ou en anglais, au choix. | Must | Client | [CR-03/Q02](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-010 | Trois créneaux de départ sont proposés chaque jour, à 7h, 10h et 14h, pour des sorties d'une durée d'environ 3 heures. | Must | Client | [CR-01/Q05](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) |
| REQ-011 | Du 15 juin au 31 octobre, les créneaux proposent des sorties dauphins ainsi que des sorties baleines ; en dehors de cette période, les mêmes créneaux proposent uniquement des sorties dauphins. | Must | Client | [CR-01/Q05](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues), [§7-4](./compte-rendu-entretien-01.md#7-contraintes-évoquées) |
| REQ-038 | Aucune sortie n'est proposée à la réservation le 25 décembre et le 1ᵉʳ janvier, jours de fermeture de l'entreprise. | Must | Client | [CR-03/Q01](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
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
| REQ-023 | Une fois l'annulation météo décidée, le gérant contacte par téléphone chaque client concerné pour lui proposer un report, un avoir ou un remboursement, et enregistre son choix. La matérialisation de l'avoir (code de réduction, saisie au paiement) est précisée en REQ-050. | Must | Gérant | [CR-02/Q04](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-024 | Si un nouveau créneau est proposé au client à la suite d'une annulation météo, cette proposition tient compte des disponibilités et de la météo, et est communiquée par téléphone. Si la première proposition ne convient pas au client, gérant et client s'accordent directement par téléphone sur un remplacement adapté ; aucune procédure automatisée n'est prévue en cas de désaccord. | Must | Gérant | [CR-02/Q06](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* — répond au point resté sans réponse en [CR-02, §9](./compte-rendu-entretien-02.md#9-ce-que-nous-navons-pas-abordé) |
| REQ-026 | Les clients sont prévenus par téléphone en cas d'annulation ou de modification. | Must | Gérant | [CR-02/Q09](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |

### 9.6 Espace de gestion (usage réservé au gérant)

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-028 | L'espace de gestion permet de modifier les tarifs. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-029 | L'espace de gestion permet d'obtenir le planning des réservations dans un format imprimable. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) |
| REQ-030 | L'espace de gestion ne permet pas de modifier le contenu présenté aux clients (messages de la page d'accueil ou autre contenu), ni les créneaux, ni les bateaux déjà existants (nom, capacité). L'ajout d'un nouveau bateau reste possible (REQ-041). | Won't *(sauf REQ-041)* | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02.md#5-règles-métier-découvertes) ; *nuancé par [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) le 2026-08-12* |
| REQ-031 | L'accès à l'espace de gestion est réservé à un compte unique, celui du gérant. | Must | Gérant | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) |
| REQ-033 | La flotte comprend deux bateaux à fond de verre : le Ti Kap (12 places) et Le Grand Bleu (24 places). Cette composition n'est pas modifiable depuis l'espace de gestion pour cette version (les bateaux existants ne peuvent pas être renommés ni recalibrés) ; l'ajout d'un nouveau bateau est en revanche possible (REQ-041). | Must | Gérant | [CR-01/Q01](./compte-rendu-entretien-01.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *nuancé par [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues)* |
| REQ-034 | La connexion à l'espace de gestion se fait avec une adresse e-mail et un mot de passe. Le mot de passe doit compter au moins 8 caractères, dont au moins une majuscule, une minuscule, un chiffre et un caractère spécial. | Must | Gérant | *Déduit* — aucune règle de sécurité pour l'accès au compte unique de gestion (REQ-031) n'a été discutée avec le client ; règle minimale retenue par l'équipe |
| REQ-039 | Le gérant peut modifier les horaires d'ouverture et les jours de fermeture (dont le 25 décembre et le 1ᵉʳ janvier, REQ-038) depuis l'espace de gestion. | Must | Gérant | [CR-03/Q01](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-041 | Le gérant peut créer un nouveau bateau (nom, capacité) depuis l'espace de gestion, pour qu'il apparaisse sur l'interface de réservation. Aucun besoin immédiat à ce jour : capacité anticipée par le client pour une évolution future de la flotte. | Should | Gérant | [CR-03/Q06](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) ; *le formulaire limité à nom + capacité, sans types de sorties compatibles, est une hypothèse d'équipe non confirmée par le client — voir [CR-03, §6-2](./compte-rendu-entretien-03.md#6-ambiguïtés-détectées), [§8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien)* |

### 9.7 Communication - compléments

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-025 | Un message type est envoyé automatiquement par le site au client, par défaut 24 heures avant sa sortie, avec les conditions météo prévues et la liste des affaires à prévoir. | Must | Client | [CR-02/Q08](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *contenu et automatisation confirmés par [CR-03/Q03](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) le 2026-08-12 — priorité relevée de Should à Must* |
| REQ-042 | Le gérant peut personnaliser, depuis l'espace de gestion, l'horaire d'envoi du message de rappel (par défaut 24 heures avant le départ, REQ-025). | Should | Gérant | [CR-03/Q03](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-027 | Le nouvel outil de réservation est le canal de communication principal ; WhatsApp reste utilisé comme canal de secours, en complément et non à sa place. | Should | Gérant, Client | [CR-02/Q07](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* — répond à l'ambiguïté relevée en [§6-5](./compte-rendu-entretien-02.md#6-ambiguïtés-détectées) |

### 9.8 Exploitation - élément de contexte

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-032 | À ce stade, l'outil est utilisé au quotidien par le gérant uniquement. | Must *(dimensionne les autres exigences)* | Gérant | [CR-02/Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [§7-3](./compte-rendu-entretien-02.md#7-contraintes-évoquées) |

### 9.9 Bons cadeaux et avoirs

Sous-section ajoutée en v3 : contrainte introduite spontanément par le
client en fin de troisième entretien, sans avoir été demandée — voir
[CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues)
et l'analyse d'impact [impact-CR-001.md](./impact-CR-001.md).

Révisée en v4 : le client est revenu sur le fonctionnement du bon cadeau lors
d'un échange oral le 2026-08-13. `REQ-045` est **inversée** (montant libre,
plus aucun rattachement à un type de sortie), `REQ-047` et `REQ-048` sont
précisées sur le montant total de la réservation, et `REQ-051` est ajoutée
(validité d'un an pour l'avoir). Voir l'analyse d'impact
[impact-CR-002.md](./impact-CR-002.md). Conséquence à trancher avec le
client : bon cadeau et avoir n'ont plus de différence de comportement, seule
leur origine les distingue (voir [§11](#11-questions-restées-ouvertes),
question 8).

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-043 | Le client peut acheter, sur la plateforme, un bon cadeau utilisable à tout moment sur le site. | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-044 | Un bon cadeau est valable 1 an à compter de sa date d'achat ; passé ce délai, il n'est plus utilisable. | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-045 | Un bon cadeau porte un montant libre, choisi par l'acheteur au moment de l'achat. Il n'est rattaché ni à un type de sortie, ni à une catégorie de tarif adulte ou enfant, et reste utilisable sur n'importe quelle réservation. | Must | Client | [CR-04/Q01](./compte-rendu-entretien-04.md#2-questions-posées-et-réponses-obtenues), [CR-04/Q02](./compte-rendu-entretien-04.md#2-questions-posées-et-réponses-obtenues), analyse d'impact [`impact-CR-002.md`](./impact-CR-002.md) ; *remplace en v4 la règle inverse issue du troisième entretien, qui rattachait le bon à un type de sortie déterminé à l'achat* |
| REQ-046 | Le bénéficiaire d'un bon cadeau renseigne son code au moment de réserver, exclusivement sur la plateforme (achat et usage), pour l'appliquer au paiement de sa réservation ; aucune réservation par téléphone n'accepte un bon cadeau. | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) ; *l'exclusion stricte du téléphone est une hypothèse d'équipe non confirmée par le client — voir [CR-03, §6-1](./compte-rendu-entretien-03.md#6-ambiguïtés-détectées), [§8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien)* |
| REQ-047 | Si le montant total de la réservation est supérieur au montant du bon cadeau, le bénéficiaire paie la différence en carte bancaire, selon les mêmes règles de paiement que le reste (REQ-017). | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) ; *« prix de la sortie » précisé en « montant total de la réservation » en v4, échange oral du 2026-08-13* |
| REQ-048 | Si le montant du bon cadeau est supérieur au montant total de la réservation, la différence n'est pas remboursée : le surplus est perdu. | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) ; *même précision qu'en REQ-047* |
| REQ-049 | Un bon cadeau est à usage unique : une fois utilisé pour une réservation, son code ne peut pas être réemployé. | Must | Client | [CR-03/Q07](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-050 | Un avoir accordé par le gérant (à la suite d'une annulation météo, REQ-023) est délivré sous forme d'un code de réduction unique, que le client saisit au moment de payer une réservation future. | Must | Gérant, Client | [CR-03/Q05](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
| REQ-051 | Un avoir est valable 1 an à compter de sa date d'émission par le gérant ; passé ce délai, il n'est plus utilisable. | Must | Gérant, Client | [CR-04/Q04](./compte-rendu-entretien-04.md#2-questions-posées-et-réponses-obtenues), analyse d'impact [`impact-CR-002.md`](./impact-CR-002.md) ; *origine à reconfirmer, voir [CR-04, §6-2](./compte-rendu-entretien-04.md#6-ambiguïtés-détectées)* |

## 10. Exigences non fonctionnelles

Trop souvent oubliées, et c'est là que les projets se cassent. Chacune doit
être vérifiable, et sourcée comme les autres.

| ID | Exigence | Comment on la vérifie | Source |
|---|---|---|---|
| REQ-100 | Volumétrie et pics de charge : usage attendu très faible (un seul gérant, deux bateaux, trois créneaux par jour), avec un pic de réservations en saison haute (15 juin - 31 octobre). | Pas de dégradation perceptible avec quelques dizaines de réservations simultanées en période de pointe. | *Déduit* — aucune volumétrie chiffrée fournie par le client ; hypothèse d'équipe basée sur la taille de la flotte (REQ-033) |
| REQ-101 | Support et conditions réseau : le site reste utilisable sur les principaux navigateurs desktop et mobiles, y compris en connexion mobile standard. | Test de réservation complet sur ordinateur, tablette et mobile, en 4G. | *Déduit*, lié à REQ-035 |
| REQ-102 | Langues : site disponible en français et en anglais (REQ-040). | Aucun contenu (y compris les messages automatiques, REQ-025) ne reste non traduit dans l'une des deux langues. | [CR-03/Q02](./compte-rendu-entretien-03.md#2-questions-posées-et-réponses-obtenues) |
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
| 1 | Quelles sont les règles précises (délai, frais, mode de validation) pour une modification de la taille d'un groupe après réservation ? | CR-01, reconfirmée le 2026-08-11, toujours non reposée à CR-03 | Laissée « à la discrétion de l'armateur », aucune règle formalisée, y compris après relance | Traité au cas par cas par téléphone avec le gérant, sans automatisation dans l'outil |
| 2 | Quel est le budget d'hébergement et le budget total du projet ? | Non posée | en attente | L'équipe recherche une solution d'hébergement à faible coût, sans engager le client au-delà de ce qui sera validé |
| 3 | Sous quel format la facture doit-elle être transmise au client (e-mail, PDF téléchargeable), avec quelles mentions légales ? | Non posée | en attente | Facture générée automatiquement par le prestataire de paiement (REQ-018) et transmise par e-mail au client |
| 4 | Quelle est la durée de conservation des données clients attendue par le client, et son point de départ (depuis la sortie ? depuis le dernier contact ?) ? | Non posée | en attente | Conservation minimale de 3 mois après la sortie (REQ-105), à confirmer |
| 5 | Les modalités précises de connexion à l'espace de gestion (identifiant, règle de mot de passe) conviennent-elles au gérant ? | [CR-02/Q03](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues) | Compte unique confirmé ; règle de mot de passe non discutée | Connexion par e-mail et mot de passe (REQ-034/REQ-104), à reconfirmer avec le client |
| 6 | Un bon cadeau peut-il être acheté ou utilisé par téléphone à titre exceptionnel, y compris par le gérant lui-même ? | [CR-03, §8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien) | en attente | Exclusion stricte du téléphone pour l'achat et l'usage d'un bon cadeau (REQ-046) |
| 7 | Le formulaire de création d'un nouveau bateau depuis l'espace de gestion doit-il inclure les types de sorties compatibles, ou seulement un nom et une capacité ? | [CR-03, §8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien) | en attente | Formulaire limité à nom + capacité (REQ-041), tout bateau créé habilité à tous les types de sortie |
| 8 | L'avoir et le bon cadeau sont-ils bien deux dispositifs séparés, ou un mécanisme de code unique ? **Question devenue critique en v4** : depuis l'échange du 2026-08-13, les deux portent un montant libre, expirent à un an, s'imputent sur le montant total et sont à usage unique. Seule leur origine les distingue (vendu au client contre accordé par le gérant). | [CR-03, §8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien), relancée le 2026-08-13 | en attente | Maintenus comme deux dispositifs distincts, non cumulables sur une même réservation (REQ-046, REQ-050) ; une classe unique porteuse d'un attribut d'origine reste la solution de repli si le client confirme l'équivalence |
| 9 | ~~Le prix d'achat d'un bon cadeau correspond-il au tarif standard d'une sortie au moment de l'achat, ou le client peut-il choisir un montant libre ?~~ | [CR-03, §8](./compte-rendu-entretien-03.md#8-questions-à-poser-au-prochain-entretien) | **répondue le 2026-08-13** : montant libre choisi par l'acheteur (REQ-045) | *sans objet* |
| 10 | Le montant d'un bon cadeau est-il borné (minimum, maximum, arrondi) ? Question ouverte par la réponse à la question 9 : un montant libre sans borne autorise aussi bien 3 € que 5 000 €. | Non posée, identifiée le 2026-08-13 | en attente | Montant entier, compris entre 10 € et le forfait de privatisation le plus élevé (1 100 €, REQ-014), à confirmer |

## 12. Validation client

| Version | Date | Présentée au client | Retour |
|---|---|---|---|
| v1 | — | non | Rédigée à partir de CR-01 et CR-02, sans relecture client formelle |
| v2 | 2026-08-11 | non | Intègre les réponses orales du client du 2026-08-11 (compte-rendu à formaliser) et reformate le document selon le template d'équipe |
| v3 | 2026-08-12 | non | Intègre les réponses du troisième entretien ([CR-03](./compte-rendu-entretien-03.md)) : corrige REQ-001, ajoute REQ-038 à REQ-050 (jours de fermeture, horaires, bilinguisme, création de bateau, bons cadeaux, avoir), documenté dans [impact-CR-001.md](./impact-CR-001.md) |
| v4 | 2026-08-13 | non | Intègre l'échange oral du 2026-08-13 : REQ-045 inversée (bon cadeau à montant libre, sans type de sortie), REQ-047 et REQ-048 précisées sur le montant total, REQ-051 ajoutée (avoir valable 1 an), question 9 du §11 répondue et question 10 ouverte, documenté dans [impact-CR-002.md](./impact-CR-002.md). Compte rendu de l'entretien à formaliser en CR-04 |

## 13. Glossaire

Définitions en langage courant, telles qu'utilisées par le client.

- **Créneau** : un horaire de départ fixe (7h, 10h ou 14h) proposé pour une sortie.
- **Sortie** : la prestation en mer proposée à un horaire donné, sur un bateau donné (baleines ou dauphins selon la saison).
- **Forfait** : la formule de réservation standard.
- **Privatisation** : réservation qui bloque un bateau entier sur un créneau, sans réduction de tarif.
- **Report** : déplacement d'une réservation existante vers une autre date, à la différence d'une annulation qui met fin à la réservation.
- **Avoir** : montant crédité au client par le gérant, utilisable pour une réservation future, proposé comme alternative au remboursement ; matérialisé par un code de réduction unique, saisi au paiement, valable 1 an à compter de son émission.
- **Bon cadeau** : code acheté sur le site pour un montant libre, offert ou utilisé plus tard par son bénéficiaire pour réserver n'importe quelle sortie, valable 1 an, à usage unique.
- **Espace de gestion** : la partie de l'outil réservée au gérant, pour modifier les tarifs, obtenir le planning des réservations, gérer les horaires d'ouverture et créer un nouveau bateau.

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
- `REQ-100`, `REQ-101`, `REQ-103`, `REQ-105` — les exigences non
  fonctionnelles pour lesquelles aucune donnée chiffrée ou aucune décision
  n'a été confirmée par le client (voir [§11](#11-questions-restées-ouvertes)).

`REQ-102` (langues) sortait de cette liste en v2 uniquement faute d'avoir été
posée au client ; ce n'est plus une hypothèse d'équipe depuis `CR-03/Q02`.

Deux exigences de la v3 (`REQ-041`, `REQ-046`) citent bien un échange consigné
(`CR-03/Q06`, `CR-03/Q07`) mais reposent en partie sur une hypothèse d'équipe
non encore confirmée par le client — la formulation du client laissait place
à plusieurs lectures (voir [CR-03, §6](./compte-rendu-entretien-03.md#6-ambiguïtés-détectées)).
Ce n'est pas un « déduit » au sens strict (l'origine de l'exigence est bien un
échange client), mais un point à reconfirmer, tracé au [§11](#11-questions-restées-ouvertes),
questions 6 et 7.

Toutes les autres exigences ont été dites explicitement par le client.
`./tools/traceability.sh`, s'il est ajouté au projet, devra vérifier que
chaque `REQ` a une source et que l'échange cité existe réellement dans les
comptes rendus.
