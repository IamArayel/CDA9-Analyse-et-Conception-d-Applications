# Spécifications - ADMIN (espace de gestion du gérant)

**Domaine :** `ADMIN`
**Source :** `docs/cahier-des-charges.md` (v5), cas d'usage Must have
« modifier les tarifs et suivre le planning sans ressaisie manuelle »,
complété par `docs/compte-rendu-entretien-03.md` (CR-03) et
`docs/impact-CR-001.md`, puis par `docs/compte-rendu-entretien-05.md`
(CR-05) et `docs/impact-CR-003.md` pour l'émission d'un avoir.
**Gabarit :** `docs/cle-specification.md` ; chaque spécification en reprend
les sept rubriques, dans le même ordre.

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

- `REQ-030` (Won't, hors création d'un bateau) : l'espace de gestion ne
  permet ni de modifier le contenu présenté aux clients, ni les créneaux, ni
  les bateaux déjà existants (nom, capacité). Seul l'ajout d'un nouveau
  bateau est couvert, par la dernière spécification de ce fichier.
- `REQ-033` : la flotte existante (Ti Kap 12 places, Le Grand Bleu 24 places)
  est une donnée de référence fixe, non éditable depuis l'espace de gestion ;
  contrainte de conception plutôt que fonctionnalité, nuancée en v3 par la
  possibilité d'ajouter un bateau.

---

## SPEC-ADMIN-01 - Connexion à l'espace de gestion

**Exigences :**

- `REQ-031` : accès réservé à un compte unique, celui du gérant.
- `REQ-032` : le gérant est le seul utilisateur quotidien de l'outil.
- `REQ-034` : connexion par e-mail et mot de passe, règle de complexité.
- `REQ-104` : contrôle d'accès de l'espace de gestion.

**Statut :** revue IA faite
**Version :** v1

### Règle

L'espace de gestion n'est accessible qu'après authentification du compte
unique du gérant, par e-mail et mot de passe conforme à la règle de
complexité.

> Un mot de passe compte au moins 8 caractères, dont au moins une majuscule,
> une minuscule, un chiffre et un caractère spécial ; tout mot de passe qui
> ne respecte pas ces quatre conditions est refusé au moment où il est
> défini.

### Portée

Couvre l'authentification et le refus d'accès. Ne couvre pas ce que le gérant
fait une fois connecté, ni l'accès du client au site public.

- Ne couvre pas les comptes multi-utilisateurs (salariés, capitaines) :
  écartés par le client, cf. cahier des charges §6.
- Ne couvre pas les actions de gestion elles-mêmes : `SPEC-ADMIN-02`,
  `SPEC-ADMIN-03`, `SPEC-ADMIN-04`, `SPEC-ADMIN-05`.
- Ne couvre pas la consultation d'un créneau avant annulation météo :
  `SPEC-CANCEL-01`.
- Ne couvre pas le parcours client, qui ne demande aucun compte :
  `SPEC-BOOKING-01`.

### Scénarios nominaux

