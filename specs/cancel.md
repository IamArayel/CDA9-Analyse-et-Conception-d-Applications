# Spécifications - CANCEL (annulation météo, à l'initiative du gérant)

**Domaine :** `CANCEL`
**Source :** `docs/cahier-des-charges.md` (v3), cas d'usage Must have
« annuler un créneau météo et informer les clients concernés », complété par
`docs/compte-rendu-entretien-03.md` (CR-03) et `docs/impact-CR-001.md`.
**Gabarit :** `docs/cle-specification.md` ; chaque spécification en reprend
les sept rubriques, dans le même ordre.

L'annulation d'un **créneau** pour raison météo est décidée par le gérant.
Elle ne se confond pas avec l'annulation ou le report d'**une réservation
individuelle** à l'initiative du client, hors périmètre applicatif, ni avec
l'annulation automatique d'une sortie qui n'atteint pas 6 inscrits, spécifiée
dans le domaine BOOKING.

**Convention de traçabilité.** Les exigences couvertes sont listées une par
ligne, immédiatement sous le titre de la spécification. `tools/traceability.sh`
rattache une exigence au dernier identifiant `SPEC-xxx-nn` rencontré au-dessus
d'elle et ne retient que la première de chaque ligne : aucune exigence n'est
donc citée ailleurs que dans cette liste, et les renvois vers une autre
spécification n'apparaissent qu'après elle.

**Consigne de revue IA**, utilisée pour la rubrique « Revue IA » de chaque
spécification de ce fichier :

> Analyse cette spécification. Recherche les ambiguïtés, contradictions,
> comportements non définis, cas limites oubliés et exigences impossibles à
> tester. Ne réécris pas la spécification.

Les remarques refusées sont aussi reportées dans `docs/journal.md`.

---

## Hors périmètre applicatif du domaine

Placé avant la première spécification, pour que la matrice de traçabilité ne
rattache pas mécaniquement ces exigences à la dernière spécification du
fichier. Elles existent au cahier des charges mais ne donnent lieu à aucune
fonctionnalité dans cette version ; elles sont citées ici pour que la chaîne
de traçabilité ne les signale pas comme non couvertes.

- `REQ-019` : l'annulation d'une réservation à l'initiative du client se fait
  exclusivement par téléphone avec le gérant. Le barème dégressif (100 %
  au-delà de 7 jours, 25 % de commission entre 7 jours et 48 heures, 50 %
  entre 48 heures et 24 heures) reste une règle métier appliquée à la main
  par le gérant, hors de toute automatisation dans cette version.
- `REQ-020` : le report d'une réservation à l'initiative du client se
  négocie de la même façon, par téléphone. Aucune fonctionnalité « annuler
  ou reporter ma réservation » n'existe côté client.
- `REQ-027` (Should) : WhatsApp reste un canal de secours utilisé
  manuellement par le gérant ; aucune intégration technique n'est prévue.

---

## SPEC-CANCEL-01 - Visualisation des clients inscrits avant décision

**Exigences :**

- `REQ-022` : visualisation de la situation du créneau avant toute annulation.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le gérant peut consulter, pour tout créneau à venir, la liste des clients
inscrits avant de décider d'une annulation.

> Sur le créneau de 10h du 20 juillet, le gérant voit la liste des clients
> inscrits, leur contact et leur nombre de participants, avant même
> d'envisager d'annuler.

### Portée

Couvre la consultation d'un créneau et de ses inscrits. Ne couvre ni la
décision d'annuler, ni le contact des clients.

- Ne couvre pas la décision d'annulation elle-même : `SPEC-CANCEL-02`.
- Ne couvre pas le contact des clients ni l'enregistrement de leur choix :
  `SPEC-CANCEL-04`.
- Ne couvre pas l'export imprimable du planning : `SPEC-ADMIN-03`.
- Les données affichées se limitent à celles collectées à la réservation,
  cf. `SPEC-NFR-04`.

### Scénarios nominaux

