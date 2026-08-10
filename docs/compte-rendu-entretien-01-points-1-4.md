# Compte rendu d'entretien n° 1 — synthèse des points 1 à 8

**Date :** …
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …

> Ce document reprend, au format du gabarit `compte-rendu-entretien-01.md`, les
> points 1 à 4 de la trame « Questions au client » (flotte/capacités, créneaux/
> prestations, tarification, paiement/annulation) — déjà échangés en entretien —
> ainsi que les points 5 à 8 (météo, communication, exploitation quotidienne,
> intégrations techniques). Les questions des points 5 à 8 n'ont pas encore de
> réponse : ce fichier est envoyé au client, qui complétera par e-mail.

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë — c'est
précisément l'ambiguïté qu'il faudra lever.

> « Je gère mes réservations par téléphone et WhatsApp. Entre les annulations de
> dernière minute, les groupes qui changent de taille et la météo qui m'oblige à
> décaler des sorties, je m'y perds. J'aimerais que mes clients puissent réserver
> directement en ligne et payer. »

Contexte : Ti Baleine propose des sorties en mer, sur plusieurs créneaux par
jour, avec plusieurs bateaux à capacité limitée.

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la trace
de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-01/Q07` désigne la question 7 de ce
compte rendu. La numérotation est définitive — on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | Combien de bateaux l'entreprise possède-t-elle ? Quelles sont leurs caractéristiques (nom, type, modèle) ? | 2 bateaux à fond de verre (observation du fond marin) : *Ti Kap* (12 places) et *Le Grand Bleu* (24 places). |
| Q02 | Quelle est la capacité maximale de passagers par bateau ? Existe-t-il une jauge minimale de passagers pour maintenir un départ ? | Une réservation peut être prise à partir de 2 personnes, mais la sortie n'est maintenue qu'à partir de 6 personnes ; en dessous, elle est annulée. |
| Q03 | Certains bateaux sont-ils réservés à des types de sorties particulières (privatisation, excursion VIP, observation spécifique) ? | Oui : un bateau peut être bloqué en entier sur un créneau pour une réservation privée (privatisation). |
| Q04 | Proposez-vous différentes formules (sortie découverte 2h, coucher de soleil, privatisation journée) ? | Formule « forfait » à 4 ou 12 personnes (identiques en pratique) et formule « privatisation » (sans tarif préférentiel). Les suppléments (ex. champagne) restent personnalisables et vendus uniquement par téléphone. |
| Q05 | Quels sont les créneaux horaires fixes par jour ? Varient-ils selon la saison ou les jours de la semaine ? | 3 départs/jour à 7h, 10h, 14h, pour des sorties d'environ 3h. Saison baleines du 15 juin au 31 octobre (sinon sortie « Cétacés dauphins », mêmes créneaux) ; la saison change la nature de la sortie, pas les horaires. |
| Q06 | Quel est le délai d'escale/nettoyage nécessaire entre deux sorties pour un même bateau ? | Sortie baleines : 2h30 ; sortie dauphins : 2h ; 30 min à 1h de préparation du bateau ensuite. Privatisation = une demi-journée, souvent l'après-midi (sunset). |
| Q07 | Quels sont les tarifs (adulte, enfant, bébé, groupe, privatisation complète) ? | Baleines : 65 € adulte / 40 € enfant. Dauphins : 50 € adulte / 30 € enfant. Privatisation : 600 € (*Ti Kap*) / 1 100 € (*Le Grand Bleu*). Tarifs révisés chaque année, saisis en back-office par l'armateur. *(tarif bébé non chiffré — sans réponse)* |
| Q08 | Quelle est la taille maximale d'un groupe en ligne ? Comment gérer les modifications de taille de groupe après réservation ? | Réponse partielle : en ligne, le client voit les places disponibles par bateau et choisit lui-même son bateau ; toute modification après réservation reste « à la discrétion de l'armateur » — règle non formalisée. |
| Q09 | Jusqu'à combien de temps avant le départ un client peut-il réserver en ligne ? | Jusqu'à 12h avant le départ (12h la veille si départ l'après-midi ou le lendemain). |
| Q10 | Souhaitez-vous le paiement de la totalité à la commande ou le versement d'un acompte ? | Paiement de la totalité au moment de la réservation, sur le site. |
| Q11 | Avez-vous déjà un contrat monétique/banque ou un prestataire privilégié (Stripe, PayPal, etc.) ? | Non, aucun prestataire de paiement en ligne actuellement ; un TPE bancaire classique est utilisé sur place. |
| Q12 | Le paiement doit-il être intégralement en ligne ou une partie en espèces ? | Intégralement en ligne, carte bancaire uniquement — ni espèces, ni virement, ni chèque. |
| Q13 | Quelles sont les conditions de remboursement en cas d'annulation par le client ? | Barème dégressif : remboursement à 100 % au-delà de J-7 ; 25 % de commission retenue entre J-7 et J-48h ; 50 % retenue entre J-48h et J-24h. *(le traitement des annulations à moins de 24h, et la possibilité de reporter plutôt qu'annuler, restent sans réponse)* |
| Q14 | **Météo —** Qui prend la décision d'annuler un créneau, et comment s'effectue le choix entre report de date et remboursement intégral ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q15 | **Météo —** Souhaitez-vous déclencher l'annulation d'un créneau en un clic et informer automatiquement tous les passagers impactés ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q16 | **Météo —** Comment le client doit-il pouvoir choisir un nouveau créneau en cas d'annulation météo (lien dédié, avoir, contact direct) ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q17 | **Communication —** Souhaitez-vous conserver WhatsApp (API WhatsApp Business) en parallèle des e-mails/SMS pour les confirmations, rappels et alertes météo ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q18 | **Communication —** À quel moment envoyer un rappel de sortie aux clients (ex. J-1, avec consignes d'embarquement et lieu de rendez-vous) ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q19 | **Communication —** Comment les clients sont-ils avertis d'une annulation ou d'une modification ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q20 | **Exploitation —** Qui va manipuler l'outil au quotidien (capitaine sur smartphone, personne au guichet/bureau) ? Plusieurs types de compte (admin, salariés…) ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q21 | **Exploitation —** Avez-vous besoin d'une vue « Planning du jour » imprimable ou consultable sur mobile pour vérifier la liste des passagers à l'embarquement ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q22 | **Exploitation —** Devez-vous pouvoir ajouter manuellement des réservations prises sur place ou au téléphone, pour bloquer des places dans le système ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q23 | **Intégrations —** Avez-vous déjà un site web (WordPress, Shopify…) sur lequel intégrer le module de réservation, ou faut-il concevoir la plateforme de A à Z ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q24 | **Intégrations —** Utilisez-vous un logiciel comptable ou de caisse particulier avec lequel l'application doit communiquer ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q25 | **Gestion des durées —** La privatisation est-elle possible aussi le matin, ou uniquement l'après-midi ? | — *(question envoyée par e-mail, réponse en attente)* |
| Q26 | **Grille tarifaire —** Quelle est la tranche d'âge d'un "enfant" | — *(question envoyée par e-mail, réponse en attente)* |

Une question posée et **restée sans réponse** figure quand même ici, avec
« sans réponse » : c'est une trace, et elle sert au §8.

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage : s'il
répond « non, pas tout à fait », la compréhension n'est pas acquise.

La flotte compte deux bateaux à capacités différentes (12 et 24 places). Une
réservation en ligne peut être ouverte dès 2 passagers, mais le système doit
distinguer « réservation acceptée » de « sortie confirmée » : en dessous de 6
inscrits sur le créneau, le départ est annulé — ce n'est donc pas la
réservation individuelle qui est bloquée, mais le créneau entier qui devient
incertain tant que le seuil n'est pas atteint.

L'offre commerciale est simple : deux familles de sorties (baleines en saison,
dauphins hors saison) sur les mêmes 3 créneaux quotidiens, un forfait standard
(4 ou 12 personnes, sans distinction tarifaire réelle) et une offre de
privatisation qui bloque un bateau entier sans réduction de prix. Les
suppléments plus personnalisés (champagne, etc.) sortent volontairement du
périmètre de la réservation en ligne et restent gérés par téléphone.

Côté paiement, le client veut un système strict et simple : 100 % du montant
encaissé par carte bancaire au moment de la réservation, sans acompte, sans
autre moyen de paiement, et sans prestataire de paiement déjà en place — c'est
donc un choix de solution de paiement en ligne qui reste entièrement à faire.
La politique d'annulation est déjà chiffrée par paliers (J-7, J-48h, J-24h),
mais le sort exact d'une annulation dans les dernières 24h, et l'existence
d'une option de report plutôt que d'annulation, ne sont pas encore tranchés.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
|---|---|---|
| Armateur / gérant Ti Baleine | Définit et révise les tarifs en back-office, décide de la jauge minimale de départ, arbitre les modifications de groupe après réservation | Interlocuteur direct de l'entretien, cité comme décideur sur Q07 et Q08 |
| Client / passager | Réserve et paie en ligne, choisit son bateau parmi les places disponibles affichées | Décrit dans le parcours de réservation en ligne (Q08, Q09) |

## 5. Règles métier découvertes

| # | Règle | Formulation exacte du client | Sûre ? |
|---|---|---|---|
| 1 | Une réservation peut être prise dès 2 personnes, mais la sortie n'est maintenue qu'à partir de 6 personnes inscrites sur le créneau | « min de personnes = Au moins 2 personnes sur la résa » / « min 6 personnes sinon sortie annulé » | à confirmer — seuil de déclenchement de l'annulation non précisé (voir ambiguïté 1) |
| 2 | La privatisation bloque le bateau entier sur le créneau, sans tarif préférentiel | « Bateau entier bloqué sur créneaux, (réservation privée) » / « Privatisation = pas de tarif préférentiel » | oui |
| 3 | Les forfaits 4 et 12 personnes sont équivalents en pratique | « Forfait = 4 ou 12 personnes (la même chose) » | oui |
| 4 | Les suppléments personnalisables (ex. champagne) sont hors périmètre de la réservation en ligne, vendus uniquement par téléphone | « suppléments pour champagne géré par téléphone (uniquement par tel car personnalisable) » | oui |
| 5 | La saison détermine la nature de la sortie (baleines vs dauphins) mais pas les horaires | « Saisons sorties baleines (contraintes mais pas sur les horaires) » | oui |
| 6 | La réservation en ligne se ferme 12h avant le départ (12h la veille si départ l'après-midi ou le lendemain) | « jusqu'à 12H avant le départ […] Si le lendemain → 12H la veille » | oui |
| 7 | Le paiement est intégral, en ligne, par carte bancaire uniquement | « full carte bleu site » | oui |
| 8 | Le remboursement en cas d'annulation suit un barème dégressif selon le délai avant le départ | « + 7j = remboursement full » / « 7j à 48h = 25% de commission gardée » / « 48h à 24H = 50% de commission gardée » | oui |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « min de personnes = Au moins 2 personnes sur la résa […] mais si pas 6 personnes pas de départ » | (a) 2 personnes suffisent pour réserver et bloquer des places ; la sortie n'est confirmée qu'à 6 inscrits (b) 6 personnes est la jauge plancher en dessous de laquelle aucune réservation n'est acceptée | non |
| 2 | « modification à la discrétion de l'armateur » (taille de groupe après réservation) | (a) validation au cas par cas, sans règle formalisable dans le système (b) une règle métier reste à définir (délai, frais, seuils) pour que le back-office puisse l'appliquer | non |
| 3 | « Privatisation = […] souvent l'après-midi (sunset…) » | (a) simple tendance observée, le client peut privatiser n'importe quel créneau (b) contrainte horaire fixe réservant la privatisation à l'après-midi | non |
| 4 | « Est-ce qu'un client peut reporter sa résa même 24H avant ? » | (a) le report est une alternative distincte de l'annulation, avec ses propres règles (b) le report suit le même barème que l'annulation | non |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
|---|---|---|
| 1 | Jauge technique par bateau : 12 places (*Ti Kap*), 24 places (*Le Grand Bleu*), départ non maintenu sous 6 passagers | métier |
| 2 | Fenêtre de réservation en ligne fermée à moins de 12h du départ | métier |
| 3 | Paiement exclusivement par carte bancaire en ligne ; aucun prestataire de paiement existant, seul un TPE bancaire classique en place | technique / métier |
| 4 | Fenêtre saisonnière des sorties baleines limitée au 15 juin – 31 octobre | métier |

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.

| Priorité | Question | Pourquoi elle compte |
|---|---|---|
| 1 | Quelle est la règle exacte d'annulation faute de jauge : à quel moment le seuil de 6 personnes est-il vérifié, et que se passe-t-il pour les passagers déjà inscrits (remboursement automatique, proposition de report) ? | Sans cette règle, impossible de spécifier le cycle de vie d'un créneau (REQ liées à l'annulation automatique) |
| 2 | Comment formaliser « modification à la discrétion de l'armateur » pour les changements de taille de groupe après réservation ? | Une règle non formalisée ne peut pas être implémentée ; il faut un délai limite, des frais éventuels, un mode de validation |
| 3 | Un client peut-il reporter (et non seulement annuler) sa réservation, y compris dans la fenêtre 24h avant le départ ? | Question restée ouverte côté client lui-même ; conditionne l'existence d'une fonctionnalité de report distincte de l'annulation |
| 4 | La privatisation est-elle limitée à des créneaux précis (ex. après-midi uniquement) ou le client peut-il privatiser n'importe quel créneau du planning normal ? | Impacte directement le moteur de disponibilité des créneaux |
| 5 | Quel est le tarif « bébé », évoqué dans la question initiale mais non chiffré ? | Nécessaire pour compléter la grille tarifaire (Q07) |

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que l'entretien n'a
pas touchés. C'est là que se cachent les découvertes tardives et coûteuses.

- Les points 5 à 8 (Q14 à Q24) sont désormais posés au client, mais **sans
  réponse à ce stade** : ils lui sont envoyés par e-mail (météo/annulations
  armateur, communication/notifications, exploitation quotidienne, intégrations
  techniques). Ce compte rendu sera complété dès réception des réponses.
- Q25 à Q26, ajoutées après coup, sont sans réponse elles aussi : la
  privatisation le matin, l'âge maximum du tarif « enfant » et l'âge minimum
  accepté à bord.
- Le tarif « bébé », mentionné dans la question posée mais jamais chiffré par le
  client.
- Le traitement d'une annulation dans les dernières 24h avant le départ, et
  l'existence d'une option de report distincte de l'annulation.
- La formalisation de la règle de modification de groupe après réservation
  (au-delà de « à la discrétion de l'armateur »).