```gherkin
Étant donné le compte unique du gérant, avec un mot de passe conforme
Quand le gérant saisit son e-mail et son mot de passe corrects
Alors il accède à l'espace de gestion
Et il y retrouve les tarifs, le planning, les horaires et la flotte
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | mot de passe de 7 caractères, par ailleurs complet | refusé à la définition, avec la règle rappelée |
| 2 | mot de passe de 12 caractères sans caractère spécial | refusé à la définition |
| 3 | e-mail correct, mot de passe erroné | accès refusé, sans indiquer lequel des deux champs est en cause |
| 4 | e-mail inconnu | accès refusé, message identique au cas 3 |
| 5 | accès direct à une URL de l'espace de gestion sans session ouverte | accès refusé, renvoi vers l'écran de connexion |
| 6 | tentative de créer un second compte de gestion | impossible : aucun écran de création de compte n'existe |
| 7 | échecs de connexion répétés | non défini, voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12, à reposer au client (question 5 du §11 du cahier des
charges, restée ouverte après CR-03).

- Verrouillage après N échecs de connexion : aucune règle demandée par le
  client. Hypothèse retenue en attendant : aucun verrouillage automatique.
- Réinitialisation d'un mot de passe oublié : aucune procédure demandée. Sur
  un compte unique, l'absence de procédure bloquerait toute l'exploitation ;
  hypothèse retenue : réinitialisation par l'équipe technique, hors outil.
- Durée d'une session ouverte : non discutée ; hypothèse retenue :
  déconnexion après une période d'inactivité, valeur à fixer en conception.

### Critères d'acceptation

- [ ] AC-1 : une connexion avec l'e-mail et le mot de passe du compte unique
      donne accès à l'espace de gestion.
- [ ] AC-2 : un mot de passe qui ne respecte pas l'une des quatre conditions
      de complexité est refusé au moment où il est défini.
- [ ] AC-3 : une tentative de connexion avec des identifiants incorrects est
      refusée, avec le même message quel que soit le champ en cause.
- [ ] AC-4 : une requête vers une page de l'espace de gestion sans session
      ouverte n'affiche aucune donnée de gestion.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| L'exigence « l'outil est utilisé au quotidien par le gérant uniquement » est un élément de contexte, pas un comportement testable | acceptée | l'exigence est citée en portée du domaine plutôt que transformée en critère d'acceptation ; le critère testable est l'unicité du compte (AC-1) |
| Aucune procédure de mot de passe oublié sur un compte unique : risque d'arrêt complet de l'exploitation | acceptée | tracé dans « Ce qui n'est pas défini » et rattaché à la question 5 du §11 du cahier des charges |
| Un message d'erreur distinguant e-mail inconnu et mot de passe erroné faciliterait l'énumération des comptes | acceptée | cas limites 3 et 4 alignés sur un message unique |
| Proposer une authentification à deux facteurs | refusée | non demandée par le client, hors périmètre d'un outil à un seul utilisateur et à faible enjeu ; contredirait la contrainte de coût |

## SPEC-ADMIN-02 - Modification des tarifs

**Exigences :**

- `REQ-016` : le gérant modifie les tarifs lui-même.
- `REQ-028` : l'espace de gestion permet de modifier les tarifs.

**Statut :** revue IA faite
**Version :** v1

### Règle

Un tarif modifié par le gérant s'applique aux réservations créées après la
modification et ne change jamais le montant d'une réservation déjà payée.

> Le gérant porte le tarif adulte d'une sortie dauphins de 50 € à 55 € ; les
> réservations déjà payées à 50 € restent à 50 €, les suivantes sont
> calculées à 55 €.

### Portée

Couvre la modification des tarifs adulte et enfant par type de sortie, et des
forfaits de privatisation par bateau. Ne couvre pas le calcul d'un montant,
ni les valeurs de départ.

- Ne couvre pas le calcul du montant d'une réservation standard :
  `SPEC-BOOKING-06`.
- Ne couvre pas le forfait de privatisation appliqué à une réservation :
  `SPEC-BOOKING-05`.
- Ne couvre pas le montant d'un bon cadeau déjà vendu : `SPEC-BOOKING-09`.
- Ne couvre pas le remboursement d'une sortie annulée : `SPEC-CANCEL-04`.

### Scénarios nominaux

```gherkin
Étant donné une réservation dauphins déjà payée 100 € pour deux adultes
Et le tarif adulte dauphins à 50 €
Quand le gérant porte le tarif adulte dauphins à 55 €
Alors la réservation déjà payée reste à 100 €
Et une nouvelle réservation pour deux adultes est calculée à 110 €
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | tarif modifié alors qu'un client a validé son formulaire sans avoir payé | le montant présenté au récapitulatif reste celui en vigueur à la validation du formulaire |
| 2 | tarif négatif saisi | refusé |
| 3 | tarif à 0 € saisi | refusé : aucune sortie gratuite n'a été prévue par le client |
| 4 | tarif d'une sortie baleines modifié hors saison | accepté, sans effet visible tant que la saison n'a pas repris |
| 5 | tarif modifié entre l'achat d'un bon cadeau et son utilisation | le bon conserve son montant ; l'écart est payé par carte ou perdu selon son sens |
| 6 | tarif de privatisation d'un bateau créé après coup | à saisir avant que la privatisation de ce bateau soit proposée |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Date d'effet différée d'un tarif : le client modifie ses tarifs « en
  général une fois par an » sans évoquer de programmation. Hypothèse
  retenue : effet immédiat à l'enregistrement.