```gherkin
Étant donné un créneau à venir portant trois réservations payées
Quand le gérant consulte ce créneau depuis l'espace de gestion
Alors la liste des clients inscrits s'affiche
Et chaque ligne porte le nom, le contact et le nombre de participants
Et aucune décision d'annulation n'a été demandée
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | créneau sans aucun inscrit | liste vide affichée, et le créneau reste annulable |
| 2 | réservation en attente de paiement sur ce créneau | non listée : elle ne réserve aucune place |
| 3 | créneau privatisé | une seule ligne, correspondant au client ayant privatisé le bateau |
| 4 | créneau déjà passé | consultable en lecture, sans possibilité d'annulation |
| 5 | deux bateaux engagés sur le même créneau | les inscrits sont présentés par bateau |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Consultation depuis un téléphone en situation d'urgence météo : le client
  décrit un usage sur ordinateur. Hypothèse retenue : l'espace de gestion
  reste conçu pour un écran d'ordinateur, sans engagement mobile.
- Historique des consultations : non demandé, aucune trace conservée.

### Critères d'acceptation

- [ ] AC-1 : la consultation d'un créneau à venir affiche la liste de ses
      clients inscrits, avec nom, contact et nombre de participants.
- [ ] AC-2 : cette consultation ne déclenche aucune annulation.
- [ ] AC-3 : un créneau sans inscrit affiche une liste vide et reste
      annulable.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Rien ne garantissait que la consultation soit sans effet de bord | acceptée | AC-2 ajouté : la consultation est explicitement neutre |
| Le cas d'un créneau vide n'était pas traité alors qu'il est fréquent hors saison | acceptée | cas limite 1 et AC-3 ajoutés |
| Les réservations non payées pourraient laisser croire à des inscrits à contacter | acceptée | cas limite 2 ajouté |
| Afficher aussi l'historique des annulations passées du créneau | refusée | non demandé par le client, et sans effet sur la décision météo qui se prend sur la situation du jour |

## SPEC-CANCEL-02 - Annulation météo décidée manuellement par le gérant

**Exigences :**

- `REQ-021` : la décision appartient au gérant, jamais déclenchée automatiquement.
- `REQ-022` : l'annulation n'a lieu qu'après consultation du créneau.

**Statut :** revue IA faite
**Version :** v1

### Règle

Un créneau ne passe à l'état annulé pour raison météo que sur décision
explicite du gérant, prise après consultation de la situation du créneau.

> Aucune règle météo automatisée n'existe dans l'application : sans action du
> gérant, un créneau reste ouvert quelles que soient les prévisions.

### Portée

Couvre l'annulation d'un créneau pour raison météo. Ne couvre ni les
annulations d'origine différente, ni le traitement des clients concernés.

- Ne couvre pas l'annulation automatique d'une sortie n'atteignant pas
  6 inscrits, qui est le seul cas automatisé : `SPEC-BOOKING-03`.
- Ne couvre pas la répercussion côté client : `SPEC-CANCEL-03`.
- Ne couvre pas le contact des clients et leur choix : `SPEC-CANCEL-04`.
- Ne couvre pas la fermeture programmée d'une journée entière :
  `SPEC-ADMIN-04`.

### Scénarios nominaux

```gherkin
Étant donné un créneau à venir consulté par le gérant
Quand le gérant décide de l'annuler pour raison météo
Alors le créneau passe à l'état « annulé »
Et il n'apparaît plus disponible côté client
Et la liste des clients à contacter est constituée
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | prévisions météo dégradées, sans action du gérant | aucune annulation : rien n'est automatisé |
| 2 | créneau déjà annulé | l'action est sans effet, l'état reste « annulé » |
| 3 | créneau déjà passé | annulation refusée |
| 4 | créneau déjà annulé faute d'atteindre 6 inscrits | annulation météo sans objet, l'état d'annulation est unique |
| 5 | deux bateaux engagés sur le créneau | l'annulation porte sur le créneau entier, voir la rubrique suivante |
| 6 | météo redevenue favorable après l'annulation | pas de retour en arrière : les clients ont déjà été contactés |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Annulation d'un seul bateau sur un créneau qui en engage deux : le client
  n'a jamais évoqué ce découpage. Hypothèse retenue : l'annulation porte sur
  le créneau entier, tous bateaux confondus.
- Motif enregistré avec l'annulation : non demandé. Hypothèse retenue : la
  raison météo est le seul motif prévu, sans champ de commentaire.
- Désannulation d'un créneau : non prévue, l'information ayant déjà été
  transmise par téléphone aux clients.

### Critères d'acceptation

- [ ] AC-1 : un créneau consulté puis annulé par le gérant passe à l'état
      « annulé ».
- [ ] AC-2 : aucun créneau ne passe à l'état « annulé » pour raison météo
      sans action explicite du gérant.
- [ ] AC-3 : l'annulation d'un créneau déjà annulé n'a aucun effet et ne
      produit pas d'erreur bloquante.
