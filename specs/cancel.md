# Spécifications - CANCEL (alerte et annulation météo, à l'initiative du gérant)

**Domaine :** `CANCEL`
**Source :** `docs/cahier-des-charges.md` (v5), cas d'usage Must have
« annuler un créneau météo et informer les clients concernés », complété par
`docs/compte-rendu-entretien-03.md` (CR-03) et `docs/impact-CR-001.md`, puis
par `docs/compte-rendu-entretien-05.md` (CR-05) et `docs/impact-CR-003.md`
pour l'alerte préventive et le passage de l'annonce à l'écrit.
**Gabarit :** `docs/cle-specification.md` ; chaque spécification en reprend
les sept rubriques, dans le même ordre.

Un créneau connaît trois états : **programmé**, **en alerte** quand le
gérant le juge menacé sans avoir tranché, et **annulé**. L'annulation d'un
créneau ne se confond ni avec l'annulation d'une réservation à l'initiative
du client, spécifiée dans le domaine ADMIN, ni avec l'annulation automatique
d'une sortie qui n'atteint pas 6 inscrits, spécifiée dans le domaine
BOOKING.

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
fonctionnalité dans ce domaine ; elles sont citées ici pour que la chaîne de
traçabilité ne les signale pas comme non couvertes.

- `REQ-024` (Won't depuis la v5) : aucun report n'est proposé à la suite
  d'une annulation météo, celle-ci donnant lieu à un remboursement intégral.
  L'exigence a été inversée plutôt que supprimée, pour que la correction
  reste lisible ; elle ne donne donc lieu à aucune fonctionnalité.
- `REQ-020` : le report d'une réservation à l'initiative du client se négocie
  par téléphone avec le gérant. Aucune fonctionnalité « annuler ou reporter
  ma réservation » n'existe côté client. L'enregistrement de l'issue par le
  gérant relève du domaine ADMIN.
- `REQ-027` (Should) : WhatsApp reste un canal de secours utilisé
  manuellement par le gérant ; aucune intégration technique n'est prévue,
  ce que le client a reconfirmé en v5.

---

## SPEC-CANCEL-01 - Visualisation des clients inscrits avant décision

**Exigences :**

- `REQ-022` : visualisation de la situation du créneau avant toute décision.

**Statut :** revue IA faite
**Version :** v2

### Règle

Le gérant peut consulter, pour tout créneau à venir, la liste des clients
inscrits avant de le mettre en alerte ou de l'annuler.

> Sur le créneau de 10h du 20 juillet, le gérant voit la liste des clients
> inscrits, leur contact et leur nombre de participants, avant même
> d'envisager une alerte.

### Portée

Couvre la consultation d'un créneau et de ses inscrits. Ne couvre aucune des
décisions qui peuvent la suivre.

- Ne couvre pas la mise en alerte : `SPEC-CANCEL-06`.
- Ne couvre pas la décision d'annulation : `SPEC-CANCEL-02`.
- Ne couvre pas l'information des clients ni leur remboursement :
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
Et aucune décision d'alerte ni d'annulation n'a été prise
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | créneau sans aucun inscrit | liste vide affichée, et le créneau reste annulable |
| 2 | réservation immobilisée mais non payée | non listée : le client n'a pas payé, même si ses places sont retenues jusqu'à expiration, cf. `SPEC-BOOKING-03` |
| 3 | créneau privatisé | une seule ligne, correspondant au client ayant privatisé le bateau |
| 4 | créneau déjà passé | consultable en lecture, sans possibilité d'alerte ni d'annulation |
| 5 | deux bateaux engagés sur le même créneau | les inscrits sont présentés par bateau |
| 6 | créneau déjà en alerte | la liste indique en outre la date d'envoi de l'alerte |

### Ce qui n'est pas défini

Assumé au 2026-08-12, revu au 2026-08-14.

- Consultation depuis un téléphone en situation d'urgence météo : le client
  décrit un usage sur ordinateur. Hypothèse retenue : l'espace de gestion
  reste conçu pour un écran d'ordinateur, sans engagement mobile. La
  suppression de l'appel téléphonique rend cette hypothèse plus sensible,
  le gérant décidant désormais depuis son bureau uniquement.
- Historique des consultations : non demandé, aucune trace conservée.

### Critères d'acceptation

- [ ] AC-1 : la consultation d'un créneau à venir affiche la liste de ses
      clients inscrits, avec nom, contact et nombre de participants.
- [ ] AC-2 : cette consultation ne déclenche ni alerte ni annulation.
- [ ] AC-3 : un créneau sans inscrit affiche une liste vide et reste
      annulable.
- [ ] AC-4 : un créneau en alerte affiche la date d'envoi de son alerte.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Rien ne garantissait que la consultation soit sans effet de bord | acceptée | AC-2 ajouté : la consultation est explicitement neutre |
| Le cas d'un créneau vide n'était pas traité alors qu'il est fréquent hors saison | acceptée | cas limite 1 et AC-3 ajoutés |
| Depuis la v5, le gérant doit savoir si une alerte a déjà été envoyée avant d'en décider une seconde | acceptée | cas limite 6 et AC-4 ajoutés |
| Afficher aussi l'historique des annulations passées du créneau | refusée | non demandé par le client, et sans effet sur la décision météo qui se prend sur la situation du jour |

## SPEC-CANCEL-02 - Annulation d'un créneau décidée par le gérant

**Exigences :**

- `REQ-021` : la décision appartient au gérant, jamais déclenchée automatiquement.

**Statut :** revue IA faite
**Version :** v2

### Règle

Un créneau ne passe à l'état annulé pour raison météo que sur décision
explicite du gérant, qu'il ait été mis en alerte auparavant ou non.

> Aucune règle météo automatisée n'existe dans l'application : sans action du
> gérant, un créneau reste ouvert quelles que soient les prévisions, et une
> alerte laissée sans suite vaut maintien de la sortie.

### Portée

Couvre le passage d'un créneau à l'état annulé. Ne couvre ni l'alerte qui
peut le précéder, ni le traitement des clients qui le suit.

- Ne couvre pas la mise en alerte ni les messages qu'elle déclenche :
  `SPEC-CANCEL-06`.
- Ne couvre pas l'information et le remboursement des clients :
  `SPEC-CANCEL-04`.
- Ne couvre pas la répercussion côté client : `SPEC-CANCEL-03`.
- Ne couvre pas l'annulation automatique d'une sortie n'atteignant pas
  6 inscrits, seul cas automatisé de l'application : `SPEC-BOOKING-03`.
- Ne couvre pas la fermeture programmée d'une journée entière :
  `SPEC-ADMIN-04`.

### Scénarios nominaux

```gherkin
Étant donné un créneau à venir, consulté par le gérant
Quand le gérant décide de l'annuler pour raison météo
Alors le créneau passe à l'état « annulé »
Et il n'apparaît plus disponible côté client
Et les clients inscrits entrent dans le traitement prévu par SPEC-CANCEL-04
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | prévisions dégradées, sans action du gérant | aucune annulation : rien n'est automatisé |
| 2 | créneau en alerte laissé sans décision jusqu'au départ | la sortie a lieu : l'alerte seule n'annule rien |
| 3 | créneau annulé sans avoir été mis en alerte | annulation possible : l'alerte n'est pas un préalable obligatoire, et le message part alors immédiatement, cf. `SPEC-CANCEL-04` |
| 4 | créneau déjà annulé | l'action est sans effet, l'état reste « annulé » |
| 5 | créneau déjà passé | annulation refusée |
| 6 | créneau déjà annulé faute d'atteindre 6 inscrits | annulation météo sans objet, l'état d'annulation est unique |
| 7 | deux bateaux engagés sur le créneau | l'annulation porte sur le créneau entier, voir la rubrique suivante |
| 8 | météo redevenue favorable après l'annulation | pas de retour en arrière : les clients ont déjà été prévenus et remboursés |

### Ce qui n'est pas défini

Assumé au 2026-08-12, complété au 2026-08-14.

- Heure limite au-delà de laquelle le gérant ne peut plus annuler : non
  tranchée. Hypothèse retenue : annulation possible jusqu'à l'heure de
  départ. Question 12 du §11 du cahier des charges.
- Annulation d'un seul bateau sur un créneau qui en engage deux : le client
  n'a jamais évoqué ce découpage. Hypothèse retenue : l'annulation porte sur
  le créneau entier, tous bateaux confondus.
- Motif enregistré avec l'annulation : non demandé. Hypothèse retenue : la
  raison météo est le seul motif prévu, sans champ de commentaire.
- Désannulation d'un créneau : non prévue, les clients ayant déjà été
  remboursés.

### Critères d'acceptation

- [ ] AC-1 : un créneau annulé par le gérant passe à l'état « annulé ».
- [ ] AC-2 : aucun créneau ne passe à l'état « annulé » pour raison météo
      sans action explicite du gérant.
- [ ] AC-3 : un créneau en alerte non annulé avant l'heure de départ reste
      maintenu.
- [ ] AC-4 : l'annulation d'un créneau déjà annulé n'a aucun effet et ne
      produit pas d'erreur bloquante.
- [ ] AC-5 : l'annulation d'un créneau déjà passé est refusée.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| La v4 affirmait qu'aucune annulation n'est automatique, ce qui contredit l'annulation automatique au seuil de 6 inscrits | acceptée | la portée distingue les deux origines, et le cas limite 6 traite leur rencontre |
| Rien ne disait si l'alerte est un préalable obligatoire à l'annulation | acceptée | cas limite 3 : elle ne l'est pas, et le message part alors immédiatement au lieu d'être programmé |
| Une alerte laissée sans décision n'avait pas d'issue écrite | acceptée | cas limite 2 et AC-3 : la sortie a lieu, conformément au silence valant maintien |
| Intégrer un service météo pour proposer les annulations au gérant | refusée | le client a explicitement demandé que la décision reste la sienne, alerte comprise ; une proposition automatique introduirait une dépendance externe que le projet ne veut pas porter |

## SPEC-CANCEL-03 - Répercussion en temps réel côté client

**Exigences :**

- `REQ-004` : les places affichées au client reflètent la réalité du créneau.

**Statut :** revue IA faite
**Version :** v2

### Règle

Dès l'enregistrement d'une décision du gérant, le créneau reflète son
nouvel état côté client : un créneau annulé cesse d'être proposé, un créneau
en alerte reste réservable mais affiche le risque.

> Un client qui consulte les créneaux après une annulation ne voit plus le
> créneau ; après une mise en alerte, il le voit toujours, assorti de
> l'avertissement.

### Portée

Couvre l'effet immédiat des deux décisions sur l'affichage et sur la
réservabilité. Ne couvre ni les décisions elles-mêmes, ni les messages
envoyés aux clients déjà inscrits.

- Ne couvre pas la décision d'annulation : `SPEC-CANCEL-02`.
- Ne couvre pas la mise en alerte et ses messages : `SPEC-CANCEL-06`.
- Ne couvre pas l'information des clients inscrits : `SPEC-CANCEL-04`.
- Ne couvre pas la mise à jour du nombre de places après une réservation,
  qui suit le même mécanisme : `SPEC-BOOKING-03`.

### Scénarios nominaux

```gherkin
Étant donné un client consultant les places disponibles d'un créneau
Quand le gérant met ce créneau en alerte
Alors l'affichage du client signale le risque d'annulation sans rechargement manuel
Et le créneau reste réservable
Quand le gérant annule ensuite ce créneau
Alors le créneau n'est plus proposé à la réservation
Et toute tentative de réservation sur ce créneau est refusée
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | client en cours de saisie du formulaire sur un créneau annulé | la réservation est refusée à la validation, avec le motif d'annulation |
| 2 | client déjà engagé dans le paiement d'un créneau annulé | le paiement est interrompu et le client n'est pas débité |
| 3 | client en cours de saisie sur un créneau qui passe en alerte | la réservation reste possible, l'avertissement s'affiche avant la validation |
| 4 | page laissée ouverte plusieurs heures avant la décision | l'affichage se met à jour, ou la réservation est refusée à la validation |
| 5 | client hors ligne au moment de la décision | l'état réel est constaté au retour de la connexion |
| 6 | créneau annulé puis journée entière déclarée fermée | aucun conflit : le créneau reste indisponible |

### Ce qui n'est pas défini

Assumé au 2026-08-12, complété au 2026-08-14.

- Formulation exacte de l'avertissement affiché sur un créneau en alerte :
  le client a demandé que le risque soit « signalé », sans en fixer les
  mots. Hypothèse retenue : un avertissement visible avant la validation du
  formulaire, dont le texte reste à valider avec lui.
- Délai maximal de propagation de la mise à jour : non discuté. Hypothèse
  retenue : quelques secondes, sans engagement contractuel.

### Critères d'acceptation

- [ ] AC-1 : après annulation, le créneau n'est plus proposé à la
      réservation, sans action technique.
- [ ] AC-2 : après mise en alerte, le créneau reste proposé et le risque est
      affiché avant la validation du formulaire.
- [ ] AC-3 : l'affichage d'un client déjà présent sur la page se met à jour
      sans rechargement manuel.
- [ ] AC-4 : une réservation validée sur un créneau annulé est refusée.
- [ ] AC-5 : un client engagé dans le paiement d'un créneau annulé n'est pas
      débité.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le cas d'un client en cours de paiement au moment de l'annulation n'était pas traité | acceptée | cas limite 2 et AC-5 : le client ne doit pas être débité pour une sortie annulée |
| La v4 traitait un seul état, l'annulation ; l'alerte a l'effet inverse, le créneau reste vendu | acceptée | règle, scénario et AC-2 réécrits pour distinguer les deux |
| « Sans redéploiement ni action technique » décrit une contrainte d'exploitation, pas un comportement observable | acceptée | les critères portent sur ce que voit le client |
| Masquer un créneau en alerte pour éviter de vendre une sortie incertaine | refusée | le client a explicitement demandé qu'il reste réservable avec un signalement ; masquer reviendrait à annuler sans le dire |

## SPEC-CANCEL-04 - Information et remboursement des clients d'un créneau annulé

**Exigences :**

- `REQ-023` : chaque client d'un créneau annulé est prévenu et remboursé intégralement.
- `REQ-026` : les clients sont prévenus par écrit, jamais par téléphone.
- `REQ-058` : le remboursement est exécuté par le prestataire après validation du gérant.

**Statut :** revue IA faite
**Version :** v2

### Règle

Tout client dont le créneau est annulé par le gérant est prévenu par écrit et
remboursé de la totalité de ce qu'il a payé ; aucun choix ne lui est proposé.

> L'annulation vient du prestataire, pas du client : le barème dégressif ne
> s'applique pas, et il n'y a ni report ni avoir à négocier.

### Portée

Couvre l'information et le remboursement consécutifs à une annulation décidée
par le gérant. Ne couvre pas les annulations venant du client.

- Ne couvre pas l'annulation demandée par le client, avec son choix entre
  report, avoir et remboursement : `SPEC-ADMIN-06`.
- Ne couvre pas la décision d'annulation : `SPEC-CANCEL-02`.
- Ne couvre pas le message d'alerte préalable ni celui qui confirme
  l'annulation deux heures avant le départ : `SPEC-CANCEL-06`.
- Ne couvre pas l'encaissement initial : `SPEC-BOOKING-07`.

**Cette spécification a été réécrite en v5.** Jusqu'à la v4, elle décrivait
un appel téléphonique du gérant à chaque client et l'enregistrement d'un
choix entre report, avoir et remboursement. Le client a corrigé le
2026-08-14 : ce choix n'a jamais concerné l'annulation météo, et il ne veut
plus téléphoner.

### Scénarios nominaux

```gherkin
Étant donné un créneau annulé portant trois réservations payées
Quand l'annulation est enregistrée
Alors chaque client reçoit un message écrit lui annonçant l'annulation
Et le montant intégralement payé lui est remboursé
Et aucun choix entre report, avoir et remboursement ne lui est proposé
Et le gérant n'a passé aucun appel
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | réservation réglée en partie par carte et en partie par un bon cadeau | la part payée par carte est remboursée ; la part couverte par le bon donne lieu à un avoir de montant équivalent, voir la rubrique suivante |
| 2 | réservation réglée par un code d'avoir | un nouvel avoir de montant équivalent est émis, cf. `SPEC-ADMIN-06` |
| 3 | réservation immobilisée mais non payée au moment de l'annulation | rien à rembourser, la retenue est simplement libérée |
| 4 | message non délivré, numéro ou adresse erronés | le remboursement a lieu quand même ; la non-délivrance relève de la responsabilité du client |
| 5 | créneau annulé faute d'atteindre 6 inscrits | même traitement, remboursement intégral, cf. `SPEC-BOOKING-03` |
| 6 | privatisation annulée | même traitement, le forfait est intégralement remboursé |
| 7 | client ayant déjà renoncé après l'alerte, donc déjà remboursé | aucun second remboursement |

### Ce qui n'est pas défini

Assumé au 2026-08-14.

- Sort d'une réservation réglée avec un bon cadeau : le client n'a jamais
  envisagé le cas, ouvert depuis `CR-03`. Hypothèse retenue : un avoir de
  montant équivalent, un bon cadeau n'étant pas de l'argent remboursable.
- Délai de remboursement : non discuté. Hypothèse retenue : déclenché à
  l'enregistrement de l'annulation, exécuté selon les délais du prestataire.
- Trace de l'envoi conservée : le client considère la non-délivrance comme
  un non-sujet. Hypothèse retenue : la date et le canal de chaque envoi sont
  enregistrés, faute de quoi le gérant ne peut répondre à un client
  affirmant n'avoir rien reçu.

### Critères d'acceptation

- [ ] AC-1 : chaque client d'un créneau annulé reçoit un message écrit
      annonçant l'annulation.
- [ ] AC-2 : aucun écran ne propose de choix entre report, avoir et
      remboursement à la suite d'une annulation décidée par le gérant.
- [ ] AC-3 : le montant remboursé est égal au montant payé, sans retenue.
- [ ] AC-4 : le remboursement est déclenché sans intervention téléphonique.
- [ ] AC-5 : une réservation non payée au moment de l'annulation ne donne
      lieu à aucun remboursement.
- [ ] AC-6 : la date et le canal de chaque message envoyé sont enregistrés.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le montant du remboursement après annulation météo n'était écrit nulle part, alors qu'un barème dégressif existe pour les annulations client | acceptée | règle et AC-3 : remboursement intégral, sans retenue |
| Une réservation payée par bon cadeau n'a pas de règle de compensation | acceptée | cas limites 1 et 2, hypothèse de l'avoir équivalent tracée |
| Sans trace des envois, un client affirmant n'avoir rien reçu est inopposable, alors que plus personne ne téléphone | acceptée | AC-6 ajouté, malgré la position du client qui juge la non-délivrance sans objet |
| Conserver un choix entre report, avoir et remboursement, plus favorable au client | refusée | le client a tranché deux fois, le 2026-08-14 : ce choix n'appartient qu'aux annulations qu'il n'a pas décidées lui-même |

## SPEC-CANCEL-05 - Message de rappel automatisé avant la sortie

**Exigences :**

- `REQ-025` : message type envoyé automatiquement, par défaut 24 heures avant.
- `REQ-042` : horaire d'envoi personnalisable par le gérant.
- `REQ-057` : les messages partent par SMS et par e-mail.

**Statut :** revue IA faite
**Version :** v2

### Règle

Chaque client dont la réservation est confirmée reçoit automatiquement, à
l'horaire configuré et par défaut 24 heures avant le départ, un message type
indiquant les conditions météo prévues et les affaires à prévoir, envoyé par
SMS et par e-mail.

> Le gérant n'a aucune action à faire : l'envoi est déclenché par le site à
> l'horaire qu'il a configuré une fois pour toutes.

### Portée

Couvre l'envoi automatique du message de rappel et le réglage de son horaire.
Ne couvre ni l'alerte météo, ni l'annonce d'une annulation.

- Ne couvre pas le message d'alerte ni celui de confirmation d'annulation :
  `SPEC-CANCEL-06`.
- Ne couvre pas l'annonce d'une annulation et son remboursement :
  `SPEC-CANCEL-04`.
- Ne couvre pas la langue du message, traitée comme exigence transverse :
  `SPEC-NFR-02`.
- Ne couvre pas WhatsApp comme canal, hors périmètre, cf. la portée du
  domaine en tête de fichier.

### Scénarios nominaux

```gherkin
Étant donné une réservation confirmée pour une sortie le 20 juillet à 10h00
Et un horaire d'envoi configuré à 24 heures avant le départ
Quand le 19 juillet à 10h00 est atteint
Alors le message type est envoyé au client par SMS et par e-mail
Et il contient les conditions météo prévues et les affaires à prévoir
Et le gérant n'a effectué aucune action
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | réservation confirmée après l'horaire d'envoi, par exemple la veille à 11h pour un départ à 7h | le message est envoyé dès la confirmation |
| 2 | créneau annulé avant l'horaire d'envoi | aucun message de rappel n'est envoyé |
| 3 | créneau mis en alerte | le rappel part quand même : les deux messages coexistent, à quelques heures d'intervalle |
| 4 | horaire d'envoi modifié par le gérant | les envois à venir suivent le nouvel horaire, ceux déjà partis ne sont pas rejoués |
| 5 | envoi impossible sur un canal, adresse ou numéro erronés | l'autre canal part quand même, et l'échec est enregistré |
| 6 | plusieurs réservations d'un même client sur un même créneau | un message par réservation |
| 7 | client ayant choisi l'anglais à la réservation | message envoyé en anglais |

### Ce qui n'est pas défini

Assumé au 2026-08-12, revu au 2026-08-14.

- Source des conditions météo annoncées : aucun service météo n'a été
  évoqué. Hypothèse retenue : le gérant renseigne la prévision du jour dans
  l'espace de gestion, l'application n'interroge aucun service externe.
- Contenu exact du texte type et liste des affaires à prévoir : toujours pas
  fourni par le client, en français comme en anglais. Question 15 de
  `CR-05`.
- Redondance avec l'alerte météo : le client accepte que les deux messages
  partent à quelques heures d'écart. Hypothèse retenue : aucun regroupement,
  aucune suppression de l'un au profit de l'autre.

### Critères d'acceptation

- [ ] AC-1 : à l'horaire configuré, le message type est envoyé
      automatiquement par SMS et par e-mail à chaque client dont la
      réservation est confirmée, sans action du gérant.
- [ ] AC-2 : la valeur par défaut de l'horaire d'envoi est 24 heures avant le
      départ.
- [ ] AC-3 : une modification de l'horaire d'envoi s'applique aux envois à
      venir.
- [ ] AC-4 : aucun message n'est envoyé pour un créneau annulé.
- [ ] AC-5 : une réservation confirmée après l'horaire d'envoi déclenche
      l'envoi immédiatement.
- [ ] AC-6 : l'échec d'envoi sur un canal n'empêche pas l'envoi sur l'autre.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Une réservation confirmée après l'horaire d'envoi ne recevrait jamais de rappel | acceptée | cas limite 1 et AC-5 ajoutés |
| Le canal n'était pas nommé, et l'hypothèse « e-mail seul » a été démentie par le client | acceptée | SMS et e-mail systématiques, l'exigence correspondante ajoutée à la liste en tête de spécification |
| Un échec sur un canal ne devait pas emporter l'autre | acceptée | cas limite 5 et AC-6 ajoutés |
| « Conditions météo prévues » suppose une source de données que le projet n'a pas | acceptée | hypothèse de la saisie par le gérant écrite, ce qui évite d'engager une intégration météo non budgétée |
| Fusionner le rappel et l'alerte quand les deux tombent le même jour | refusée | le client a répondu explicitement que les deux partent, à quelques heures d'intervalle |

## SPEC-CANCEL-06 - Alerte météo préventive

**Exigences :**

- `REQ-052` : le gérant place un créneau en alerte, créneau par créneau.
- `REQ-053` : message d'alerte envoyé la veille à 18h aux clients inscrits.
- `REQ-054` : confirmation 2 heures avant le départ si annulation, silence si maintien.
- `REQ-055` : le créneau en alerte reste réservable, le risque étant signalé.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le gérant peut placer un créneau en alerte météo : les clients inscrits en
sont avertis la veille à 18h, et ne reçoivent un second message que si la
sortie est finalement annulée, deux heures avant l'heure de départ.

> Pour un départ à 7h, l'alerte part la veille à 18h et, si le gérant annule,
> la confirmation part à 5h le jour même. S'il n'annule pas, le client ne
> reçoit rien de plus et la sortie a lieu.

### Portée

Couvre la mise en alerte, les deux messages qui en découlent, et la durée
pendant laquelle l'alerte court. Ne couvre ni l'annulation elle-même, ni ses
conséquences financières.

- Ne couvre pas la décision d'annuler : `SPEC-CANCEL-02`.
- Ne couvre pas le remboursement des clients d'un créneau annulé :
  `SPEC-CANCEL-04`.
- Ne couvre pas le droit au remboursement intégral du client qui renonce
  après une alerte : `SPEC-ADMIN-06`.
- Ne couvre pas l'affichage du risque côté client : `SPEC-CANCEL-03`.
- Ne couvre pas le message de rappel, qui part indépendamment :
  `SPEC-CANCEL-05`.

### Scénarios nominaux

```gherkin
Étant donné un créneau du 20 juillet à 7h portant quatre réservations payées
Quand le gérant met ce créneau en alerte le 19 juillet
Alors chaque client inscrit reçoit, le 19 juillet à 18h, un message par SMS et par e-mail
Et ce message annonce un risque d'annulation et une décision communiquée 2 heures avant le départ
Quand le gérant n'annule pas le créneau
Alors aucun autre message n'est envoyé
Et la sortie a lieu comme prévu
```

```gherkin
Étant donné ce même créneau, en alerte
Quand le gérant l'annule le 20 juillet à 4h00
Alors un message de confirmation d'annulation est envoyé à 5h00
Et les clients sont remboursés selon SPEC-CANCEL-04
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | sortie maintenue | aucun second message : l'absence de message vaut maintien |
| 2 | alerte posée après 18h la veille, ou le jour même | le message d'alerte part immédiatement au lieu d'être programmé |
| 3 | client réservant après l'envoi de l'alerte | il voit le risque signalé sur la plateforme et reçoit, le cas échéant, la confirmation d'annulation |
| 4 | créneau de 14h annulé | la confirmation part à midi, à l'instant même où les réservations de ce créneau ferment, cf. `SPEC-BOOKING-04` |
| 5 | annulation décidée après le repère des 2 heures | le message part immédiatement, voir la rubrique suivante |
| 6 | alerte posée sur un créneau sans aucun inscrit | acceptée, aucun message n'est envoyé |
| 7 | privatisation | traitée comme les autres réservations, le client unique reçoit les mêmes messages |
| 8 | seconde alerte sur un créneau déjà en alerte | sans effet, aucun message n'est renvoyé |
| 9 | créneau annulé faute d'atteindre 6 inscrits alors qu'il était en alerte | l'annulation automatique l'emporte, un seul message d'annulation part |
| 10 | envoi impossible sur un canal | l'autre canal part quand même, et l'échec est enregistré |

### Ce qui n'est pas défini

Assumé au 2026-08-14, à reposer au client (`CR-05` §8, questions 1 et 2).

- Horaires figés ou réglables : 18h pour l'alerte et 2 heures pour la
  confirmation sont donnés comme des valeurs, sans que le client dise si
  elles se règlent. Hypothèse retenue : valeurs par défaut réglables depuis
  l'espace de gestion, par cohérence avec l'horaire du message de rappel.
- Heure limite de décision : hypothèse retenue, l'annulation reste possible
  jusqu'à l'heure de départ, le message partant alors immédiatement.
- Portée d'une alerte sur un créneau où naviguent deux bateaux : hypothèse
  retenue, elle couvre le créneau entier, comme l'annulation.
- Levée d'une alerte sans annulation : aucun message n'est prévu, puisque le
  silence vaut maintien. Le créneau redevient simplement programmé.
- Contenu exact des deux messages, en français et en anglais : non fourni.

### Critères d'acceptation

- [ ] AC-1 : le gérant peut mettre en alerte un créneau donné sans affecter
      les autres créneaux du même jour.
- [ ] AC-2 : aucune alerte n'est déclenchée sans action du gérant.
- [ ] AC-3 : les clients inscrits reçoivent le message d'alerte la veille à
      18h, par SMS et par e-mail.
- [ ] AC-4 : une sortie maintenue ne donne lieu à aucun second message.
- [ ] AC-5 : une sortie annulée donne lieu à un message de confirmation
      2 heures avant l'heure de départ prévue.
- [ ] AC-6 : un créneau en alerte reste réservable jusqu'à son heure de
      fermeture habituelle.
- [ ] AC-7 : un client ayant réservé après l'envoi de l'alerte reçoit la
      confirmation d'annulation.
- [ ] AC-8 : une alerte posée après l'heure d'envoi programmée part
      immédiatement.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « L'alerte court jusqu'à l'heure maximale de réservation » était incohérent pour les créneaux de 7h et 10h, fermés à la réservation la veille à midi, soit six heures avant l'envoi de l'alerte | acceptée | le client a corrigé : l'alerte court jusqu'à l'heure de départ |
| Un client réservant après l'envoi de l'alerte n'était couvert par aucune règle | acceptée | cas limite 3 et AC-7 ajoutés |
| Le message de confirmation d'un créneau de 14h tombe à l'instant exact où ses réservations ferment | acceptée | cas limite 4 écrit, et l'interaction rappelée dans `SPEC-BOOKING-04` |
| Une alerte posée trop tard n'avait pas de comportement défini | acceptée | cas limite 2 et AC-8 : envoi immédiat |
| Envoyer un message de levée d'alerte quand la sortie est maintenue | refusée | le client a explicitement dit qu'aucun message ne part si la sortie est maintenue ; ajouter un message rassurant contredirait la règle qu'il a posée deux fois |
| Rendre l'alerte obligatoire avant toute annulation | refusée | la météo peut se dégrader en quelques heures ; imposer une alerte préalable empêcherait d'annuler un créneau du matin décidé la veille au soir |