- Historique des tarifs successifs : non demandé. Hypothèse retenue : seule
  la valeur en cours est conservée, le montant payé étant figé sur chaque
  réservation.

### Critères d'acceptation

- [ ] AC-1 : un tarif modifié depuis l'espace de gestion s'applique à toute
      réservation créée après l'enregistrement.
- [ ] AC-2 : le montant d'une réservation déjà payée reste inchangé après une
      modification de tarif.
- [ ] AC-3 : un tarif négatif ou nul est refusé à la saisie.
- [ ] AC-4 : le montant d'un récapitulatif déjà présenté au client ne change
      pas si le tarif est modifié avant son paiement.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le comportement d'un tarif modifié pendant qu'un client est dans le tunnel de paiement n'était pas défini | acceptée | cas limite 1 ajouté et AC-4 créé : le montant est figé à la validation du formulaire |
| « S'applique aux réservations futures » n'est pas testable tant que « future » n'est pas rattaché à un événement | acceptée | la règle nomme désormais l'événement de référence, la création de la réservation |
| Le cas d'un tarif à 0 € (sortie offerte) n'était pas tranché | acceptée | cas limite 3 : refusé, faute de règle client sur la gratuité |
| Prévoir un versionnement complet des tarifs pour la comptabilité | refusée | le client tient sa comptabilité à la main et n'a demandé aucun historique ; le montant payé reste porté par la réservation, ce qui suffit à la traçabilité comptable |

## SPEC-ADMIN-03 - Export du planning des réservations

**Exigences :**

- `REQ-029` : planning des réservations dans un format imprimable.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le gérant obtient à tout moment, depuis l'espace de gestion, un document
imprimable listant les réservations de la période demandée, créneau par
créneau.

> Pour la journée du 20 juillet, le document liste les trois créneaux, et
> pour chacun les clients inscrits, leur contact et leur nombre de
> participants.

### Portée

Couvre la génération d'un document imprimable au format PDF. Ne couvre
aucune action sur les réservations depuis ce document.

- Ne couvre pas la modification d'une réservation depuis le planning :
  aucune saisie de réservation n'existe dans l'espace de gestion,
  cf. `SPEC-BOOKING-01`.
- Ne couvre pas la liste des inscrits consultée avant une annulation météo,
  qui est un écran et non un export : `SPEC-CANCEL-01`.
- Ne couvre pas la répartition des passagers entre les deux bateaux, écartée
  par le client, cf. la portée du domaine BOOKING.

### Scénarios nominaux