- [ ] AC-4 : l'annulation d'un créneau déjà passé est refusée.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| La spécification affirmait qu'aucune annulation n'est automatique, ce qui contredit l'annulation automatique au seuil de 6 inscrits | acceptée | la portée distingue désormais explicitement les deux origines d'annulation, et le cas limite 4 traite leur rencontre |
| « Un créneau non consulté ne déclenche aucune annulation » était impossible à tester tel quel | acceptée | AC-2 reformulé sur l'absence d'action du gérant, observable, plutôt que sur l'absence de consultation |
| L'annulation partielle d'un seul bateau n'était pas tranchée | acceptée | cas limite 5 et hypothèse ajoutés |
| Intégrer un service météo pour proposer les annulations au gérant | refusée | le client a explicitement demandé que la décision reste la sienne, sans automatisme ; une proposition automatique introduirait une dépendance externe et une responsabilité que le projet ne veut pas porter |

## SPEC-CANCEL-03 - Répercussion en temps réel côté client de l'annulation

**Exigences :**

- `REQ-021` : effet de la décision d'annulation du gérant.
- `REQ-004` : les places affichées au client reflètent la réalité du créneau.

**Statut :** revue IA faite
**Version :** v1

### Règle

Dès l'enregistrement de l'annulation, le créneau cesse d'être proposé et
réservable côté client, sans redéploiement ni action technique.

> Un client qui consulte les créneaux après l'annulation ne voit plus le
> créneau annulé ; celui qui l'avait déjà à l'écran voit son affichage se
> mettre à jour.

### Portée

Couvre l'effet immédiat de l'annulation sur l'affichage et sur la
réservabilité. Ne couvre ni la décision, ni l'information individuelle des
clients déjà inscrits.

- Ne couvre pas la décision d'annulation : `SPEC-CANCEL-02`.
- Ne couvre pas le contact téléphonique des clients inscrits :
  `SPEC-CANCEL-04`.
- Ne couvre pas la mise à jour du nombre de places après une réservation,
  qui suit le même mécanisme : `SPEC-BOOKING-03`.

### Scénarios nominaux

