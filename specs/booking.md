# Spécifications - BOOKING (réservation en ligne)

**Domaine :** `BOOKING`
**Source :** `docs/cahier-des-charges.md` (v3), cas d'usage Must have
« réserver et payer une sortie en ligne », complété par
`docs/compte-rendu-entretien-03.md` (CR-03) et `docs/impact-CR-001.md`.
**Gabarit :** `docs/cle-specification.md` ; chaque spécification en reprend
les sept rubriques, dans le même ordre.

**Convention de traçabilité.** Les exigences couvertes sont listées une par
ligne, immédiatement sous le titre de la spécification. `tools/traceability.sh`
rattache une exigence au dernier identifiant `SPEC-xxx-nn` rencontré au-dessus
d'elle et ne retient que la première de chaque ligne : aucune exigence n'est
donc citée ailleurs que dans cette liste, et les renvois vers une autre
spécification n'apparaissent qu'après elle. Les critères d'acceptation sont
écrits pour être directement transposables en cas de test
(`tests/cases/CASE-BOOKING-nn.md`).

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

- `REQ-013` (Won't) : les suppléments personnalisables, par exemple le
  champagne à bord, restent vendus uniquement par téléphone ; aucune vente en
  ligne.
- `REQ-037` (Won't) : la répartition des passagers entre les bateaux, quand
  plusieurs sont disponibles, reste une décision manuelle du gérant, hors
  outil ; aucune règle de répartition automatique.

---

## SPEC-BOOKING-01 - Formulaire et validité d'une réservation standard

**Exigences :**

- `REQ-001` : réservation possible pour une personne seule ou pour un groupe.
- `REQ-008` : accès interdit aux enfants de moins de 4 ans.
- `REQ-009` : liste exhaustive des informations demandées.
- `REQ-015` : tarif enfant de 4 à 11 ans, adulte à partir de 12 ans.
- `REQ-036` : le site est l'unique point d'entrée d'une nouvelle réservation.

**Statut :** revue IA faite
**Version :** v2

### Règle

Une réservation est créée dès lors que les huit informations demandées sont
fournies et qu'elle porte sur au moins un participant.

> Un client qui renseigne nom, prénom, e-mail, téléphone, nombre d'adultes,
> nombre d'enfants, créneau et type de sortie, pour une seule place, obtient
> une réservation à l'état « en attente de paiement ».

### Portée

Couvre la saisie du formulaire, sa validation et l'état initial de la
réservation. Ne couvre ni le prix, ni le paiement, ni la disponibilité.

- Ne couvre pas le calcul du montant : `SPEC-BOOKING-06`.
- Ne couvre pas le paiement ni le passage à l'état confirmé :
  `SPEC-BOOKING-07`.
- Ne couvre pas le contrôle de capacité du créneau : `SPEC-BOOKING-03`.
- Ne couvre pas l'heure limite de réservation : `SPEC-BOOKING-04`.
- Ne couvre pas la privatisation, qui ne se compte pas en places :
  `SPEC-BOOKING-05`.
- Ne couvre pas la saisie d'une réservation par le gérant : elle n'existe
  pas, cf. `SPEC-ADMIN-03`, qui n'offre qu'un export en lecture.

**L'âge exact de chaque enfant n'est pas un champ du formulaire.** Le client
n'a demandé qu'un nombre d'enfants ; l'interdiction d'accès aux moins de
4 ans est donc affichée en avertissement, pas contrôlée par l'application.

### Scénarios nominaux

```gherkin
Étant donné un créneau dauphins du 20 juillet à 10h00 avec 5 places libres
Et un client qui réserve une place pour lui seul
Quand il renseigne nom, prénom, e-mail, téléphone, 1 adulte, 0 enfant
Et qu'il valide le formulaire
Alors la réservation est acceptée
Et elle passe à l'état « en attente de paiement »
Et aucun âge individuel d'enfant ne lui a été demandé
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | 1 adulte, 0 enfant | accepté : aucun minimum de personnes n'est imposé |
| 2 | 0 adulte, 0 enfant | refusé : au moins un participant est requis |
| 3 | 0 adulte, 2 enfants | refusé : au moins un adulte est requis dès qu'un enfant est déclaré, voir la rubrique suivante |
| 4 | enfant de moins de 4 ans dans le groupe | non détectable par l'application ; l'avertissement d'interdiction est affiché avant la validation |
| 5 | e-mail ou téléphone au format invalide | refusé, avec indication du champ concerné |
| 6 | champ obligatoire vide | refusé, avec indication du champ concerné |
| 7 | nombre de participants supérieur à la capacité du plus grand bateau | refusé au titre de la capacité, cf. `SPEC-BOOKING-03` |
| 8 | client rappelant le gérant pour réserver par téléphone | aucune saisie possible : le gérant le renvoie vers le site |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Réservation composée uniquement d'enfants : le client n'a jamais évoqué de
  mineur non accompagné. Hypothèse retenue : refusée, au moins un adulte est
  requis dès qu'un enfant est déclaré.
- Format attendu du numéro de téléphone (national ou international) : non
  discuté. Hypothèse retenue : format libre, non vide, contrôlé sur sa forme
  générale seulement.
- Acceptation de conditions générales ou d'une mention RGPD au moment de la
  réservation : non demandée par le client. Hypothèse retenue : une case
  d'acceptation est nécessaire, sa formulation reste à valider.

### Critères d'acceptation

- [ ] AC-1 : un formulaire complet portant sur un seul participant est
      accepté et crée une réservation à l'état « en attente de paiement ».
- [ ] AC-2 : un formulaire sans aucun participant est refusé.
- [ ] AC-3 : un formulaire déclarant des enfants sans aucun adulte est
      refusé.
- [ ] AC-4 : un champ obligatoire manquant ou invalide est refusé, et le
      champ en cause est nommé au client.
- [ ] AC-5 : l'avertissement d'interdiction d'accès aux enfants de moins de
      4 ans est visible avant la validation du formulaire.
- [ ] AC-6 : aucun écran ne demande l'âge individuel d'un enfant.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Une réservation de 2 enfants et 0 adulte satisfait toutes les règles écrites alors qu'elle est absurde | acceptée | cas limite 3, AC-3 et hypothèse d'équipe ajoutés |
| L'interdiction d'accès aux moins de 4 ans est présentée comme une règle applicative alors qu'aucune donnée ne permet de la vérifier | acceptée | la règle est explicitement qualifiée d'avertissement affiché, et AC-5 porte sur l'affichage, pas sur un contrôle de saisie |
| « Réservation valide » n'était pas rattachée à un état observable | acceptée | l'état « en attente de paiement » est nommé dans la règle et en AC-1 |
| Ajouter un champ d'âge par enfant pour rendre la règle des 4 ans vérifiable | refusée | le client a explicitement exclu toute information supplémentaire ; collecter une donnée non demandée irait aussi contre la minimisation retenue en NFR sur les données personnelles |

## SPEC-BOOKING-02 - Créneaux et types de sortie proposés selon la saison

**Exigences :**

- `REQ-010` : trois créneaux par jour, à 7h, 10h et 14h, d'environ 3 heures.
- `REQ-011` : sorties baleines du 15 juin au 31 octobre, dauphins toute l'année.
- `REQ-038` : aucun créneau les jours de fermeture.

**Statut :** revue IA faite
**Version :** v1

### Règle

Trois créneaux de départ sont proposés chaque jour d'ouverture, à 7h, 10h et
14h ; ils proposent une sortie dauphins toute l'année et une sortie baleines
seulement du 15 juin au 31 octobre.

> Le 1ᵉʳ décembre, les trois créneaux ne proposent que la sortie dauphins ;
> le 1ᵉʳ août, ils proposent dauphins et baleines.

### Portée

Couvre l'offre de créneaux et de types de sortie présentée au client. Ne
couvre ni la disponibilité en places, ni l'heure limite de réservation.

- Ne couvre pas la déclaration des jours de fermeture et des horaires, qui
  se fait côté gestion : `SPEC-ADMIN-04`.
- Ne couvre pas le nombre de places restantes : `SPEC-BOOKING-03`.
- Ne couvre pas l'heure au-delà de laquelle un créneau n'est plus
  réservable : `SPEC-BOOKING-04`.
- Ne couvre pas l'annulation d'un créneau pour raison météo :
  `SPEC-CANCEL-02`.

### Scénarios nominaux

```gherkin
Étant donné une date au 1ᵉʳ août, en saison baleines
Quand le client consulte les créneaux de cette date
Alors les créneaux de 7h, 10h et 14h sont proposés
Et chacun propose une sortie dauphins et une sortie baleines
Étant donné une date au 1ᵉʳ décembre
Quand le client consulte les créneaux de cette date
Alors seule la sortie dauphins est proposée, sur les trois créneaux
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | 15 juin et 31 octobre | bornes incluses : les sorties baleines sont proposées ces deux jours |
| 2 | 14 juin et 1ᵉʳ novembre | hors saison : seule la sortie dauphins est proposée |
| 3 | 25 décembre ou 1ᵉʳ janvier | aucun créneau proposé à la réservation |
| 4 | jour de fermeture ajouté par le gérant | aucun créneau proposé à cette date, dès l'enregistrement |
| 5 | demande de réservation d'une sortie baleines hors saison, formulée hors du parcours d'affichage | refusée : la saison est contrôlée à l'enregistrement, pas seulement à l'affichage |
| 6 | date située au-delà de la saison en cours, par exemple à 18 mois | voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Horizon d'ouverture des réservations : le client n'a pas dit jusqu'à quand
  à l'avance on peut réserver. Hypothèse retenue : 12 mois glissants, ce qui
  couvre l'usage d'un bon cadeau valable un an.
- Durée exacte d'une sortie : « environ 3 heures » n'est pas un engagement
  contractuel. Hypothèse retenue : durée indicative affichée, sans heure de
  retour garantie.

### Critères d'acceptation

- [ ] AC-1 : sur une date en saison, les trois créneaux proposent dauphins et
      baleines.
- [ ] AC-2 : sur une date hors saison, les trois créneaux ne proposent que
      dauphins.
- [ ] AC-3 : les 15 juin et 31 octobre proposent des sorties baleines.
- [ ] AC-4 : un jour de fermeture ne propose aucun créneau.
- [ ] AC-5 : une réservation de sortie baleines portant sur une date hors
      saison est refusée à l'enregistrement.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Les bornes du 15 juin et du 31 octobre n'étaient pas dites inclusives | acceptée | cas limite 1 et AC-3 ajoutés |
| La saison n'était contrôlée qu'à l'affichage : rien n'empêchait un enregistrement hors saison | acceptée | cas limite 5 et AC-5 ajoutés |
| Aucun horizon maximal de réservation n'est fixé, alors qu'un bon cadeau est valable un an | acceptée | hypothèse des 12 mois glissants tracée dans « Ce qui n'est pas défini » |
| Rendre la durée de 3 heures vérifiable par un horaire de retour | refusée | le client parle d'une durée « d'environ 3 heures » liée à l'observation en mer ; en faire un engagement horaire créerait une obligation que l'exploitation ne peut pas tenir |

## SPEC-BOOKING-03 - Capacité, seuil minimal et places disponibles en temps réel

**Exigences :**

- `REQ-002` : sortie maintenue à partir de 6 inscrits, contrôlé à 24 heures.
- `REQ-003` : en deçà, sortie annulée et clients remboursés.
- `REQ-004` : places restantes visibles au moment de réserver.
- `REQ-007` : un seul bateau engagé à la fois sur une sortie baleines.
- `REQ-033` : capacités de la flotte existante, 12 et 24 places.

**Statut :** revue IA faite
**Version :** v1

### Règle

La capacité d'un bateau n'est jamais dépassée, et une sortie qui compte moins
de 6 inscrits au contrôle des 24 heures est annulée et intégralement
remboursée.

> Sur un créneau du Ti Kap où 3 places restent libres, une demande pour
> 4 personnes est refusée ; si ce même créneau ne compte que 5 inscrits
> 24 heures avant le départ, la sortie est annulée et les 5 clients sont
> remboursés intégralement.

### Portée

Couvre le décompte des places, le seuil de maintien d'une sortie et la
contrainte du naturaliste unique. Ne couvre ni la tarification, ni le
paiement, ni les remboursements décidés au cas par cas.

- Ne couvre pas le moment où les places sont décomptées, qui dépend du
  paiement : `SPEC-BOOKING-07`.
- Ne couvre pas la privatisation, qui bloque le bateau entier :
  `SPEC-BOOKING-05`.
- Ne couvre pas l'annulation météo, décidée par le gérant :
  `SPEC-CANCEL-02`.
- Ne couvre pas l'ajout d'un bateau à la flotte : `SPEC-ADMIN-05`.
- Ne couvre pas la répartition des passagers entre bateaux, écartée par le
  client, cf. la portée du domaine en tête de fichier.

### Scénarios nominaux

```gherkin
Étant donné un créneau sur le Ti Kap, de 12 places, avec 9 places vendues
Quand un client consulte ce créneau
Alors 3 places disponibles lui sont affichées
Quand il demande 4 places
Alors la réservation est refusée
Quand il demande 3 places et que son paiement est confirmé
Alors le créneau affiche 0 place disponible aux autres clients
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | demande égale au nombre exact de places restantes | acceptée |
| 2 | exactement 6 inscrits au contrôle des 24 heures | sortie maintenue |
| 3 | 5 inscrits au contrôle des 24 heures | sortie annulée, chaque client remboursé intégralement |
| 4 | inscriptions portant le total à 7 après une annulation pour seuil non atteint | sans effet : la sortie annulée n'est pas rétablie |
| 5 | deux réservations concurrentes sur la dernière place | une seule aboutit, l'autre est refusée, cf. `architecture.md` §5 |
| 6 | sortie baleines déjà engagée sur un bateau, demande sur l'autre bateau au même créneau | refusée : un seul naturaliste |
| 7 | sortie baleines sur un bateau et sortie dauphins sur l'autre, au même créneau | acceptées toutes les deux |
| 8 | privatisation comptant moins de 6 participants | maintenue : le seuil de 6 ne s'applique pas, le bateau étant intégralement payé, voir la rubrique suivante |
| 9 | réservation restée en attente de paiement | ne décompte aucune place |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Application du seuil de 6 à une privatisation : le client a énoncé le
  seuil pour une sortie ouverte à la vente. Hypothèse retenue : une
  privatisation n'y est pas soumise, le bateau étant payé en entier.
- Heure exacte du contrôle des 24 heures : hypothèse retenue, contrôle
  automatique déclenché 24 heures pile avant l'heure de départ du créneau.
- Comportement si un client réserve entre le contrôle et le départ :
  hypothèse retenue, les réservations restent possibles jusqu'à l'heure de
  fermeture du créneau, mais ne rétablissent pas une sortie déjà annulée.

### Critères d'acceptation

- [ ] AC-1 : une demande supérieure au nombre de places restantes est
      refusée, adultes et enfants confondus.
- [ ] AC-2 : une demande égale au nombre de places restantes est acceptée.
- [ ] AC-3 : deux réservations concurrentes visant la dernière place ne
      peuvent pas aboutir toutes les deux.
- [ ] AC-4 : un créneau comptant moins de 6 inscrits au contrôle des
      24 heures est annulé et chaque client est remboursé intégralement.
- [ ] AC-5 : un créneau comptant exactement 6 inscrits au contrôle est
      maintenu.
- [ ] AC-6 : une seconde sortie baleines sur le même créneau est refusée.
- [ ] AC-7 : le nombre de places affiché à un client diminue après la
      confirmation du paiement d'un autre client, sans rechargement manuel.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le seuil de 6 inscrits appliqué à une privatisation annulerait une sortie intégralement payée | acceptée | cas limite 8 et hypothèse d'équipe ajoutés |
| « À partir de 6 inscrits » ne disait pas si 6 exactement suffit | acceptée | cas limites 2 et 3, AC-4 et AC-5 ajoutés sur les deux côtés du seuil |
| Rien ne disait si une réservation non payée bloque une place | acceptée | cas limite 9 ajouté, cohérent avec le décompte au paiement confirmé |
| Réserver temporairement les places dès la validation du formulaire | refusée | cela créerait une durée de rétention de panier que le client n'a jamais évoquée, et un risque de places bloquées par des paniers abandonnés en pleine saison |

## SPEC-BOOKING-04 - Fermeture des réservations en ligne selon le créneau

**Exigences :**

- `REQ-005` : fermeture à midi le jour même pour 14h, la veille à midi pour 7h et 10h.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le créneau de 14h n'est plus réservable en ligne à partir de midi le jour du
départ, et les créneaux de 7h et 10h ne le sont plus à partir de midi la
veille du départ.

> Une demande pour le créneau de 14h du 20 juillet est acceptée à 11h59 le
> 20 juillet, refusée à 12h00.

### Portée

Couvre l'heure limite de réservation en ligne. Ne couvre ni les
modifications, ni les annulations, ni les reports après cette heure.

- Ne couvre pas le report d'une réservation existante, organisé par
  téléphone, cf. la portée du domaine CANCEL.
- Ne couvre pas l'annulation météo d'un créneau, possible à tout moment :
  `SPEC-CANCEL-02`.
- Ne couvre pas le contrôle du seuil de 6 inscrits, déclenché 24 heures avant
  le départ : `SPEC-BOOKING-03`.

### Scénarios nominaux

```gherkin
Étant donné le créneau de 14h du 20 juillet
Quand un client valide et paie sa réservation le 20 juillet à 11h30
Alors la réservation est confirmée
Quand un autre client tente de réserver ce créneau le 20 juillet à 12h30
Alors le créneau ne lui est plus proposé
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | demande pour le créneau de 14h à 12h00 pile | refusée : la fermeture est effective à midi |
| 2 | demande pour le créneau de 7h à 11h59 la veille | acceptée |
| 3 | demande pour le créneau de 10h à 12h00 pile la veille | refusée |
| 4 | formulaire validé avant midi, paiement confirmé après midi | refusée, sans débit : la limite s'apprécie à la confirmation du paiement |
| 5 | client appelant le gérant après la fermeture | aucune réservation possible : le gérant n'a pas d'écran de saisie |
| 6 | heure de référence | heure locale de l'exploitation, voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Fuseau horaire de référence : jamais explicité par le client. Hypothèse
  retenue : l'heure locale du lieu d'exploitation fait foi, pour la
  fermeture comme pour le contrôle des 24 heures et l'envoi des rappels.
- Durée maximale du tunnel de paiement : non discutée. Hypothèse retenue :
  un paiement qui aboutit après l'heure de fermeture est refusé, le client
  n'étant jamais débité pour une sortie qu'il ne peut plus rejoindre.

### Critères d'acceptation

- [ ] AC-1 : le créneau de 14h n'est plus réservable à partir de 12h00 le
      jour du départ.
- [ ] AC-2 : les créneaux de 7h et 10h ne sont plus réservables à partir de
      12h00 la veille du départ.
- [ ] AC-3 : une réservation validée avant la fermeture mais payée après est
      refusée et le client n'est pas débité.
- [ ] AC-4 : un créneau fermé n'apparaît plus dans les créneaux proposés.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « L'heure dépasse midi » laissait 12h00 pile indéterminé | acceptée | cas limites 1 et 3, AC-1 et AC-2 formulés en « à partir de 12h00 » |
| Le moment d'appréciation de la limite, validation ou paiement, n'était pas fixé | acceptée | cas limite 4 et AC-3 ajoutés |
| Aucun fuseau horaire de référence n'est écrit alors que toutes les règles sont horaires | acceptée | hypothèse ajoutée et rendue transverse aux règles horaires du domaine |
| Autoriser une réservation tardive validée manuellement par le gérant | refusée | contredit la règle client selon laquelle toute nouvelle réservation passe par le site, et rouvrirait la saisie manuelle que le projet supprime |

## SPEC-BOOKING-05 - Privatisation d'un bateau

**Exigences :**

- `REQ-006` : privatisation d'un bateau entier sur un créneau.
- `REQ-014` : forfaits de 600 € pour le Ti Kap et 1 100 € pour Le Grand Bleu.

**Statut :** revue IA faite
**Version :** v1

### Règle

Une privatisation bloque un bateau entier sur le créneau choisi, au forfait
propre à ce bateau, sans tarif préférentiel par personne.

> Une privatisation du Ti Kap sur le créneau de 10h est facturée 600 €, quel
> que soit le nombre de participants, et aucune place de ce bateau n'est plus
> vendue sur ce créneau.

### Portée

Couvre la réservation d'un bateau entier et son effet sur la disponibilité.
Ne couvre ni le tarif par personne, ni les suppléments à bord.

- Ne couvre pas la tarification standard par personne : `SPEC-BOOKING-06`.
- Ne couvre pas la saisie ou la modification des forfaits :
  `SPEC-ADMIN-02`.
- Ne couvre pas les suppléments personnalisables, vendus par téléphone,
  cf. la portée du domaine en tête de fichier.
- Ne couvre pas le forfait d'un bateau créé après coup : `SPEC-ADMIN-05`.

### Scénarios nominaux

```gherkin
Étant donné le Ti Kap libre sur le créneau de 10h du 20 juillet
Quand un client réserve une privatisation de ce bateau sur ce créneau
Et que son paiement de 600 € est confirmé
Alors le bateau entier est bloqué sur ce créneau
Et aucune place individuelle n'y est plus proposée
Et Le Grand Bleu reste réservable sur le même créneau
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | privatisation demandée sur un bateau où des places sont déjà vendues au même créneau | refusée |
| 2 | place individuelle demandée sur un bateau déjà privatisé au même créneau | refusée |
| 3 | privatisation d'une sortie baleines | acceptée, et elle consomme le naturaliste du créneau : aucune autre sortie baleines n'est possible sur ce créneau |
| 4 | nombre de participants supérieur à la capacité du bateau privatisé | refusé : la capacité s'applique aussi à une privatisation |
| 5 | privatisation à moins de 6 participants | acceptée : le seuil de maintien ne s'y applique pas, cf. `SPEC-BOOKING-03` |
| 6 | privatisation demandée sur une demi-journée plutôt que sur un départ | voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Périmètre horaire d'une privatisation : le client parle de privatisation
  « le matin (brunch) » ou « l'après-midi (coucher de soleil) », alors que
  l'offre compte trois départs fixes. Une privatisation du matin bloque-t-elle
  le départ de 7h, celui de 10h, ou les deux ? Hypothèse retenue : elle
  bloque un seul créneau, celui choisi par le client. Question à poser au
  prochain entretien.
- Prestation associée au brunch et au coucher de soleil : évoquée par le
  client sans contenu ni supplément tarifaire. Hypothèse retenue : aucune
  prestation supplémentaire n'est vendue en ligne.

### Critères d'acceptation

- [ ] AC-1 : une privatisation confirmée bloque toutes les places du bateau
      sur le créneau choisi.
- [ ] AC-2 : le montant facturé est le forfait du bateau, indépendant du
      nombre de participants.
- [ ] AC-3 : une place individuelle demandée sur un bateau privatisé au même
      créneau est refusée.
- [ ] AC-4 : une privatisation demandée sur un bateau portant déjà des places
      vendues au même créneau est refusée.
- [ ] AC-5 : le second bateau reste réservable sur le même créneau.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « Le créneau choisi (matin ou après-midi) » est incompatible avec trois départs fixes : le périmètre bloqué est ambigu | acceptée | cas limite 6, hypothèse explicite et question au client ajoutées |
| Le cas d'une privatisation demandée sur un créneau déjà partiellement vendu n'était pas traité | acceptée | cas limite 1 et AC-4 ajoutés |
| Le lien entre privatisation et naturaliste unique n'était pas écrit | acceptée | cas limite 3 ajouté |
| Appliquer une réduction au-delà d'un certain nombre de participants | refusée | le client a explicitement exclu tout tarif préférentiel sur la privatisation |

## SPEC-BOOKING-06 - Tarification standard par type de sortie

**Exigences :**

- `REQ-012` : deux formules, forfait standard et privatisation.
- `REQ-014` : 65 € et 40 € en baleines, 50 € et 30 € en dauphins.
- `REQ-015` : tarif enfant de 4 à 11 ans, adulte à partir de 12 ans.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le montant d'une réservation standard est la somme du tarif adulte multiplié
par le nombre d'adultes et du tarif enfant multiplié par le nombre d'enfants,
pour le type de sortie choisi.

> Une sortie baleines pour 2 adultes et 1 enfant est facturée
> 2 × 65 € + 1 × 40 €, soit 170 €.

### Portée

Couvre le calcul du montant dû pour une réservation standard. Ne couvre ni
les forfaits, ni les réductions, ni le paiement.

- Ne couvre pas le forfait de privatisation : `SPEC-BOOKING-05`.
- Ne couvre pas la modification des tarifs : `SPEC-ADMIN-02`.
- Ne couvre pas la déduction d'un bon cadeau : `SPEC-BOOKING-09`.
- Ne couvre pas la déduction d'un code d'avoir : `SPEC-BOOKING-10`.
- Ne couvre pas l'encaissement : `SPEC-BOOKING-07`.

### Scénarios nominaux

```gherkin
Étant donné une réservation standard pour une sortie baleines
Et 2 adultes et 1 enfant déclarés
Quand le montant est calculé
Alors il est de 170 €
Étant donné la même composition pour une sortie dauphins
Quand le montant est calculé
Alors il est de 130 €
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | 0 enfant déclaré | montant calculé sur les seuls adultes |
| 2 | répartition adulte/enfant erronée par le client | non détectable : la répartition est déclarative, aucun âge n'étant collecté |
| 3 | tarif modifié entre la validation du formulaire et le paiement | le montant présenté à la validation est celui facturé, cf. `SPEC-ADMIN-02` |
| 4 | réservation privatisée | hors de ce calcul : forfait par bateau |
| 5 | montant affiché dans la version anglaise du site | même montant, en euros, cf. `SPEC-BOOKING-11` |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Réductions de groupe, tarifs résidents ou promotions : jamais évoqués par
  le client. Hypothèse retenue : aucune réduction en dehors des bons cadeaux
  et des avoirs.
- Arrondis et centimes : tous les tarifs cités sont des montants entiers.
  Hypothèse retenue : les montants restent entiers, sans arrondi à définir.

### Critères d'acceptation

- [ ] AC-1 : une sortie baleines est facturée 65 € par adulte et 40 € par
      enfant déclaré.
- [ ] AC-2 : une sortie dauphins est facturée 50 € par adulte et 30 € par
      enfant déclaré.
- [ ] AC-3 : le montant affiché au récapitulatif est celui exigé au paiement.
- [ ] AC-4 : le montant reste exprimé en euros quelle que soit la langue
      d'affichage.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le lien entre le montant affiché et le montant encaissé n'était pas exigé | acceptée | AC-3 ajouté, seul critère qui protège le client d'un écart entre récapitulatif et débit |
| Le tarif enfant est présenté comme lié à l'âge alors qu'aucun âge n'est collecté | acceptée | cas limite 2 : la répartition est qualifiée de déclarative |
| Aucune règle de devise n'existe alors que le site devient bilingue | acceptée | cas limite 5 et AC-4 ajoutés, cohérents avec l'analyse d'impact qui exclut la multidevise |
| Prévoir une grille de réductions par palier de participants | refusée | aucune demande client, et le seul levier commercial cité est le bon cadeau |

## SPEC-BOOKING-07 - Paiement en ligne intégral par carte

**Exigences :**

- `REQ-017` : totalité du montant en carte bancaire au moment de la réservation.
- `REQ-018` : paiement délégué à un prestataire tiers, aucune donnée sensible stockée.

**Statut :** revue IA faite
**Version :** v1

### Règle

Une réservation n'est confirmée qu'après encaissement en ligne de la totalité
du montant dû, par carte bancaire, auprès du prestataire de paiement.

> Tant que le prestataire n'a pas confirmé la transaction, la réservation
> reste en attente de paiement et ne décompte aucune place.

### Portée

Couvre l'encaissement, l'état de la réservation et le décompte des places qui
en découle. Ne couvre ni le calcul du montant, ni les remboursements.

- Ne couvre pas le calcul du montant dû : `SPEC-BOOKING-06`.
- Ne couvre pas la déduction préalable d'un bon cadeau ou d'un avoir :
  `SPEC-BOOKING-09`, `SPEC-BOOKING-10`.
- Ne couvre pas le remboursement après annulation météo : `SPEC-CANCEL-04`.
- Ne couvre pas le remboursement après seuil de 6 non atteint :
  `SPEC-BOOKING-03`.
- Aucune donnée de carte n'est stockée ni traitée par l'application, cf.
  `docs/adr/ADR-001-stack.md`.

### Scénarios nominaux

```gherkin
Étant donné une réservation en attente de paiement de 170 €
Quand le client règle 170 € par carte bancaire
Et que le prestataire confirme la transaction
Alors la réservation passe à l'état « confirmée »
Et les places correspondantes sont décomptées de la capacité du créneau
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | paiement refusé par la banque | réservation non confirmée, aucune place décomptée |
| 2 | client abandonnant le tunnel de paiement | réservation non confirmée, aucune place décomptée |
| 3 | double soumission du paiement | un seul débit, une seule réservation confirmée |
| 4 | montant dû nul après application d'un bon cadeau couvrant tout le prix | aucun paiement carte n'est demandé, la réservation est confirmée directement |
| 5 | transaction confirmée côté prestataire mais notification perdue | l'état renvoyé par le prestataire fait foi ; à défaut, rapprochement manuel par le gérant |
| 6 | tentative de paiement en plusieurs fois ou par un autre moyen | refusée : paiement intégral par carte uniquement |
| 7 | réservation restée en attente de paiement plusieurs jours | voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Délai d'expiration d'une réservation en attente de paiement : non discuté.
  Hypothèse retenue : expiration automatique après un délai court, à fixer en
  conception, sans effet sur la disponibilité puisque les places ne sont pas
  décomptées avant le paiement.
- Facture remise au client : non tranchée, question 3 du §11 du cahier des
  charges. Hypothèse retenue : justificatif émis par le prestataire de
  paiement et transmis par e-mail.

### Critères d'acceptation

- [ ] AC-1 : la totalité du montant est exigée en carte bancaire, sans
      acompte ni autre moyen de paiement.
- [ ] AC-2 : un paiement refusé ou abandonné laisse la réservation non
      confirmée et ne décompte aucune place.
- [ ] AC-3 : un paiement confirmé fait passer la réservation à l'état
      « confirmée » et décompte les places du créneau.
- [ ] AC-4 : une double soumission ne produit qu'un seul débit.
- [ ] AC-5 : aucune donnée de carte bancaire n'est enregistrée par
      l'application.
- [ ] AC-6 : un montant dû nul confirme la réservation sans passage par le
      paiement carte.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Un bon cadeau couvrant exactement le prix conduit à un paiement de 0 €, cas non traité par une règle de paiement intégral | acceptée | cas limite 4 et AC-6 ajoutés |
| La double soumission du paiement n'était pas traitée alors qu'elle est fréquente sur mobile | acceptée | cas limite 3 et AC-4 ajoutés |
| Rien ne disait ce qu'il advient d'une réservation restée en attente | acceptée | cas limite 7 et hypothèse d'expiration ajoutés |
| Mettre en place une reprise automatique des paiements échoués | refusée | complexité sans demande client, et le gérant conserve le contact téléphonique comme filet |

## SPEC-BOOKING-08 - Accessibilité multi-support

**Exigences :**

- `REQ-035` : site adapté à l'ordinateur, la tablette et le mobile.
- `REQ-101` : utilisable sur les principaux navigateurs, y compris en 4G.

**Statut :** revue IA faite
**Version :** v1

### Règle

Le parcours complet de réservation reste utilisable sur mobile, tablette et
ordinateur, y compris en connexion mobile standard.

> De la consultation des places au paiement, aucun écran n'impose de
> défilement horizontal ni ne rend un élément inaccessible sur un écran de
> téléphone.

### Portée

Couvre l'utilisabilité du parcours client sur les différents supports. Ne
couvre ni l'espace de gestion, ni le design graphique.

- Ne couvre pas l'espace de gestion, consulté depuis un ordinateur :
  `SPEC-ADMIN-01`.
- Ne couvre pas la langue d'affichage : `SPEC-BOOKING-11`.
- Ne couvre pas la charte graphique, absente chez le client, cf. cahier des
  charges §7.

### Scénarios nominaux

```gherkin
Étant donné un client sur un téléphone en connexion 4G
Quand il consulte les places disponibles, remplit le formulaire et paie
Alors le parcours aboutit à une réservation confirmée
Et aucun écran n'a imposé de défilement horizontal
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | écran de 320 pixels de large | parcours utilisable, sans défilement horizontal |
| 2 | passage du mode portrait au mode paysage en cours de saisie | les données déjà saisies sont conservées |
| 3 | connexion mobile lente ou intermittente | le parcours reste utilisable, les états de chargement sont visibles |
| 4 | perte de connexion pendant le paiement | cf. `SPEC-BOOKING-07`, la réservation reste non confirmée |
| 5 | navigateur ancien hors des versions cibles | voir la rubrique suivante |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Liste des navigateurs et versions cibles : non discutée avec le client.
  Hypothèse retenue : les deux dernières versions majeures des navigateurs
  les plus répandus, sur mobile et sur ordinateur.
- Niveau d'accessibilité visé, au sens des référentiels d'accessibilité
  numérique : non demandé. Hypothèse retenue : bonnes pratiques de base,
  sans engagement de conformité formelle.

### Critères d'acceptation

- [ ] AC-1 : le parcours complet aboutit sur mobile, tablette et ordinateur.
- [ ] AC-2 : aucun écran du parcours n'impose de défilement horizontal sur un
      écran de 320 pixels de large.
- [ ] AC-3 : le parcours complet aboutit en connexion mobile 4G.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « Utilisable » et « élément inaccessible » ne sont pas mesurables | acceptée | AC-2 rattaché à une largeur d'écran précise, AC-1 et AC-3 à l'aboutissement du parcours |
| Aucune liste de navigateurs cibles n'existe, l'exigence est donc infalsifiable | acceptée | hypothèse ajoutée dans « Ce qui n'est pas défini » |
| Viser une conformité complète à un référentiel d'accessibilité | refusée | non demandé par le client et hors budget annoncé ; l'engagement se limite aux bonnes pratiques, ce qui est écrit plutôt que sous-entendu |

## SPEC-BOOKING-09 - Achat et usage d'un bon cadeau

**Exigences :**

- `REQ-043` : achat d'un bon cadeau sur la plateforme.
- `REQ-044` : validité d'un an à compter de l'achat.
- `REQ-045` : montant libre, aucun rattachement à un type de sortie.
- `REQ-046` : code saisi au moment de réserver, exclusivement sur la plateforme.
- `REQ-047` : différence payée en carte si le montant total dépasse le bon.
- `REQ-048` : surplus perdu si le bon dépasse le montant total.
- `REQ-049` : usage unique.

**Statut :** revue IA faite
**Version :** v2

### Règle

Un bon cadeau est un code unique portant un montant libre choisi à l'achat,
valable un an, utilisable une seule fois sur la plateforme et déduit du
montant total d'une réservation sans remboursement du surplus.

> Un bon cadeau de 100 € appliqué à une réservation de 170 € laisse 70 € à
> payer par carte ; appliqué à une réservation de 65 €, il est intégralement
> consommé et les 35 € restants sont perdus.

### Portée

Couvre l'achat d'un bon cadeau, sa validité et son application au montant
total dû. Ne couvre ni le calcul de ce montant, ni les avoirs.

- Ne couvre pas le code d'avoir émis après une annulation météo :
  `SPEC-BOOKING-10`.
- Ne couvre pas le calcul du montant de la réservation :
  `SPEC-BOOKING-06`.
- Ne couvre pas l'encaissement du solde : `SPEC-BOOKING-07`.

Le bon cadeau n'impose **aucune** condition sur la réservation à laquelle il
s'applique : ni type de sortie, ni formule, ni composition du groupe. La
règle inverse, en vigueur jusqu'en v3, a été retirée par le client lors de
l'échange oral du 2026-08-13 (`impact-CR-002.md`).

L'exclusivité plateforme, pour l'achat comme pour l'usage, est une hypothèse
d'équipe issue de `CR-03` §6, ambiguïté 1.

### Scénarios nominaux

```gherkin
Étant donné un client qui achète un bon cadeau d'un montant de 100 €
Quand son paiement est confirmé
Alors un code unique lui est délivré
Et ce code expire un an plus tard
Étant donné ce code, non utilisé
Et une réservation de 170 €, quel qu'en soit le type de sortie
Quand le bénéficiaire saisit le code au paiement
Alors 100 € sont déduits
Et 70 € restent à payer par carte bancaire
Et le code ne peut plus servir
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | bon d'un montant exactement égal au montant total | rien à payer par carte, réservation confirmée, cf. `SPEC-BOOKING-07` |
| 2 | bon inférieur au montant total | différence exigée par carte |
| 3 | bon supérieur au montant total | surplus perdu, aucun remboursement ni avoir résiduel |
| 4 | code saisi le jour anniversaire de l'achat | accepté : la validité court jusqu'à la fin du jour anniversaire |
| 5 | code saisi le lendemain du jour anniversaire | refusé |
| 6 | code déjà utilisé une fois | refusé |
| 7 | code saisi sur une réservation baleines, dauphins ou privatisée | accepté dans les trois cas : le bon n'est rattaché à aucun type de sortie |
| 8 | code inexistant ou mal saisi | refusé, sans indiquer si le code existe |
| 9 | bon cadeau et code d'avoir saisis sur la même réservation | refusé : les deux dispositifs ne se cumulent pas |
| 10 | code présenté par téléphone au gérant | refusé : achat et usage passent exclusivement par la plateforme |
| 11 | réservation payée avec un bon cadeau puis annulée pour raison météo | voir la rubrique suivante |
| 12 | tarifs augmentés depuis l'achat du bon | sans effet : le bon vaut son montant, la différence est payée par carte |
| 13 | montant d'achat très faible ou très élevé | voir la rubrique suivante : aucune borne n'a été fixée par le client |

### Ce qui n'est pas défini

Assumé au 2026-08-13, à reposer au client (`CR-03` §8 questions 1 et 3,
cahier des charges §11 questions 8 et 10).

- Bornes du montant d'achat : le client a demandé un montant libre sans
  fixer de minimum, de maximum ni de pas d'arrondi. Hypothèse retenue :
  montant entier compris entre 10 € et 1 100 €, borne haute alignée sur le
  forfait de privatisation le plus élevé.
- Sort d'un bon cadeau consommé sur une réservation ensuite annulée pour
  raison météo : le client n'a pas envisagé le cas. Hypothèse retenue : le
  gérant délivre un code d'avoir d'un montant équivalent, faute de pouvoir
  rembourser un moyen de paiement qui n'est pas de l'argent.
- Cumul avec un code d'avoir : non abordé par le client. Hypothèse retenue :
  dispositifs mutuellement exclusifs. Depuis la v4 du cahier des charges,
  cette exclusion n'est plus étayée par une différence de comportement entre
  les deux dispositifs.
- Usage exceptionnel par téléphone : hypothèse d'équipe de l'exclusion
  stricte, à confirmer.

### Critères d'acceptation

- [ ] AC-1 : l'achat confirmé d'un bon cadeau délivre un code unique portant
      le montant choisi par l'acheteur et daté d'une expiration à un an.
- [ ] AC-2 : un code valide et non utilisé déduit son montant du montant
      total d'une réservation, quel que soit son type de sortie.
- [ ] AC-3 : lorsque le montant total dépasse le montant du bon, la
      différence est exigée par carte bancaire.
- [ ] AC-4 : lorsque le montant du bon dépasse le montant total, aucun
      remboursement ni avoir résiduel n'est produit.
- [ ] AC-5 : un code déjà utilisé est refusé.
- [ ] AC-6 : un code dont l'expiration est dépassée est refusé.
- [ ] AC-7 : aucun écran de l'achat ne demande de choisir un type de sortie
      ni une catégorie de tarif.
- [ ] AC-8 : un bon cadeau et un code d'avoir ne peuvent pas être appliqués à
      la même réservation.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le prix d'achat d'un bon cadeau n'est défini nulle part, ce qui rend le formulaire d'achat non spécifiable | acceptée | tranché en v2 : le montant est libre, la question 9 du §11 du cahier des charges est close |
| Une réservation payée par bon cadeau puis annulée pour météo n'a pas de règle de remboursement | acceptée | cas limite 11 et hypothèse de l'avoir équivalent ajoutés ; le point n'était couvert ni par la spécification d'annulation, ni par celle du paiement |
| « Valable 1 an » ne dit pas si le jour anniversaire est inclus | acceptée | cas limites 4 et 5 ajoutés |
| Un message distinguant « code inexistant » de « code déjà utilisé » facilite le sondage de codes | acceptée | cas limite 8 aligné sur un refus indifférencié |
| Un montant libre sans borne autorise un bon de 3 € comme de 5 000 € | acceptée | cas limite 13 et hypothèse de bornage ajoutés en v2, question 10 ouverte au §11 du cahier des charges |
| Le bon cadeau et l'avoir ayant désormais les mêmes règles, les fusionner en un seul dispositif | refusée pour l'instant | la fusion est défendable et tracée en `impact-CR-002.md` §8, mais elle relève d'une décision client, pas d'une simplification unilatérale ; deux dispositifs distincts restent l'hypothèse en vigueur tant que la question 8 du §11 est ouverte |
| Créditer le surplus non consommé sous forme d'avoir | refusée | le client a explicitement dit que le surplus est perdu sans remboursement ; la spécification suit la règle métier même si elle est défavorable au bénéficiaire |

## SPEC-BOOKING-10 - Saisie d'un code d'avoir au paiement

**Exigences :**

- `REQ-050` : avoir délivré sous forme de code de réduction unique saisi au paiement.
- `REQ-051` : validité d'un an à compter de la date d'émission.

**Statut :** revue IA faite
**Version :** v2

### Règle

Un code d'avoir, émis par le gérant à la suite d'une annulation météo, est
valable un an à compter de son émission et déduit une seule fois du montant
total d'une réservation future, quel que soit le type de sortie.

> Un avoir de 130 € appliqué à une réservation de 170 € laisse 40 € à payer
> par carte bancaire, et le code ne peut plus servir.

### Portée

Couvre l'application d'un code d'avoir au paiement d'une réservation et sa
durée de validité. Ne couvre ni la décision d'accorder un avoir, ni son
montant.

- Ne couvre pas la décision d'accorder un avoir ni son enregistrement :
  `SPEC-CANCEL-04`.
- Ne couvre pas le bon cadeau, dispositif distinct : `SPEC-BOOKING-09`.
- Ne couvre pas l'encaissement du solde : `SPEC-BOOKING-07`.

Depuis la v4 du cahier des charges, un avoir ne se distingue plus d'un bon
cadeau que par **son origine** : il est accordé par le gérant après une
annulation météo, alors que le bon cadeau est vendu. Montant libre, validité
d'un an, usage unique, imputation sur le montant total et perte du surplus
sont désormais identiques pour les deux dispositifs. Le maintien de deux
dispositifs séparés est une hypothèse d'équipe, tracée en question 8 du §11
du cahier des charges.

### Scénarios nominaux

```gherkin
Étant donné un code d'avoir de 130 €, émis il y a trois mois et non utilisé
Et une réservation dauphins de 170 €
Quand le client saisit le code au moment de payer
Alors 130 € sont déduits du montant dû
Et 40 € restent à payer par carte bancaire
Et le code est marqué comme utilisé
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | avoir inférieur au montant total | différence exigée par carte |
| 2 | avoir supérieur au montant total | surplus perdu, aligné sur le traitement du bon cadeau |
| 3 | avoir exactement égal au montant total | rien à payer par carte, réservation confirmée |
| 4 | code déjà utilisé | refusé |
| 5 | code saisi sur un type de sortie différent de la sortie annulée à l'origine | accepté : un avoir n'est rattaché à aucun type de sortie |
| 6 | code d'avoir et bon cadeau sur la même réservation | refusé : dispositifs non cumulables |
| 7 | avoir émis puis créneau de remplacement annulé à son tour | un nouvel avoir est émis par le gérant, avec une nouvelle date d'émission et donc une nouvelle échéance, cf. `SPEC-CANCEL-04` |
| 8 | code saisi le jour anniversaire de l'émission | accepté : la validité court jusqu'à la fin du jour anniversaire, aligné sur le bon cadeau |
| 9 | code saisi le lendemain du jour anniversaire | refusé |
| 10 | avoir sur le point d'expirer | voir la rubrique suivante : aucun rappel n'est prévu |

### Ce qui n'est pas défini

Assumé au 2026-08-13, à reposer au client (cahier des charges §11,
question 8).

- Information du client sur l'expiration de son avoir : le client a fixé la
  durée sans dire si un rappel doit être envoyé. Hypothèse retenue : aucun
  rappel automatique, la date d'expiration figure sur le message qui
  communique le code.
- Fractionnement d'un avoir sur plusieurs réservations : non abordé.
  Hypothèse retenue : usage unique, comme le bon cadeau, le surplus étant
  perdu.
- Distinction définitive entre avoir et bon cadeau : hypothèse d'équipe de
  deux dispositifs séparés, désormais fondée sur la seule origine du code.

### Critères d'acceptation

- [ ] AC-1 : un code d'avoir valide déduit son montant du montant total dû.
- [ ] AC-2 : lorsque le montant total dépasse le montant de l'avoir, la
      différence est exigée par carte bancaire.
- [ ] AC-3 : un code d'avoir déjà utilisé est refusé.
- [ ] AC-4 : un code d'avoir est accepté quel que soit le type de sortie
      réservé.
- [ ] AC-5 : un code d'avoir et un bon cadeau ne peuvent pas être appliqués à
      la même réservation.
- [ ] AC-6 : un code d'avoir émis il y a plus d'un an est refusé.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le traitement du surplus d'un avoir n'était pas écrit, alors qu'il l'est pour le bon cadeau | acceptée | cas limite 2 ajouté, aligné sur le bon cadeau et signalé comme hypothèse |
| Aucune durée de validité n'est fixée : le code pourrait ressurgir des années plus tard | acceptée | tranché en v2 par le client : validité d'un an, cas limites 8 et 9 et critère AC-6 ajoutés |
| La différence entre avoir et bon cadeau n'était pas explicite dans la spécification elle-même | acceptée | portée réécrite en v2 : il ne reste qu'un seul critère distinctif, l'origine du code |
| Un avoir expirant à un an peut pénaliser un client dont l'entreprise a annulé la sortie | acceptée comme risque, non corrigée | la règle est désormais une demande explicite du client (2026-08-13) ; le risque est tracé en `impact-CR-002.md` §8 plutôt que corrigé unilatéralement |
| ~~Fixer une durée de validité d'un an par symétrie avec le bon cadeau~~ | *refusée en v1, devenue sans objet en v2* | le motif du refus était l'absence de règle client ; le client a depuis demandé cette durée |

## SPEC-BOOKING-11 - Parcours de réservation bilingue français et anglais

**Exigences :**

- `REQ-040` : consultation et réservation en français ou en anglais.
- `REQ-102` : aucun contenu non traduit dans l'une des deux langues.

**Statut :** revue IA faite
**Version :** v1

### Règle

L'intégralité du parcours de réservation est disponible en français et en
anglais, au choix du client, le français s'appliquant par défaut.

> Un client qui choisit l'anglais voit en anglais les places disponibles, le
> formulaire, le récapitulatif et l'écran de paiement, sans aucun contenu
> resté en français.

### Portée

Couvre la langue du parcours de réservation client. Ne couvre ni l'espace de
gestion, ni la devise.

- Ne couvre pas la langue du reste du site et des messages automatiques,
  traitée comme exigence transverse : `SPEC-NFR-02`.
- Ne couvre pas le message de rappel envoyé avant la sortie :
  `SPEC-CANCEL-05`.
- Ne couvre pas l'espace de gestion, utilisé par le seul gérant
  francophone : `SPEC-ADMIN-01`.
- Ne couvre pas la devise : les montants restent en euros, cf.
  `docs/impact-CR-001.md` §9.

### Scénarios nominaux

```gherkin
Étant donné un client sur le site
Quand il choisit la langue anglaise
Alors les places disponibles, le formulaire et le paiement s'affichent en anglais
Et aucun libellé ne reste en français
Quand un autre client n'exprime aucun choix de langue
Alors le site s'affiche en français
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | navigateur configuré en anglais, sans choix explicite du client | français par défaut : aucune détection automatique n'a été demandée |
| 2 | changement de langue en cours de saisie du formulaire | les données déjà saisies sont conservées |
| 3 | message d'erreur de validation d'un champ | traduit comme le reste du parcours |
| 4 | confirmation de réservation envoyée au client | rédigée dans la langue choisie lors de la réservation |
| 5 | montants et dates | montants en euros dans les deux langues, dates au format de la langue affichée |
| 6 | demande d'une troisième langue | hors périmètre : deux langues livrées |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Détection automatique de la langue du navigateur : non demandée.
  Hypothèse retenue : choix manuel uniquement, français par défaut.
- Traduction des contenus saisis par le gérant, par exemple un nom de
  bateau : hypothèse retenue, ils restent tels quels dans les deux langues.

### Critères d'acceptation

- [ ] AC-1 : le parcours complet, de la consultation des places au paiement,
      s'affiche en anglais lorsque l'anglais est choisi.
- [ ] AC-2 : aucun libellé du parcours ne reste en français en version
      anglaise.
- [ ] AC-3 : en l'absence de choix, le parcours s'affiche en français.
- [ ] AC-4 : un changement de langue en cours de saisie ne fait pas perdre
      les données déjà renseignées.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Le comportement d'un changement de langue en cours de saisie n'était pas défini | acceptée | cas limite 2 et AC-4 ajoutés |
| Les messages d'erreur et les e-mails de confirmation n'étaient pas explicitement couverts | acceptée | cas limites 3 et 4 ajoutés, cohérents avec l'effet de bord relevé dans l'analyse d'impact |
| « Sans contenu resté en français » n'est vérifiable que si le périmètre est borné | acceptée | AC-2 borné au parcours de réservation, le reste du site relevant de l'exigence transverse |
| Détecter la langue du navigateur pour l'appliquer automatiquement | refusée | le client a demandé un site en deux langues, pas une détection ; le français par défaut est le comportement le plus prévisible pour le gérant qui accompagne ses clients au téléphone |