```gherkin
Étant donné trois réservations payées sur le créneau de 10h du 20 juillet
Quand le gérant demande l'export du planning pour le 20 juillet
Alors un document PDF imprimable est généré
Et il liste les trois réservations sous le créneau de 10h
Et il indique le nombre total de participants attendus sur ce créneau
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | période demandée sans aucune réservation | document généré, indiquant explicitement l'absence de réservation |
| 2 | créneau annulé pour raison météo sur la période | le créneau figure au document avec la mention « annulé » |
| 3 | réservation en attente de paiement | absente du document : le planning ne liste que les réservations payées |
| 4 | privatisation sur un créneau | figure comme une réservation unique occupant tout le bateau |
| 5 | export demandé pendant un pic de réservations | document conforme à l'état des réservations au moment de la demande |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Étendue de la période exportée : le client n'a pas précisé s'il voulait la
  journée, la semaine ou la saison. Hypothèse retenue : plage de dates au
  choix, proposée par défaut sur la journée du lendemain, qui est l'usage
  décrit en entretien.
- Ordre et contenu exact des colonnes : non discutés. Hypothèse retenue :
  créneau, bateau, nom du client, contact, nombre d'adultes et d'enfants.

### Critères d'acceptation

- [ ] AC-1 : une demande d'export produit un document PDF imprimable.
- [ ] AC-2 : les réservations du document sont regroupées par créneau, dans
      l'ordre chronologique.
- [ ] AC-3 : une période sans réservation produit un document lisible
      mentionnant l'absence de réservation, et non une erreur.
- [ ] AC-4 : une réservation non payée n'apparaît pas au document.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « Format imprimable » n'est pas vérifiable tant que le format n'est pas nommé | acceptée | le PDF est retenu explicitement dans la règle et en AC-1 |
| Le sort des réservations non payées dans le planning n'était pas tranché | acceptée | cas limite 3 et AC-4 : seules les réservations payées figurent au planning, cohérent avec le fait que les places ne sont décomptées qu'au paiement |
| Un export vide devrait être une erreur explicite | refusée | un document vide est un résultat métier normal hors saison ; le gérant doit pouvoir imprimer une journée sans réservation sans se demander si l'outil a échoué |

## SPEC-ADMIN-04 - Horaires d'ouverture et jours de fermeture

**Exigences :**

- `REQ-038` : aucune sortie proposée le 25 décembre ni le 1ᵉʳ janvier.
- `REQ-039` : horaires et jours de fermeture modifiables par le gérant.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le gérant déclare les jours de fermeture depuis l'espace de gestion, et
aucun créneau n'est proposé à la réservation sur un jour déclaré fermé ; le
25 décembre et le 1ᵉʳ janvier y figurent par défaut.

> Le gérant ajoute le 15 août aux jours de fermeture ; à compter de cet
> enregistrement, un client qui consulte le 15 août ne se voit proposer
> aucun créneau.

### Portée

Couvre la déclaration des jours de fermeture et des horaires d'ouverture, et
leur effet immédiat sur les créneaux proposés. Ne couvre ni la définition des
créneaux, ni l'annulation d'un départ isolé.

- Ne couvre pas l'affichage des créneaux côté client, qui applique cette
  déclaration : `SPEC-BOOKING-02`.
- Ne couvre pas l'annulation d'un créneau pour raison météo, décidée cas par
  cas : `SPEC-CANCEL-02`.
- Ne couvre pas la modification des trois heures de départ, écartée par le
  client, cf. la portée du domaine en tête de fichier.
- Ne couvre pas le sort des réservations déjà payées sur une date qui
  devient fermée : voir « Ce qui n'est pas défini ».

### Scénarios nominaux

```gherkin
Étant donné l'espace de gestion, à la première ouverture
Quand le gérant consulte la section des horaires
Alors le 25 décembre et le 1ᵉʳ janvier apparaissent comme jours de fermeture
Quand le gérant ajoute le 15 août aux jours de fermeture
Alors aucun créneau n'est proposé à la réservation le 15 août
Et les autres jours restent inchangés
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | ajout d'un jour de fermeture sur une date portant déjà des réservations payées | l'ajout est accepté, et les réservations concernées sont listées au gérant, qui les traite par téléphone : rien n'est annulé ni remboursé automatiquement |
| 2 | retrait du 25 décembre de la liste | les trois créneaux redeviennent réservables ce jour-là |
| 3 | jour de fermeture ajouté sur une date passée | accepté, sans effet sur les réservations déjà honorées |
| 4 | jour de fermeture ajouté deux fois | la date n'apparaît qu'une fois dans la liste |
| 5 | horaires d'ouverture ne couvrant pas l'heure d'un départ | les trois départs restent proposés : les horaires d'ouverture ne pilotent pas les créneaux, voir la rubrique suivante |
| 6 | 25 décembre de l'année suivante | reste fermé : les deux dates par défaut se reconduisent chaque année |

### Ce qui n'est pas défini

Assumé au 2026-08-12, tracé dans `docs/impact-CR-001.md` §8 et §9.

- Effet d'une fermeture ajoutée sur une date déjà ouverte à la réservation :
  le client n'a pas envisagé le cas. Hypothèse retenue : aucune annulation
  automatique, traitement manuel par le gérant, cohérent avec le fait que
  toute annulation reste sa décision.
