# ADR-003 - Gestion de la concurrence sur la dernière place

**Statut :** accepté
**Date :** J5 (2026-08-14)
**Décidé par :** l'équipe (Chloe Baisse, Arnaud Maxime, Anthony Dégeilh),
sur remarque du formateur au jalon de fin de semaine 1
**S'appuie sur :** `ADR-002-persistance.md` pour le verrouillage InnoDB

---

Cette décision **inverse une remarque de revue IA marquée refusée** le
2026-08-12 dans `specs/booking.md` : « réserver temporairement les places dès
la validation du formulaire », écartée au motif du risque de places bloquées
par des paniers abandonnés. Le motif reste valable, il pèse simplement moins
lourd que le défaut découvert depuis. Le revirement est consigné au journal
de J5.

## Contexte

Deux clients peuvent consulter le même créneau, y voir la même dernière
place, et engager tous les deux une réservation (`REQ-004`,
`SPEC-BOOKING-03` AC-3). Le mécanisme écrit jusqu'ici décompte les places
**uniquement à la confirmation du paiement** (`SPEC-BOOKING-07` AC-3,
`SPEC-BOOKING-03` cas limite 9) : une réservation en attente de paiement
n'immobilise rien, et le conflit se résout à l'encaissement, dans une
transaction qui verrouille la ligne `sortie`, recompte, puis écrit.

Ce mécanisme garantit qu'une seule réservation aboutit. Il laisse en revanche
un cas sans réponse, et aucune spécification ne le traite : **que devient
l'argent du perdant ?** Le paiement est encaissé par Stripe avant que
l'application puisse contrôler la capacité. La chaîne actuelle impose donc de
débiter un client, puis de le rembourser, pour une sortie qu'il n'aura pas.
Le paiement est intégral et exigé au moment de la réservation (`REQ-017`), ce
qui rend ce cas d'autant plus sensible : il ne s'agit pas d'un acompte, mais
de la totalité du prix.

La contrainte de capacité elle-même n'est pas négociable (`REQ-002`,
`REQ-004`), et la volumétrie reste faible avec un pic saisonnier
(`REQ-100`) : la concurrence est rare, mais elle se produit précisément
quand la place est la plus disputée.

## Options envisagées

### Option A - Contrôle à l'encaissement (mécanisme écrit jusqu'ici, écartée)

| | |
|---|---|
| Ce qu'elle apporte | Aucune place n'est jamais immobilisée pour rien : la disponibilité affichée correspond exactement aux places vendues. Aucun état intermédiaire, aucune tâche de libération, modèle de données inchangé |
| Ce qu'elle coûte | Le client perdant a saisi sa carte, a été débité, et doit être remboursé pour une place qu'il n'aura jamais eue. Le gérant hérite d'un remboursement à justifier, dans un dispositif où il ne veut plus téléphoner à personne |
| Ce qu'elle rend difficile plus tard | Rien techniquement, mais chaque cas se paie en confiance client, et rien dans les spécifications n'en garde la trace |

### Option B - Pré-réservation à durée limitée (retenue)

| | |
|---|---|
| Ce qu'elle apporte | Le second client apprend que la place est prise **avant** de saisir sa carte. Aucun argent n'est encaissé pour une place qui ne sera pas obtenue. Le verrou de base ne change pas, il se prend plus tôt dans le parcours |
| Ce qu'elle coûte | Une durée de rétention, que le client n'a jamais évoquée : règle métier nouvelle, posée en hypothèse d'équipe. Un panier abandonné stérilise la dernière place pendant ce délai. Une colonne d'expiration au modèle, et une libération des pré-réservations échues |
| Ce qu'elle rend difficile plus tard | Le nombre de places affiché cesse d'être le nombre de places vendues : deux notions coexistent, disponible et immobilisé, qu'il faudra distinguer dans tout écran de gestion ultérieur |

### Option C - Autorisation bancaire puis capture différée (écartée pour cette version)