```gherkin
Étant donné un client consultant les places disponibles d'un créneau
Quand le gérant annule ce créneau pour raison météo
Alors l'affichage du client se met à jour sans rechargement manuel
Et le créneau y apparaît indisponible
Et toute tentative de réservation sur ce créneau est refusée
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | client en cours de saisie du formulaire sur ce créneau | la réservation est refusée à la validation, avec le motif d'annulation du créneau |
| 2 | client déjà engagé dans le tunnel de paiement | le paiement est interrompu et le client n'est pas débité |
| 3 | page laissée ouverte plusieurs heures avant l'annulation | l'affichage se met à jour, ou la réservation est refusée à la validation |
| 4 | client hors ligne au moment de l'annulation | l'indisponibilité est constatée au retour de la connexion |
| 5 | créneau annulé puis journée entière déclarée fermée | aucun conflit : le créneau reste indisponible |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Message affiché au client sur un créneau annulé : le client n'a pas
  précisé s'il souhaite afficher « annulé » ou simplement masquer le
  créneau. Hypothèse retenue : le créneau est masqué de la liste des
  créneaux réservables, et une tentative directe reçoit un motif explicite.
- Délai maximal de propagation de la mise à jour : non discuté. Hypothèse
  retenue : quelques secondes, sans engagement contractuel.

### Critères d'acceptation

- [ ] AC-1 : après annulation, le créneau n'est plus proposé à la
      réservation, sans action technique.
- [ ] AC-2 : l'affichage d'un client déjà présent sur la page se met à jour
      sans rechargement manuel.
- [ ] AC-3 : une réservation validée sur un créneau annulé est refusée.
- [ ] AC-4 : un client engagé dans le paiement d'un créneau annulé n'est pas
      débité.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le cas d'un client déjà dans le tunnel de paiement au moment de l'annulation n'était pas traité | acceptée | cas limite 2 et AC-4 ajoutés : le client ne doit pas être débité pour une sortie annulée |
| « Sans redéploiement ni action technique manuelle » décrit une contrainte d'exploitation, pas un comportement observable | acceptée | AC-1 et AC-2 formulés sur ce que voit le client |
| Le comportement attendu d'un créneau annulé, masqué ou affiché comme annulé, n'était pas tranché | acceptée | hypothèse écrite dans « Ce qui n'est pas défini » |
| Notifier automatiquement par e-mail les clients inscrits au moment de l'annulation | refusée | le client a posé le téléphone comme canal d'annonce et veut garder la main sur ce contact ; un e-mail automatique le doublerait et brouillerait le message |

## SPEC-CANCEL-04 - Contact et enregistrement du choix de chaque client

**Exigences :**

- `REQ-023` : contact téléphonique de chaque client, choix enregistré.
- `REQ-024` : proposition de report tenant compte des disponibilités et de la météo.
- `REQ-026` : information des clients par téléphone.

**Statut :** revue IA faite
**Version :** v1

### Règle

Pour chaque client inscrit sur un créneau annulé, le gérant enregistre dans
l'espace de gestion le choix retenu par téléphone : report, avoir ou
remboursement.

> L'application ne décide rien et n'envoie rien : elle garde la trace du
> choix convenu au téléphone, client par client.

### Portée

Couvre l'enregistrement du choix de chaque client après une annulation
météo. Ne couvre ni la conversation téléphonique, ni la mécanique de l'avoir,
ni le barème d'une annulation à l'initiative du client.

- Ne couvre pas l'usage du code d'avoir au paiement d'une réservation
  future : `SPEC-BOOKING-10`.
- Ne couvre pas la disponibilité du créneau de remplacement :
  `SPEC-BOOKING-03`.
- Ne couvre pas le barème dégressif d'une annulation demandée par le client,
  hors périmètre, cf. la portée du domaine en tête de fichier.
- Ne couvre pas le remboursement automatique d'une sortie annulée faute de
  6 inscrits : `SPEC-BOOKING-03`.

Le remboursement consécutif à une annulation météo est **intégral** :
l'annulation est à l'initiative de l'entreprise, le barème dégressif ne
s'applique pas.

### Scénarios nominaux

```gherkin
Étant donné un créneau annulé portant trois clients inscrits
Quand le gérant appelle le premier client et convient d'un report
Alors il enregistre « report » pour ce client
Quand le deuxième client préfère un avoir
Alors il enregistre « avoir » et un code d'avoir est délivré
Quand le troisième client demande un remboursement
Alors il enregistre « remboursement » et le montant remboursé est intégral
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | client injoignable | le choix reste « en attente » et le client apparaît comme restant à contacter |
| 2 | client refusant la première proposition de report | accord de gré à gré par téléphone, aucune procédure automatisée de désaccord |
| 3 | report demandé vers un créneau complet | refusé au titre de la capacité, une autre date est proposée |
| 4 | report demandé vers un créneau dont le tarif a changé | aucun complément n'est demandé au client |
| 5 | réservation payée en partie ou en totalité avec un bon cadeau | cas non tranché, voir la rubrique suivante |
| 6 | créneau de report annulé à son tour | le cycle recommence, un nouveau choix est enregistré |
| 7 | choix modifié après enregistrement | le gérant peut le corriger tant que le remboursement n'est pas exécuté |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Sort d'une réservation réglée avec un bon cadeau : hypothèse retenue, le
  gérant délivre un code d'avoir d'un montant équivalent plutôt qu'un
  remboursement, un bon cadeau n'étant pas un moyen de paiement
  remboursable.
- Délai au-delà duquel un client injoignable est traité par défaut : non
  discuté. Hypothèse retenue : aucun traitement par défaut, le choix reste
  en attente jusqu'au contact.
- Exécution technique du remboursement : hypothèse retenue, opération
  déclenchée par le gérant auprès du prestataire de paiement, l'application
  n'en gardant que la trace.

### Critères d'acceptation

- [ ] AC-1 : pour chaque client d'un créneau annulé, le gérant peut
      enregistrer report, avoir ou remboursement.
- [ ] AC-2 : tant qu'aucun choix n'est enregistré, le client apparaît comme
      restant à contacter.
- [ ] AC-3 : le montant d'un remboursement consécutif à une annulation météo
      est intégral.
- [ ] AC-4 : l'enregistrement d'un avoir produit un code utilisable sur une
      réservation future.
- [ ] AC-5 : un report vers un créneau sans place disponible est refusé.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le montant du remboursement après annulation météo n'était écrit nulle part, alors qu'un barème dégressif existe pour les annulations client | acceptée | règle du remboursement intégral écrite dans la portée et AC-3 ajouté ; c'est la contradiction la plus coûteuse du domaine |
| Le suivi des clients injoignables n'existait pas, alors que le gérant appelle dans l'urgence | acceptée | cas limite 1 et AC-2 ajoutés |
| Un report vers un créneau complet n'était pas traité | acceptée | cas limite 3 et AC-5 ajoutés |
| Une réservation payée par bon cadeau n'a pas de règle de compensation | acceptée | cas limite 5 et hypothèse de l'avoir équivalent ajoutés, cohérents avec la spécification du bon cadeau |
| Automatiser l'envoi des remboursements dès l'enregistrement du choix | refusée | le client veut garder la main sur des remboursements qu'il négocie au téléphone, et aucune règle de déclenchement automatique n'a été validée |