- Lien entre horaires d'ouverture et heures de départ : le client a demandé
  une section pour « modifier les horaires d'ouverture et de fermeture »
  sans dire si elle contraint les trois départs. Hypothèse retenue : les
  horaires décrivent l'accueil de l'entreprise et laissent les trois
  créneaux inchangés.
- Récurrence d'une date ajoutée : hypothèse retenue, toute date ajoutée est
  ponctuelle, seules les deux dates par défaut se reconduisent chaque année.

### Critères d'acceptation

- [ ] AC-1 : à la première ouverture de la section, le 25 décembre et le
      1ᵉʳ janvier figurent comme jours de fermeture.
- [ ] AC-2 : l'ajout d'un jour de fermeture supprime, le jour même de
      l'enregistrement, tous les créneaux proposés à cette date côté client.
- [ ] AC-3 : le retrait d'un jour de fermeture rétablit les créneaux de cette
      date.
- [ ] AC-4 : l'ajout d'un jour de fermeture sur une date portant des
      réservations payées n'annule ni ne rembourse aucune de ces
      réservations, et les signale au gérant.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le sort des réservations déjà payées sur une date qui devient fermée n'est réglé nulle part | acceptée | cas limite 1 et AC-4 ajoutés, hypothèse tracée ; le point figurait déjà comme effet de bord dans l'analyse d'impact |
| Le lien entre horaires d'ouverture et heures de départ est ambigu : la spécification pourrait laisser croire que fermer avant 7h supprime le créneau de 7h | acceptée | cas limite 5 et hypothèse explicite ajoutés |
| « Sans intervention technique » n'est pas mesurable | acceptée | AC-2 reformulé sur un fait observable, la disparition des créneaux le jour même de l'enregistrement |
| Proposer une gestion de périodes de fermeture (congés annuels) plutôt que des dates isolées | refusée | le client n'a cité que deux dates fixes ; ajouter une notion de période dépasserait le besoin exprimé et alourdirait le modèle de données au-delà du MVP |

## SPEC-ADMIN-05 - Création d'un nouveau bateau

**Exigences :**

- `REQ-041` : création d'un bateau (nom, capacité) depuis l'espace de gestion.

**Statut :** revue IA faite
**Version :** v1

### Règle

Un bateau créé depuis l'espace de gestion avec un nom et une capacité est
immédiatement proposé côté client, avec la capacité saisie et sans
restriction de type de sortie.

> Le gérant crée « Le Petit Bleu », 8 places ; dès l'enregistrement, un
> client qui consulte un créneau voit 8 places disponibles sur ce bateau.

### Portée

Couvre l'ajout d'un bateau à la flotte. Ne couvre ni la modification, ni la
suppression, ni les règles d'exploitation qui s'appliquent ensuite à ce
bateau comme aux autres.

- Ne couvre pas la modification ou la suppression d'un bateau existant,
  écartées par le client, cf. la portée du domaine en tête de fichier.
- Ne couvre pas les règles de capacité et de naturaliste unique, qui
  s'appliquent à tout bateau : `SPEC-BOOKING-03`.
- Ne couvre pas la répartition des passagers entre bateaux, écartée par le
  client.
- Ne couvre pas le forfait de privatisation du nouveau bateau, qui relève de
  la saisie des tarifs : `SPEC-ADMIN-02`.

### Scénarios nominaux