| | |
|---|---|
| Ce qu'elle apporte | Stripe autorise le montant sans le capturer ; l'application ne capture qu'une fois la place acquise, et annule l'autorisation sinon. Aucun débit, donc aucun remboursement, même dans le cas résiduel de l'option B |
| Ce qu'elle coûte | Le client voit une empreinte sur sa carte, ce qui demande une explication dans le parcours. L'intégration Stripe se complique (deux appels au lieu d'un, gestion des autorisations expirées) et sort du périmètre outillé par `ADR-001` |
| Ce qu'elle rend difficile plus tard | Rien : c'est un complément naturel de l'option B, activable sans rien défaire |

## Décision

Une place est **immobilisée dès la validation du formulaire** pour une durée
de **15 minutes**, sous le même verrou de ligne que celui décrit dans
`architecture.md` §5 ; la pré-réservation devient une réservation confirmée
au paiement, et se libère automatiquement à expiration.

## Raisons

Le seul défaut sérieux de l'option A est aussi le plus coûteux pour le
client : encaisser la totalité d'une sortie, puis rembourser. L'option B
déplace le refus avant la saisie de la carte, ce qui est la seule différence
qui compte du point de vue de la personne qui réserve.

Le prix de ce déplacement est borné et connu : une place peut rester
immobilisée quinze minutes. Ce coût ne se paie que sur les dernières places
en pleine saison, exactement les cas où le mécanisme actuel produisait un
remboursement. Ailleurs, il est invisible.

Le verrou lui-même ne change pas. `ADR-002` a retenu InnoDB pour son
verrouillage de ligne dans une transaction : c'est la même transaction, prise
au moment de la pré-réservation au lieu de l'encaissement. Aucune complexité
nouvelle de ce côté.

Quinze minutes est un compromis : assez pour un paiement sur mobile avec une
authentification forte, assez peu pour ne pas stériliser une place sur un
créneau presque complet. La valeur est une **hypothèse d'équipe**, pas une
réponse du client, et elle est reposée au §11 du cahier des charges.

L'option C est écartée pour cette version, non par désaccord mais par
séquencement : elle règle un cas résiduel, elle suppose l'option B, et elle
complique l'intégration de paiement au moment où celle-ci n'est pas encore
écrite. Elle reste la première évolution à envisager.

## Conséquences acceptées

- Une place peut rester indisponible quinze minutes sans avoir été vendue.
  Le nombre affiché au client compte donc les places immobilisées.
- La durée retenue n'est pas validée par le client : elle est marquée
  `déduit` au cahier des charges et fait l'objet d'une question ouverte.
- L'expiration est évaluée **à la lecture** : une pré-réservation échue ne
  compte plus dans les places prises, même si aucune tâche n'est encore
  passée la supprimer. Le nettoyage périodique n'est qu'un entretien, jamais
  une condition de correction.
- Un cas résiduel subsiste : la pré-réservation expire pendant que le client
  est sur la page de paiement, et le paiement aboutit ensuite. La règle est
  alors de reprendre la place si elle est encore libre, et de refuser puis
  rembourser sinon. Ce cas doit être écrit dans `SPEC-BOOKING-07`, où il
  n'existe pas aujourd'hui.
- Si l'intégration retenue est la page de paiement hébergée par Stripe, sa
  session ne peut pas expirer aussi vite qu'une rétention de quinze minutes.
  La fenêtre exacte est à vérifier au moment de l'intégration, et à traiter
  soit par la règle de reprise ci-dessus, soit en alignant la durée de
  rétention sur cette contrainte.
- Le modèle de données change : une colonne d'expiration sur `reservation`,
  et un décompte qui inclut les pré-réservations non échues.

## Ce qui nous ferait revenir dessus

- Le client juge inacceptable qu'une place puisse rester bloquée quinze
  minutes en pleine saison : réduire la durée, ou passer à l'option C.
- Le cas résiduel se produit réellement en exploitation, c'est-à-dire qu'un
  remboursement pour place perdue est constaté : l'option C devient
  prioritaire.
- Le client renonce au paiement intégral en ligne au profit d'un acompte :
  l'enjeu du remboursement change d'échelle, et l'arbitrage entier est à
  reprendre.
