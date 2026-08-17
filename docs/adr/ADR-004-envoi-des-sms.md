# ADR-004 - Choix de la plateforme d'envoi des SMS

**Statut :** accepté
**Date :** J6 (2026-08-17)
**Décidé par :** l'équipe (Chloe Baisse, Arnaud Maxime, Anthony Dégeilh)
**Complète :** `ADR-001-stack.md` pour l'intégration des services externes

---

Cet ADR avait été déclaré **sans objet** le 2026-08-14, le client ayant
répondu qu'il conservait « le forfait et le numéro actuels de l'entreprise »
(`CR-05/Q21`). Cette réponse portait sur son abonnement, pas sur le mode
d'envoi. L'équipe a tranché le 2026-08-17 : les messages ne partiront pas de
son téléphone, et une plateforme d'envoi devient donc nécessaire. La
déclaration « sans objet » est corrigée dans `docs/traceability-trous.md` et
la décision est consignée au journal de J6.

## Contexte

Depuis la v5 du cahier des charges, l'application envoie **trois messages
automatiques**, systématiquement par SMS **et** par e-mail (`REQ-057`) :

- le rappel avant la sortie (`REQ-025`) ;
- l'alerte météo, la veille à 18h (`REQ-053`) ;
- la confirmation d'annulation, 2 heures avant le départ (`REQ-054`).

Deux éléments pèsent lourd. D'abord, **le gérant ne téléphone plus**
(`REQ-026`) : l'écrit est devenu le seul canal d'annonce, et un message perdu
n'est plus rattrapé par personne. Ensuite, le client a répondu « budget
illimité » pour l'exercice (`CR-05/Q05`) : **le prix n'est plus un critère de
choix**, contrairement à l'hébergement retenu en `ADR-001`.

Deux contraintes encadrent la décision : le numéro de mobile est une donnée
personnelle transmise à un tiers, ce qui engage le RGPD (`REQ-105`), et
l'exploitation se fait depuis un territoire dont le plan de numérotation doit
être couvert par la plateforme.

## Options envisagées

### Option A - Envoi depuis le téléphone du gérant (écartée)

| | |
|---|---|
| Ce qu'elle apporte | aucun prestataire, aucun compte, aucune donnée transmise à un tiers ; le numéro affiché est celui que les clients connaissent déjà |
| Ce qu'elle coûte | le volume dépasse ce qu'un téléphone de poche encaisse en saison, et l'envoi dépend de la couverture réseau et de la batterie de l'appareil. Surtout, une application hébergée ne sait pas déclencher un envoi depuis un mobile : le gérant redeviendrait l'expéditeur manuel, exactement ce qu'il veut arrêter |
| Ce qu'elle rend difficile plus tard | tout : l'automatisation demandée en `CR-05/Q01` devient impossible |

### Option B - Plateforme française multicanal, SMS et e-mail (retenue)

| | |
|---|---|
| Ce qu'elle apporte | **un seul prestataire pour les deux canaux exigés par `REQ-057`**, donc une seule intégration, une seule clé, et un seul endroit où lire l'état d'un envoi pour alimenter la trace de `SPEC-CANCEL-04` AC-6. Éditeur français, hébergement et sous-traitance dans l'Union, ce qui simplifie l'analyse RGPD. Expéditeur affiché au nom de l'entreprise |
| Ce qu'elle coûte | un point de défaillance commun : si la plateforme tombe, ni SMS ni e-mail ne partent. Le numéro de mobile et l'adresse de chaque client sont transmis à un sous-traitant, ce qui impose un contrat de sous-traitance et une mention dans les informations légales |
| Ce qu'elle rend difficile plus tard | changer de fournisseur touche les deux canaux à la fois |

### Option C - Plateforme spécialisée SMS, e-mail conservé sur le SMTP de l'hébergement

| | |
|---|---|
| Ce qu'elle apporte | les deux canaux tombent indépendamment, ce qui sert directement `SPEC-CANCEL-05` AC-6, l'échec d'un canal n'empêchant pas l'autre |
| Ce qu'elle coûte | deux intégrations, deux formats de retour, deux endroits où lire un échec d'envoi. Le SMTP d'un hébergement mutualisé a par ailleurs une réputation de délivrabilité faible, et le client craint précisément que ses e-mails finissent en indésirables (`CR-05/Q02`) |
| Ce qu'elle rend difficile plus tard | rien, mais elle double le travail d'intégration pour un bénéfice que l'option B peut obtenir autrement, voir les conséquences |