```gherkin
Étant donné une flotte de deux bateaux
Quand le gérant crée un bateau nommé « Le Petit Bleu » avec 8 places
Alors ce bateau apparaît dans les créneaux proposés côté client
Et le nombre de places disponibles affiché pour ce bateau est 8
Et il est proposé pour les sorties dauphins comme pour les sorties baleines
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | capacité saisie à 0 ou négative | refusée |
| 2 | capacité non entière | refusée |
| 3 | nom identique à un bateau existant | refusé : le nom identifie le bateau sur le planning et pour le gérant |
| 4 | bateau créé en pleine saison baleines | soumis comme les autres à la règle du naturaliste unique : un seul bateau engagé en sortie baleines par créneau |
| 5 | privatisation du nouveau bateau demandée par un client | non proposée tant que le gérant n'a pas saisi le forfait de privatisation de ce bateau |
| 6 | bateau créé par erreur | aucune suppression prévue, voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12, à reposer au client (questions 6 et 7 du §11 du cahier
des charges, et `CR-03` §8).

- Types de sorties compatibles à la création : hypothèse d'équipe, le
  formulaire se limite à un nom et une capacité, tout bateau créé étant
  habilité à tous les types de sortie, faute de disposer de cette
  information pour les deux bateaux existants eux-mêmes.
- Suppression ou désactivation d'un bateau créé par erreur : non demandée par
  le client. Hypothèse retenue : correction par l'équipe technique, hors
  outil.
- Forfait de privatisation d'un nouveau bateau : le client n'a donné de
  forfait que pour les deux bateaux existants. Hypothèse retenue : la
  privatisation de ce bateau n'est proposée qu'une fois son forfait saisi.

### Critères d'acceptation

- [ ] AC-1 : un bateau créé avec un nom et une capacité valides apparaît dans
      les créneaux proposés côté client sans intervention technique.
- [ ] AC-2 : le nombre de places affiché pour ce bateau est égal à la
      capacité saisie.
- [ ] AC-3 : une capacité nulle, négative ou non entière est refusée.
- [ ] AC-4 : un nom déjà porté par un bateau de la flotte est refusé.
- [ ] AC-5 : tant qu'aucun forfait de privatisation n'est saisi pour ce
      bateau, il n'est pas proposé à la privatisation.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Un bateau créé n'a aucun forfait de privatisation, alors que la privatisation est tarifée par bateau : contradiction avec la spécification de privatisation | acceptée | cas limite 5 et AC-5 ajoutés ; le point n'apparaissait dans aucun document du dépôt |
| L'unicité du nom d'un bateau n'était pas exigée alors que le nom sert d'identifiant au planning | acceptée | cas limite 3 et AC-4 ajoutés |
| L'habilitation du nouveau bateau aux sorties baleines reste une hypothèse d'équipe | acceptée | tracée dans « Ce qui n'est pas défini » et rattachée à la question 7 du §11 du cahier des charges |
| Prévoir dès maintenant un champ « types de sorties compatibles » | refusée | l'hypothèse d'équipe retenue est l'inverse, et l'information n'existe même pas pour les deux bateaux actuels : ajouter le champ créerait une donnée que personne ne sait renseigner tant que le client n'a pas répondu |

## SPEC-ADMIN-06 - Enregistrement d'une annulation client et émission d'un avoir

**Exigences :**

- `REQ-019` : annulation à l'initiative du client, issue et barème dégressif.
- `REQ-050` : l'avoir est délivré sous forme d'un code de réduction unique.
- `REQ-056` : remboursement intégral si le créneau avait été mis en alerte.

**Statut :** revue IA faite
**Version :** v1

### Règle

Quand un client annule sa réservation par téléphone, le gérant enregistre
depuis l'espace de gestion l'issue convenue, report, avoir ou remboursement,
et l'application produit le code d'avoir lorsque c'est cette issue qui est
retenue.

> La négociation reste téléphonique, mais elle laisse une trace dans l'outil,
> et un avoir n'existe pas tant que le gérant ne l'a pas émis.

### Portée

Couvre l'enregistrement de l'issue d'une annulation demandée par le client et
l'émission du code d'avoir. Ne couvre ni la conversation téléphonique, ni
l'usage du code, ni les annulations décidées par le gérant.

- Ne couvre pas l'usage du code d'avoir au paiement : `SPEC-BOOKING-10`.
- Ne couvre pas l'annulation décidée par le gérant, qui donne un
  remboursement intégral sans choix : `SPEC-CANCEL-04`.
- Ne couvre pas la mise en alerte d'un créneau : `SPEC-CANCEL-06`.
- Ne couvre pas le report vers un autre créneau, qui reste soumis à la
  disponibilité : `SPEC-BOOKING-03`.
- Ne couvre pas la demande d'annulation elle-même : elle se fait par
  téléphone, hors application, cf. la portée du domaine CANCEL.

**Ajoutée en v5.** Jusqu'à la v4, l'avoir était décrit comme émis à la suite
d'une annulation météo. Le client a corrigé le 2026-08-14 : cette issue
n'appartient qu'aux annulations demandées par le client. Sans cette
spécification, plus aucun avoir ne pourrait être créé, alors que son usage
est spécifié.

### Scénarios nominaux

```gherkin
Étant donné une réservation payée 170 € pour une sortie dans 5 jours
Et un client qui appelle le gérant pour annuler
Quand ils conviennent d'un avoir et que le gérant l'enregistre
Alors un code d'avoir unique est produit
Et son montant est celui décidé par le gérant, retenue du barème comprise
Et sa date d'expiration est fixée à un an
Et le code est transmis au client par écrit
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | annulation à plus de 7 jours du départ | le gérant applique 100 %, aucune retenue |
| 2 | annulation entre 48 heures et 24 heures | le gérant applique la retenue de 50 % prévue au barème |
| 3 | annulation à moins de 24 heures du départ | aucun barème n'est défini en deçà de 24 heures, voir la rubrique suivante |
| 4 | créneau mis en alerte météo, client qui renonce | remboursement intégral quel que soit le délai, y compris si la sortie a finalement lieu |
| 5 | issue « report » enregistrée | aucun code n'est émis, la réservation est rattachée au nouveau créneau sous réserve de disponibilité |
| 6 | issue « remboursement » enregistrée | aucun code n'est émis, le remboursement suit le circuit du prestataire |
| 7 | réservation payée par un bon cadeau ou un avoir | l'issue « remboursement » est remplacée par un avoir de montant équivalent |
| 8 | même réservation annulée deux fois | refusé : une réservation déjà annulée n'a plus d'issue à enregistrer |

