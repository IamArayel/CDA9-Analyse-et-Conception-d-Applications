# Cahier des charges - Ti Baleine

**Équipe :** `Le Trio`
- [Chloe Baisse](mailto:baissechloe@gmail.com)
- [Arnaud Maxime](mailto:arnaudmaxime.bidel@gmail.com)
- [Anthony Dégeilh](mailto:anthony.degeilh@gmail.com)

---

**Rédigé à partir de :**
[`compte-rendu-entretien-01-points-1-4.md`](./compte-rendu-entretien-01-points-1-4.md) (CR-01)
et [`compte-rendu-entretien-02-points-5-8.md`](./compte-rendu-entretien-02-points-5-8.md) (CR-02).

---

## 0. Objet et méthode

Ce document répertorie les exigences (`REQ-0xx`) déduites de ce que le client
a effectivement dit lors des deux entretiens. Chaque exigence cite la
question dont elle provient (`CR-01/Qnn` ou `CR-02/Qnn`) ou, à défaut, la
règle métier correspondante (`CR-0n/§5-n`).

Aucune exigence n'a été ajoutée au-delà de ce que le client a confirmé. Les
sujets évoqués mais non tranchés (réponse floue, question restée sans
réponse, ambiguïté non levée) ne sont volontairement **pas** transformés en
exigence : ils sont listés à part, au [§14](#14-points-restant-à-clarifier), comme points à clarifier.

## 1. Présentation du besoin

Ti Baleine propose des sorties en mer sur plusieurs créneaux par jour, avec
deux bateaux à capacité limitée. Les réservations sont aujourd'hui gérées par
téléphone et par WhatsApp. Le gérant souhaite que ses clients puissent
réserver et payer directement en ligne, pour ne plus avoir à suivre à la main
les annulations de dernière minute, les changements de taille de groupe et
les reports liés à la météo. *([CR-01, §1](./compte-rendu-entretien-01-points-1-4.md#1-ce-que-le-client-a-dit))*

## 2. Parties prenantes

| Personne / rôle | Ce qu'elle fait |
|---|---|
| Gérant / armateur | Seul utilisateur prévu de l'outil à ce stade ; modifie les tarifs, décide seul des annulations météo, contacte les clients concernés, valide les reports, avoirs ou remboursements |
| Naturaliste | Présence obligatoire à bord pour une sortie baleines ; un seul naturaliste est disponible, ce qui limite à un bateau à la fois pour ce type de sortie |
| Client / passager | Réserve et paie en ligne, choisit sa sortie (dauphins/baleines) parmi les places disponibles affichées, peut être recontacté par téléphone en cas d'annulation météo, peut reporter sa réservation |

*([CR-01, §4](./compte-rendu-entretien-01-points-1-4.md#4-parties-prenantes-identifiées) ; [CR-02, §4](./compte-rendu-entretien-02-points-5-8.md#4-parties-prenantes-identifiées))*

## 3. Périmètre retenu pour cette version

Le projet ne peut pas couvrir tous les sujets abordés en entretien dans les
délais impartis. Trois usages sont retenus comme indispensables ; le reste
est marqué « souhaitable » ou renvoyé au [§14](#14-points-restant-à-clarifier) tant qu'il n'est pas assez précis
pour être construit. *(Ce découpage en trois usages prioritaires est un choix
de cadrage de l'équipe, à confirmer - il ne provient pas directement d'un
compte rendu.)*

1. **Réserver et payer une sortie en ligne** - sections 4, 5, 6, 7 ci-dessous.
2. **Modifier les tarifs et suivre le planning des réservations** - section 9.
3. **Annuler un créneau pour raison météo et en informer les clients concernés** - section 8.

Chaque exigence ci-dessous porte une colonne « Priorité » : Indispensable
(rattachée à l'un des trois usages retenus) ou Souhaitable (utile, mais pas
nécessaire à ces trois usages).

## 4. Réservation en ligne

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-001 | Le client peut réserver une sortie en ligne à partir de 2 personnes. | Indispensable | [CR-01/Q02](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) |
| REQ-036 | Toute nouvelle réservation client se fait exclusivement via le site de réservation en ligne ; le gérant ne prend plus de nouvelle réservation par téléphone ou WhatsApp (ces canaux restent utilisés pour l'annulation, le report et le suivi, cf. REQ-019, REQ-020). | Indispensable | *Précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [CR-02/Q12](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§6-2](./compte-rendu-entretien-02-points-5-8.md#6-ambiguïtés-détectées) |
| REQ-002 | Une sortie n'est maintenue qu'à partir de 6 personnes inscrites sur le créneau ; ce nombre est vérifié 24 heures avant le départ. | Indispensable | [CR-01/Q02](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-1](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) ; [CR-02/Q16](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-003 | Si le nombre de 6 personnes n'est pas atteint au moment du contrôle, la sortie est annulée et les clients déjà inscrits sont remboursés. | Indispensable | [CR-02/Q16](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-004 | Au moment de réserver, le client voit le nombre de places encore disponibles sur chaque type de sortie. | Indispensable | [CR-01/Q08](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) |
| REQ-005 | Les réservations en ligne ferment 2 heures avant le départ pour le créneau de 14h (soit à midi le jour même), et la veille à midi pour les créneaux de 7h et de 10h. | Indispensable | [CR-01/Q09](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) ; *précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [CR-02, §6-3](./compte-rendu-entretien-02-points-5-8.md#6-ambiguïtés-détectées) |
| REQ-006 | Le client peut réserver une privatisation, qui bloque un bateau entier sur un créneau, aussi bien le matin (brunch) que l'après-midi (coucher de soleil). | Indispensable | [CR-01/Q03](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q01](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-007 | Un seul bateau à la fois peut être engagé sur une sortie baleines, faute d'un second naturaliste disponible ; les deux bateaux peuvent en revanche être utilisés en même temps pour des sorties dauphins. | Indispensable | [CR-02/Q10](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |
| REQ-037 | Lorsque plusieurs bateaux sont disponibles pour un même type de sortie, la répartition des passagers entre les deux bateaux est décidée manuellement par le gérant ; aucune règle de répartition automatique n'est demandée pour cette version. | Indispensable *(délimite le périmètre)* | *Précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [CR-02/Q10](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§6-1](./compte-rendu-entretien-02-points-5-8.md#6-ambiguïtés-détectées) |
| REQ-008 | L'accès à une sortie est interdit aux enfants de moins de 4 ans. | Indispensable | [CR-02/Q02](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-009 | Lors de la réservation, le client fournit : nom, prénom, e-mail, numéro de téléphone, nombre d'adultes et d'enfants, date et heure du créneau choisi, et type de sortie correspondant à la saison. Aucune information supplémentaire n'est demandée. | Indispensable | [CR-02/Q18](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) (le client a exclu toute information particulière) ; liste précise des champs *déduite* par l'équipe — ce sont les informations minimales pour identifier le client, le contacter et composer le créneau |
| REQ-035 | Le site de réservation s'adapte à la taille de l'écran utilisé par le client, qu'il réserve depuis un ordinateur, une tablette ou un téléphone mobile. | Indispensable | *Déduit* — le client vise une réservation « directement en ligne » ([CR-01, §1](./compte-rendu-entretien-01-points-1-4.md#1-ce-que-le-client-a-dit)) sans avoir précisé les appareils visés ; l'équipe retient un accès utilisable sur les principaux types d'appareils, non discuté explicitement avec le client |
| REQ-010 | Trois créneaux de départ sont proposés chaque jour, à 7h, 10h et 14h, pour des sorties d'une durée d'environ 3 heures. | Indispensable | [CR-01/Q05](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) |
| REQ-011 | Du 15 juin au 31 octobre, les créneaux proposent des sorties dauphins ainsi que des sorties baleines ; en dehors de cette période, les mêmes créneaux proposent uniquement des sorties dauphins. | Indispensable | [CR-01/Q05](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§7-4](./compte-rendu-entretien-01-points-1-4.md#7-contraintes-évoquées) |
| REQ-012 | Deux formules sont proposées à la réservation : un forfait standard et une privatisation (sans tarif préférentiel). | Indispensable | [CR-01/Q04](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-2](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes), [§5-3](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) |
| REQ-013 | Les suppléments personnalisables (par exemple le champagne à bord) ne sont pas proposés à la réservation en ligne ; ils restent vendus uniquement par téléphone. | Indispensable *(délimite le périmètre)* | [CR-01/Q04](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-4](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) |

## 5. Tarification

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-014 | Les tarifs appliqués sont : sortie baleines **65 € par adulte** et **40 € par enfant** ; sortie dauphins **50 € par adulte** et **30 € par enfant** ; privatisation **600 € pour le Ti Kap** et **1100 € pour Le Grand Bleu**. | Indispensable | [CR-01/Q07](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) |
| REQ-015 | Le tarif enfant s'applique de 4 à 11 ans, le tarif adulte à partir de 12 ans ; il n'existe pas de tarif pour les moins de 4 ans, l'accès leur étant interdit. | Indispensable | [CR-02/Q02](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-016 | Le gérant peut modifier les tarifs lui-même, en général une fois par an. | Indispensable | [CR-01/Q07](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |

## 6. Paiement

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-017 | Le paiement de la totalité du montant est exigé en ligne, par carte bancaire, au moment de la réservation. Aucun acompte, et aucun autre moyen de paiement (espèces, virement, chèque) n'est accepté en ligne. | Indispensable | [CR-01/Q10](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [Q12](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) |
| REQ-018 | Aucun prestataire de paiement en ligne n'était en place au moment des entretiens ; le client n'imposait aucune préférence entre Stripe, PayPal ou l'offre de paiement en ligne de sa banque (Crédit Agricole), à condition de retenir la moins coûteuse des trois. Après comparaison, l'équipe a retenu **Stripe** : solution la moins chère des trois étudiées, et permettant en complément la génération automatique de facture pour le client. | Indispensable *(préalable au reste)* | Critère de choix (coût le plus bas, absence de préférence du client) : [CR-01/Q11](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q19](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes). Choix final de Stripe et critère de facturation : *déduit* — décision d'équipe (comparatif de coûts, documenté dans le cahier des charges technique), non redemandée au client |

## 7. Annulation et report à l'initiative du client

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-019 | En cas d'annulation par le client, le montant remboursé suit un barème dégressif : 100 % au-delà de 7 jours avant le départ ; 25 % de commission retenue entre 7 jours et 48 heures avant le départ ; 50 % de commission retenue entre 48 heures et 24 heures avant le départ. La demande d'annulation et son remboursement sont organisés par téléphone avec le gérant, et non en autonomie sur le site ; un éventuel canal e-mail ne sert qu'à notifier une annulation déjà décidée par téléphone, jamais à en faire la demande. | Indispensable | [CR-01/Q13](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues), [§5-8](./compte-rendu-entretien-01-points-1-4.md#5-règles-métier-découvertes) ; [CR-02/Q20](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-9](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) ; *précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [CR-02/Q04](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§6-4](./compte-rendu-entretien-02-points-5-8.md#6-ambiguïtés-détectées) |
| REQ-020 | Le client peut reporter sa réservation à une autre date, y compris à moins de 24 heures du départ, sous réserve de disponibilité. Ce report est organisé par téléphone avec le gérant, et non en autonomie sur le site. | Indispensable | [CR-02/Q17](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-6](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) ; [Q20](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-9](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |

## 8. Annulation météo à l'initiative du gérant

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-021 | La décision d'annuler un créneau pour raison météo appartient uniquement au gérant ; elle n'est pas déclenchée automatiquement. | Indispensable | [CR-02/Q04](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [Q05](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-7](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |
| REQ-022 | Avant de valider une annulation météo, le gérant doit pouvoir visualiser la situation du créneau concerné (clients inscrits) ; l'annulation n'a lieu qu'après cette validation. | Indispensable | [CR-02/Q05](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-023 | Une fois l'annulation météo décidée, le gérant contacte par téléphone chaque client concerné pour lui proposer un report, un avoir ou un remboursement, et enregistre son choix. | Indispensable | [CR-02/Q04](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-024 | Si un nouveau créneau est proposé au client à la suite d'une annulation météo, cette proposition tient compte des disponibilités et de la météo, et est communiquée par téléphone. Si la première proposition ne convient pas au client, gérant et client s'accordent directement par téléphone sur un remplacement adapté ; aucune procédure automatisée n'est prévue en cas de désaccord. | Indispensable | [CR-02/Q06](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond au point resté sans réponse en [CR-02, §9](./compte-rendu-entretien-02-points-5-8.md#9-ce-que-nous-navons-pas-abordé) |
| REQ-026 | Les clients sont prévenus par téléphone en cas d'annulation ou de modification. | Indispensable | [CR-02/Q09](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |

## 9. Espace de gestion (usage réservé au gérant)

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-028 | L'espace de gestion permet de modifier les tarifs. | Indispensable | [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |
| REQ-029 | L'espace de gestion permet d'obtenir le planning des réservations dans un format imprimable. | Indispensable | [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |
| REQ-030 | L'espace de gestion ne permet pas de modifier le contenu présenté aux clients (messages de la page d'accueil ou autre contenu), ni la composition de la flotte, ni les créneaux. | Indispensable *(délimite le périmètre)* | [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§5-3](./compte-rendu-entretien-02-points-5-8.md#5-règles-métier-découvertes) |
| REQ-031 | L'accès à l'espace de gestion est réservé à un compte unique, celui du gérant. | Indispensable | [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-033 | La flotte comprend deux bateaux à fond de verre : le Ti Kap (12 places) et Le Grand Bleu (24 places). Cette composition n'est pas modifiable depuis l'espace de gestion pour cette version. | Indispensable | [CR-01/Q01](./compte-rendu-entretien-01-points-1-4.md#2-questions-posées-et-réponses-obtenues) ; [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-034 | La connexion à l'espace de gestion se fait avec une adresse e-mail et un mot de passe. Le mot de passe doit compter au moins 8 caractères, dont au moins une majuscule, une minuscule, un chiffre et un caractère spécial. | Indispensable | *Déduit* — aucune règle de sécurité pour l'accès au compte unique de gestion (REQ-031) n'a été discutée avec le client ; règle minimale retenue par l'équipe |

## 10. Communication - compléments souhaitables

Ces points ont été confirmés par le client mais ne sont pas nécessaires aux
trois usages retenus au §3 ; ils sont donc classés « souhaitables » plutôt
qu'indispensables pour cette version.

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-025 | Un message de rappel est envoyé au client 24 heures avant sa sortie, avec les conditions météo prévues et la liste des affaires à prévoir. | Souhaitable | [CR-02/Q08](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |
| REQ-027 | Le nouvel outil de réservation est le canal de communication principal ; WhatsApp reste utilisé comme canal de secours, en complément et non à sa place. | Souhaitable | [CR-02/Q07](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) ; *précisé par le client le 2026-08-11* (échange oral, compte-rendu à formaliser) — répond à l'ambiguïté relevée en [§6-5](./compte-rendu-entretien-02-points-5-8.md#6-ambiguïtés-détectées) |

## 11. Exploitation - élément de contexte

| ID | Exigence | Priorité | Source |
|---|---|---|---|
| REQ-032 | À ce stade, l'outil est utilisé au quotidien par le gérant uniquement. | Indispensable *(dimensionne les autres exigences)* | [CR-02/Q10](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [§7-3](./compte-rendu-entretien-02-points-5-8.md#7-contraintes-évoquées) |

## 12. Contraintes et éléments de contexte

Conditions qui pèsent sur le projet sans être, en elles-mêmes, une action que
l'outil doit permettre.

| # | Constat | Source |
|---|---|---|
| 1 | Aucun prestataire de paiement en ligne n'existe actuellement ; seul un terminal de paiement classique est utilisé sur place. | [CR-01, §7-3](./compte-rendu-entretien-01-points-1-4.md#7-contraintes-évoquées) |
| 2 | La saison des sorties baleines est limitée du 15 juin au 31 octobre. | [CR-01, §7-4](./compte-rendu-entretien-01-points-1-4.md#7-contraintes-évoquées) |
| 3 | Le gérant est, à ce jour, l'unique utilisateur prévu de l'outil (aucun salarié, aucun accès distinct pour un capitaine). | [CR-02, §7-3](./compte-rendu-entretien-02-points-5-8.md#7-contraintes-évoquées) |
| 4 | Il n'existe aujourd'hui aucun site internet (seulement une page Facebook, confirmée à jour par le client mais qui ne sera plus utilisée pour la gestion des réservations) ni logiciel de comptabilité ou de caisse ; la comptabilité est tenue manuellement. | [CR-02, §7-4](./compte-rendu-entretien-02-points-5-8.md#7-contraintes-évoquées), [Q13](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [Q14](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) ; actualité confirmée par le client le 2026-08-11 (échange oral, compte-rendu à formaliser) |
| 5 | Le client dispose d'un logo mais pas d'une charte graphique définie. | [CR-02/Q15](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) |

## 13. Glossaire

Définitions en langage courant, telles qu'utilisées par le client.

- **Créneau** : un horaire de départ fixe (7h, 10h ou 14h) proposé pour une sortie.
- **Sortie** : la prestation en mer proposée à un horaire donné, sur un bateau donné (baleines ou dauphins selon la saison).
- **Forfait** : la formule de réservation standard.
- **Privatisation** : réservation qui bloque un bateau entier sur un créneau, sans réduction de tarif.
- **Report** : déplacement d'une réservation existante vers une autre date, à la différence d'une annulation qui met fin à la réservation.
- **Avoir** : montant crédité au client, utilisable pour une réservation future, proposé comme alternative au remboursement.
- **Espace de gestion** : la partie de l'outil réservée au gérant, pour modifier les tarifs et obtenir le planning des réservations.

## 14. Points restant à clarifier

Ces sujets ont été abordés avec le client mais la réponse reste floue,
incomplète, ou contradictoire avec une autre réponse. Ils ne sont **pas**
transformés en exigence tant qu'ils ne sont pas tranchés - les inclure
maintenant reviendrait à inventer une fonctionnalité.

*Mise à jour du 2026-08-11 :* le client a levé oralement six des dix
ambiguïtés listées dans la version précédente de ce tableau (répartition des
bateaux, canal de réservation exclusif, écart jauge/fermeture, canal e-mail
d'annulation, place de WhatsApp, actualité de la page Facebook, proposition
d'un nouveau créneau après annulation météo, arrivée de salariés). Ces
réponses ont été intégrées à REQ-005, REQ-019, REQ-024, REQ-027, REQ-036,
REQ-037 et à la contrainte n°4 du [§12](#12-contraintes-et-éléments-de-contexte).
Un compte rendu formel de cet échange reste à rédiger pour compléter la
traçabilité (actuellement notée « échange oral du 2026-08-11 » dans les
exigences concernées). Les deux points restés sans réponse, ainsi que six
nouveaux points identifiés en préparant le prochain rendez-vous, sont listés
ci-dessous.

| # | Sujet | Ce qui reste incertain | Source |
|---|---|---|---|
| 1 | Modification de la taille d'un groupe après réservation | Laissée « à la discrétion de l'armateur », sans règle formalisée (délai, frais, mode de validation) ; reconfirmé sans détail supplémentaire par le client le 2026-08-11 | [CR-01, §6-2](./compte-rendu-entretien-01-points-1-4.md#6-ambiguïtés-détectées) ; toujours ouvert d'après [CR-02, §9](./compte-rendu-entretien-02-points-5-8.md#9-ce-que-nous-navons-pas-abordé) ; reconfirmé oralement le 2026-08-11 |
| 2 | Contenu exact du message de rappel à J-1 | Seules l'échéance et les grandes lignes sont connues, pas le texte lui-même ; le client a indiqué vouloir le définir lors du prochain rendez-vous | [CR-02, §9](./compte-rendu-entretien-02-points-5-8.md#9-ce-que-nous-navons-pas-abordé) ; reporté par le client au 2026-08-11 |
| 3 | Jours de fermeture de l'entreprise | Non abordé en entretien : existe-t-il des jours de fermeture ? Si oui, sont-ils modifiables, et par qui, depuis l'espace de gestion ? | Question préparée par l'équipe (Notion — J2 Cahier des charges), non encore posée au client |
| 4 | Budget d'hébergement / budget total du projet | Non validé avec le client ; l'équipe a présélectionné un hébergement mutualisé (Hostinger, ≈3 €/mois) sans confirmation du budget réel du client | Question préparée par l'équipe (Notion — J2 Cahier des charges), non encore posée au client |
| 5 | Diversité linguistique de la clientèle | Non abordé : proportion de clients non francophones, besoin ou non d'une version multilingue du site | Question préparée par l'équipe (Notion — J2 Cahier des charges), non encore posée au client |
| 6 | Format de la facture générée par Stripe | La génération de facture (REQ-018) est une capacité du prestataire, mais le canal (e-mail, PDF téléchargeable), les mentions légales et le moment d'envoi ne sont pas validés avec le client | *Déduit* — non discuté avec le client |
| 7 | Durée de conservation des données clients (RGPD) | Une durée minimale de 3 mois avant suppression figure dans les notes techniques de l'équipe, sans point de départ précisé (depuis la sortie ? depuis le dernier contact ?) ni validation du client | Notion — J2 Cahier des charges (note RGPD), non discuté avec le client |
| 8 | Modalités précises de connexion à l'espace de gestion | L'identifiant (e-mail) et la règle de mot de passe de REQ-034 sont déduits par l'équipe ; jamais explicitement reconfirmés par le client | [CR-02/Q03](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues), [Q10](./compte-rendu-entretien-02-points-5-8.md#2-questions-posées-et-réponses-obtenues) ; à reconfirmer avec le client |

## 15. Traçabilité

Chaque exigence de ce document cite sa source sous la forme `CR-0n/Qnn` (une
question posée en entretien) ou `CR-0n/§5-n` (une règle métier reformulée
dans le compte rendu correspondant). Quatre exigences sont marquées « déduit »,
faute d'avoir été discutées explicitement avec le client, chacune avec sa
justification dans la colonne Source :

- `REQ-009` — la liste précise des champs du formulaire de réservation (le
  client a seulement exclu toute information particulière, CR-02/Q18) ;
- `REQ-018` — le choix final de Stripe comme prestataire de paiement et le
  critère complémentaire de génération de facture (le client a seulement
  demandé de retenir la solution la moins chère, sans imposer laquelle) ;
- `REQ-034` — la règle de mot de passe de l'espace de gestion ;
- `REQ-035` — l'adaptation du site aux différents écrans.

Toutes les autres exigences ont été dites explicitement par le client.