## SPEC-CANCEL-05 - Message de rappel automatisé avant la sortie

**Exigences :**

- `REQ-025` : message type envoyé automatiquement, par défaut 24 heures avant.
- `REQ-042` : horaire d'envoi personnalisable par le gérant.

**Statut :** revue IA faite
**Version :** v1

### Règle

Chaque client dont la réservation est confirmée reçoit automatiquement, à
l'horaire configuré et par défaut 24 heures avant le départ, un message type
indiquant les conditions météo prévues et les affaires à prévoir.

> Le gérant n'a aucune action à faire : l'envoi est déclenché par le site à
> l'horaire qu'il a configuré une fois pour toutes.

### Portée

Couvre l'envoi automatique du message de rappel et le réglage de son horaire.
Ne couvre ni les appels d'annulation, ni la rédaction du contenu au cas par
cas.

- Ne couvre pas l'information d'une annulation, qui passe par téléphone :
  `SPEC-CANCEL-04`.
- Ne couvre pas la langue du message, traitée comme exigence transverse :
  `SPEC-NFR-02`.
- Ne couvre pas WhatsApp comme canal, hors périmètre, cf. la portée du
  domaine en tête de fichier.
- Ne couvre pas le contrôle du seuil de 6 inscrits, déclenché lui aussi à
  24 heures du départ : `SPEC-BOOKING-03`.

### Scénarios nominaux

```gherkin
Étant donné une réservation confirmée pour une sortie le 20 juillet à 10h00
Et un horaire d'envoi configuré à 24 heures avant le départ
Quand le 19 juillet à 10h00 est atteint
Alors le message type est envoyé au client
Et il contient les conditions météo prévues et les affaires à prévoir
Et le gérant n'a effectué aucune action
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | réservation confirmée après l'horaire d'envoi, par exemple la veille à 11h pour un départ à 7h | le message est envoyé dès la confirmation |
| 2 | créneau annulé avant l'horaire d'envoi | aucun message de rappel n'est envoyé |
| 3 | horaire d'envoi modifié par le gérant | les envois à venir suivent le nouvel horaire, ceux déjà partis ne sont pas rejoués |
| 4 | échec d'envoi, par exemple adresse invalide | l'échec est signalé au gérant dans l'espace de gestion |
| 5 | plusieurs réservations d'un même client sur un même créneau | un message par réservation |
| 6 | client ayant choisi l'anglais à la réservation | message envoyé en anglais |
| 7 | conditions météo prévues | voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Canal d'envoi : le client parle d'un « message » sans dire e-mail ou SMS,
  alors que le formulaire collecte les deux coordonnées. Hypothèse retenue :
  e-mail, seul canal sans coût par envoi, à confirmer.
- Source des conditions météo annoncées : aucun service météo n'a été
  évoqué. Hypothèse retenue : le gérant renseigne la prévision du jour dans
  l'espace de gestion, l'application n'interroge aucun service externe.
- Contenu exact du texte type et liste des affaires à prévoir : à obtenir du
  client avant la mise en production.

### Critères d'acceptation

- [ ] AC-1 : à l'horaire configuré, le message type est envoyé
      automatiquement à chaque client dont la réservation est confirmée, sans
      action du gérant.
- [ ] AC-2 : la valeur par défaut de l'horaire d'envoi est 24 heures avant le
      départ.
- [ ] AC-3 : une modification de l'horaire d'envoi s'applique aux envois à
      venir.
- [ ] AC-4 : aucun message n'est envoyé pour un créneau annulé.
- [ ] AC-5 : une réservation confirmée après l'horaire d'envoi déclenche
      l'envoi immédiatement.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Une réservation confirmée après l'horaire d'envoi ne recevrait jamais de rappel | acceptée | cas limite 1 et AC-5 ajoutés ; le cas est fréquent, les réservations de dernière minute étant permises jusqu'à midi la veille |
| Le canal d'envoi n'est jamais nommé alors que deux coordonnées sont collectées | acceptée | hypothèse de l'e-mail écrite et signalée à confirmer |
| « Conditions météo prévues » suppose une source de données que le projet n'a pas | acceptée | hypothèse de la saisie par le gérant écrite, ce qui évite d'engager une intégration météo non budgétée |
| Le rappel devrait aussi être envoyé par SMS pour les départs de 7h | refusée | coût par envoi non validé par le client, et budget total encore ouvert ; le point est reposé avec la question du canal |