### Ce qui n'est pas défini

Assumé au 2026-08-14.

- Barème applicable à moins de 24 heures du départ : le client n'a jamais
  descendu son barème en dessous de ce seuil. Hypothèse retenue : aucune
  retenue automatique, le gérant décide au cas par cas, comme il le fait
  aujourd'hui.
- Montant d'un avoir par rapport au barème : le client n'a pas dit si un
  avoir subit la même retenue qu'un remboursement. Hypothèse retenue : oui,
  le gérant saisit un montant libre et reste maître de l'arbitrage.
- Canal de transmission du code au client : hypothèse retenue, le même que
  les autres messages, SMS et e-mail.

### Critères d'acceptation

- [ ] AC-1 : le gérant peut enregistrer, pour une réservation donnée, l'issue
      convenue parmi report, avoir et remboursement.
- [ ] AC-2 : l'enregistrement d'un avoir produit un code unique, d'un montant
      saisi par le gérant et expirant un an plus tard.
- [ ] AC-3 : aucun code n'est produit pour une issue « report » ou
      « remboursement ».
- [ ] AC-4 : lorsque le créneau concerné est en alerte, le montant remboursé
      proposé est le montant intégral, sans retenue.
- [ ] AC-5 : une réservation déjà annulée ne peut pas recevoir une seconde
      issue.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Après la correction du 2026-08-14, plus aucune spécification ne créait d'avoir, alors que son usage et son expiration restaient spécifiés | acceptée | cette spécification est ajoutée pour cette seule raison ; sans elle, `SPEC-BOOKING-10` décrit un code que personne ne peut produire |
| Le barème s'arrête à 24 heures du départ et ne dit rien en deçà | acceptée | cas limite 3 et hypothèse écrites plutôt que laissées implicites, faute de règle client |
| Un client qui renonce après une alerte relève de deux règles contradictoires, le barème et le remboursement intégral | acceptée | cas limite 4 et AC-4 : l'alerte l'emporte, le risque venant du gérant |
| Automatiser le calcul de la retenue à partir du barème | refusée | le client applique ce barème à la main depuis toujours et n'a jamais demandé son automatisation ; l'outil garde la trace de sa décision, il ne la prend pas à sa place |