### Option D - Plateforme non européenne de référence (écartée)

| | |
|---|---|
| Ce qu'elle apporte | la simplicité d'intégration la plus reconnue du marché, une documentation abondante |
| Ce qu'elle coûte | éditeur hors Union européenne : le transfert de données personnelles suppose un cadre juridique supplémentaire à instruire, pour un projet qui ne traite que quelques dizaines de clients par semaine |
| Ce qu'elle rend difficile plus tard | rien techniquement, mais l'équipe s'écarte du critère posé, une plateforme française |

## Décision

Une **plateforme française d'envoi multicanal, SMS et e-mail**, retenue sur
deux critères posés par l'équipe : la simplicité d'intégration et l'adoption
du marché. **Brevo** est le candidat pressenti, éditeur français proposant les
deux canaux derrière une seule API et un SDK PHP officiel.

Le nom exact reste à confirmer à l'ouverture du compte, sur trois
vérifications qui ne peuvent pas se faire depuis le dépôt :

1. la couverture du plan de numérotation du territoire d'exploitation ;
2. la possibilité d'un expéditeur alphanumérique au nom de l'entreprise ;
3. la disponibilité d'un contrat de sous-traitance conforme au RGPD.

Si l'une des trois manque, l'option C reprend la main.

## Raisons

Le critère décisif n'est pas le SMS, c'est le fait que `REQ-057` impose
**deux canaux systématiques**. Un prestataire qui couvre les deux divise par
deux le travail d'intégration et, surtout, unifie la trace des envois, qui est
un critère d'acceptation à part entière depuis que le gérant ne téléphone
plus.

Le prix, qui avait fondé le choix d'hébergement en `ADR-001`, ne joue plus
aucun rôle ici : le client a répondu « budget illimité » pour l'exercice. Ce
qui reste, c'est la fiabilité de délivrance et la simplicité, deux critères
que l'équipe assume comme subjectifs et qu'elle a explicitement préférés au
coût.

L'option A est écartée sur un fait technique, pas sur une préférence : une
application hébergée ne déclenche pas l'envoi d'un SMS depuis un téléphone
personnel. La retenir reviendrait à annuler l'automatisation demandée.

L'option D est écartée sur le seul critère que l'équipe s'était donné, une
plateforme française, alors même qu'elle est probablement la plus simple à
intégrer. C'est un arbitrage assumé.

## Conséquences acceptées

- **Point de défaillance commun.** Une panne de la plateforme prive
  l'application de ses deux canaux. `SPEC-CANCEL-05` AC-6 exige seulement que
  l'échec d'un canal n'emporte pas l'autre, ce qui reste vrai pour un échec
  unitaire, un numéro invalide par exemple, mais pas pour une panne globale.
  Cette limite doit figurer dans `architecture.md` §6 et §9.
- **Un sous-traitant de plus au titre du RGPD.** Le numéro de mobile et
  l'adresse e-mail de chaque client lui sont transmis. Un contrat de
  sous-traitance est requis, et les informations légales du site doivent le
  mentionner. `SPEC-NFR-04` est à compléter en conséquence.
- **Un expéditeur alphanumérique ne reçoit pas de réponse.** Un client qui
  répond au SMS d'annulation écrit dans le vide. C'est compatible avec ce que
  le client a décidé, l'appel entrant restant possible vers le gérant, mais
  le message doit le dire explicitement, et cela pèse sur le texte encore à
  fournir.
- **Une clé d'API devient un secret d'exploitation**, à tenir hors du code
  et hors du dépôt, en configuration d'environnement.
- **Un second coût récurrent** apparaît pour le client. Il n'est plus un
  critère de choix, mais il reste à documenter : c'est exactement ce que
  vérifie `CASE-NFR-06`.

## Ce qui nous ferait revenir dessus

- L'une des trois vérifications d'ouverture de compte échoue, en particulier
  la couverture du plan de numérotation du territoire d'exploitation.
- Un taux de non-délivrance constaté en exploitation : le client a placé la
  non-délivrance sous la responsabilité de celui qui saisit ses coordonnées,
  mais un échec massif viendrait de la plateforme, pas des clients.
- Le client revient sur le double canal systématique et n'en garde qu'un :
  l'argument principal de l'option B tombe, et l'option C redevient la plus
  simple.
