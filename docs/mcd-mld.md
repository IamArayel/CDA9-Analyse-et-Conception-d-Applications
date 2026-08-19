# Modèle de données - MCD et MLD

**Version :** v2 - 2026-08-14 (J5)
**Dérivé de :** `docs/uml/domain.puml` (v5), `specs/*.md`,
`docs/cahier-des-charges.md` (v5)
**Décisions associées :** `adr/ADR-001-stack.md` (Symfony/PHP, Doctrine,
MySQL), `adr/ADR-002-persistance.md`
**Diagrammes :** `docs/uml/mcd.puml` (conceptuel), `docs/uml/mld.puml` (logique)

Ce document descend le diagramme de domaine vers un schéma de données. Il ne
crée aucune règle : chaque entité, chaque attribut et chaque contrainte se
rattache à une spécification. Ce qui ne s'y rattache pas n'existe pas ici.

**Alignement.** Ce modèle reflète le cahier des charges **v5** et les
spécifications qui en découlent. La v1, écrite le matin même, s'arrêtait à la
v4 et isolait les changements attendus dans une rubrique dédiée ; ils sont
désormais intégrés, et le [§9](#9-ce-que-la-v5-a-changé) récapitule ce qui a
bougé.

---

## 1. Conventions de notation

- **Clé primaire** en gras, `#` devant une clé étrangère.
- `U` signale une contrainte d'unicité, seule ou composée.
- Les noms de tables et de colonnes reprennent le vocabulaire du client,
  en minuscules et sans accent, conformément aux conventions Doctrine.
- Les montants sont en euros, en décimal exact, jamais en flottant.

## 2. Règles de gestion retenues

Elles servent d'entrée au MCD. Chacune cite la spécification qui la porte.

| # | Règle de gestion | Spécification |
|---|---|---|
| RG-01 | Un bateau porte un nom unique et une capacité en places | `SPEC-ADMIN-05` |
| RG-02 | Une sortie est programmée sur un seul créneau et affectée à un seul bateau | `SPEC-BOOKING-02` |
| RG-03 | Un même bateau ne peut effectuer qu'une sortie par créneau | `SPEC-BOOKING-03` |
| RG-04 | Un seul bateau à la fois peut être engagé en sortie baleines sur un créneau | `SPEC-BOOKING-03` |
| RG-05 | Une réservation porte sur une seule sortie et compte au moins un participant | `SPEC-BOOKING-01` |
| RG-06 | Une réservation déclarant des enfants compte au moins un adulte | `SPEC-BOOKING-01` |
| RG-07 | Le montant d'une réservation est figé à sa création et ne suit pas les évolutions de tarif | `SPEC-ADMIN-02` |
| RG-08 | Un bon cadeau porte un code unique, un montant libre et une expiration à un an | `SPEC-BOOKING-09` |
| RG-09 | Un avoir porte un code unique, un montant décidé par le gérant et une expiration à un an | `SPEC-BOOKING-10` |
| RG-10 | Un bon cadeau comme un avoir ne servent qu'une fois, sur une seule réservation | `SPEC-BOOKING-09`, `SPEC-BOOKING-10` |
| RG-11 | Un bon cadeau et un avoir ne se cumulent pas sur une même réservation | `SPEC-BOOKING-09` |
| RG-12 | Les tarifs adulte et enfant dépendent du type de sortie | `SPEC-BOOKING-06` |
| RG-13 | Le forfait de privatisation dépend du bateau, pas du type de sortie | `SPEC-BOOKING-05` |
| RG-14 | Aucun créneau n'est programmé un jour déclaré fermé | `SPEC-ADMIN-04` |
| RG-15 | L'espace de gestion n'a qu'un compte, celui du gérant | `SPEC-ADMIN-01` |
| RG-16 | L'horaire d'envoi du message de rappel est réglable | `SPEC-CANCEL-05` |
| RG-17 | Le client choisit la langue de son parcours, le français par défaut | `SPEC-BOOKING-11` |
| RG-18 | Une sortie est programmée, en alerte météo, ou annulée | `SPEC-CANCEL-06` |
| RG-19 | Les places d'une réservation sont immobilisées 15 minutes le temps du paiement | `SPEC-BOOKING-03` |
| RG-20 | Chaque message envoyé à un client laisse une trace, avec son type, son canal et sa date | `SPEC-CANCEL-04` |
| RG-21 | Le choix entre report, avoir et remboursement n'existe que pour une annulation demandée par le client | `SPEC-ADMIN-06` |

## 3. MCD - entités et propriétés

| Entité | Propriétés | Identifiant | Origine |
|---|---|---|---|
| BATEAU | nom, capacité, forfait de privatisation | nom | `SPEC-ADMIN-05`, `SPEC-BOOKING-05` |
| CRENEAU | date, heure de départ | date + heure | `SPEC-BOOKING-02` |
| SORTIE | type de sortie, formule, statut, date de mise en alerte | identifiant technique | `SPEC-BOOKING-02`, `SPEC-BOOKING-03`, `SPEC-CANCEL-06` |
| RESERVATION | nom, prénom, e-mail, mobile, nombre d'adultes, nombre d'enfants, montant, langue, statut, date de création, fin d'immobilisation | identifiant technique | `SPEC-BOOKING-01`, `SPEC-BOOKING-07` |
| NOTIFICATION | type, canal, date d'envoi, statut | identifiant technique | `SPEC-CANCEL-05`, `SPEC-CANCEL-06` |
| BON_CADEAU | code, montant, date d'achat, date d'expiration, statut | code | `SPEC-BOOKING-09` |
| AVOIR | code, montant, date d'émission, date d'expiration, statut | code | `SPEC-BOOKING-10` |
| CHOIX_ANNULATION | type, date d'enregistrement | identifiant technique | `SPEC-ADMIN-06` |
| TARIF | type de sortie, prix adulte, prix enfant | type de sortie | `SPEC-BOOKING-06`, `SPEC-ADMIN-02` |
| JOUR_FERMETURE | date, récurrence annuelle | date | `SPEC-ADMIN-04` |
| PARAMETRE | heure d'ouverture, heure de fermeture, délai du message de rappel, heure d'envoi de l'alerte, délai de confirmation | identifiant technique | `SPEC-ADMIN-04`, `SPEC-CANCEL-05`, `SPEC-CANCEL-06` |
| GERANT | e-mail, mot de passe | e-mail | `SPEC-ADMIN-01` |

## 4. MCD - associations et cardinalités

| Association | Entité A | Cardinalité A | Entité B | Cardinalité B |
|---|---|---|---|---|
| programmer | CRENEAU | 0,n | SORTIE | 1,1 |
| affecter | BATEAU | 0,n | SORTIE | 1,1 |
| recevoir | SORTIE | 0,n | RESERVATION | 1,1 |
| réduire | BON_CADEAU | 0,1 | RESERVATION | 0,1 |
| réduire | AVOIR | 0,1 | RESERVATION | 0,1 |
| notifier | RESERVATION | 0,n | NOTIFICATION | 1,1 |
| donner lieu à | RESERVATION | 0,1 | CHOIX_ANNULATION | 1,1 |
| matérialiser | CHOIX_ANNULATION | 0,1 | AVOIR | 0,1 |

TARIF, JOUR_FERMETURE, PARAMETRE et GERANT sont des entités de référence,
sans association : elles sont lues par l'application, jamais rattachées à une
réservation. Le §5 explique pourquoi TARIF, en particulier, n'est relié à
rien.

## 5. Choix de modélisation arbitrés

**Deux tables distinctes pour `bon_cadeau` et `avoir`.** Les deux dispositifs
portent aujourd'hui les mêmes colonnes d'usage, code, montant, expiration et
statut, et le cahier des charges pose la question de leur fusion (§11,
question 8, restée sans réponse). Nous les gardons séparés :

- leurs origines diffèrent, l'un est **vendu** et donne lieu à un paiement,
  l'autre est **accordé** par le gérant sans contrepartie financière ;
- leurs dates de départ diffèrent, achat contre émission, et une table
  unique imposerait une colonne discriminante et des colonnes nulles selon
  le cas ;
- la question n'est pas tranchée par le client : deux tables préservent les
  deux options, alors qu'une table fusionnée préjuge de sa réponse ;
- la séparation est réversible à peu de frais, une vue ou une union suffit à
  les lire ensemble, tandis que la fusion, elle, détruit l'information
  d'origine.

Coût accepté : la logique de code de réduction est écrite une fois et
appliquée à deux tables, ce qui est du ressort du service applicatif, pas du
schéma.

**Le forfait de privatisation est porté par `bateau`, pas par `tarif`.** Le
client tarife la privatisation par bateau, 600 € pour le Ti Kap et 1 100 €
pour Le Grand Bleu, alors que les prix adulte et enfant dépendent du type de
sortie. Les loger dans la même table obligerait à laisser vides deux colonnes
sur trois selon la ligne. La colonne est **nullable**, ce qui porte
directement `SPEC-ADMIN-05` AC-5 : un bateau créé sans forfait n'est pas
proposé à la privatisation.

**`reservation` ne référence pas `tarif`.** Le montant est figé à la création
de la réservation (`SPEC-ADMIN-02` AC-2 et AC-4). Une clé étrangère vers le
tarif ferait varier a posteriori le montant d'une réservation payée dès que
le gérant modifie sa grille. Le tarif est donc lu au moment du calcul, puis
oublié.

**Le non-cumul est une contrainte de table, pas une règle applicative
seulement.** `reservation` porte deux clés étrangères nullables, chacune
unique, et une contrainte `CHECK` interdit qu'elles soient renseignées
ensemble. L'unicité de chaque clé porte l'usage unique d'un code.

**`choix_annulation` est rattaché à la réservation, pas à la sortie.** Le
diagramme de domaine le rattache à `Sortie`, ce qui ne permet pas de savoir
quel client a choisi quoi. Le choix est propre à chaque client : la relation
correcte part de la réservation. Correction de modélisation, sans effet sur
les exigences.

**`creneau` et `sortie` restent deux tables.** Un créneau est un horaire
proposé, une sortie est la prestation qui y est rattachée pour un bateau et
un type donné. Les fusionner rendrait impossible de représenter deux bateaux
naviguant sur le même créneau, ce que `SPEC-BOOKING-03` exige.

## 6. MLD

```text
BATEAU (id, nom, capacite, forfait_privatisation)
    U : nom

CRENEAU (id, date_creneau, heure_depart)
    U : (date_creneau, heure_depart)

SORTIE (id, #creneau_id, #bateau_id, type_sortie, formule, statut,
        date_alerte, creneau_baleines)
    U : (creneau_id, bateau_id)
    U : creneau_baleines

RESERVATION (id, #sortie_id, #bon_cadeau_id, #avoir_id, nom_client,
             prenom_client, email, telephone_mobile, nombre_adultes,
             nombre_enfants, montant, langue, statut, date_creation,
             expire_le)
    U : bon_cadeau_id
    U : avoir_id

BON_CADEAU (id, code, montant, date_achat, date_expiration, statut,
            email_beneficiaire)
    U : code

AVOIR (id, code, montant, date_emission, date_expiration, statut,
       email_beneficiaire)
    U : code

NOTIFICATION (id, #reservation_id, type, canal, date_envoi, statut)

CHOIX_ANNULATION (id, #reservation_id, #avoir_id, type, date_enregistrement)
    U : reservation_id

TARIF (id, type_sortie, prix_adulte, prix_enfant)
    U : type_sortie

JOUR_FERMETURE (id, date_fermeture, recurrent_annuel)
    U : date_fermeture

PARAMETRE (id, heure_ouverture, heure_fermeture, delai_rappel_heures,
           heure_alerte, delai_confirmation_heures)

GERANT (id, email, mot_de_passe)
    U : email

PREVISION_METEO (id, date_prevision, texte)
    U : date_prevision
```

## 7. MPD - types et contraintes MySQL

| Table | Colonne | Type | Contrainte |
|---|---|---|---|
| `bateau` | `id` | INT UNSIGNED | PK, auto-incrément |
| | `nom` | VARCHAR(60) | NOT NULL, UNIQUE |
| | `capacite` | SMALLINT UNSIGNED | NOT NULL, CHECK > 0 |
| | `forfait_privatisation` | INT | NULL, CHECK > 0, en centimes |
| `creneau` | `date_creneau` | DATE | NOT NULL |
| | `heure_depart` | TIME | NOT NULL |
| `sortie` | `creneau_id` | INT UNSIGNED | FK, NOT NULL |
| | `bateau_id` | INT UNSIGNED | FK, NOT NULL |
| | `type_sortie` | VARCHAR(20) | NOT NULL, CHECK IN (DAUPHINS, BALEINES) |
| | `formule` | VARCHAR(20) | NOT NULL, CHECK IN (STANDARD, PRIVATISATION) |
| | `statut` | VARCHAR(20) | NOT NULL, CHECK IN (PROGRAMMEE, EN_ALERTE, ANNULEE) |
| | `date_alerte` | DATETIME | NULL |
| | `creneau_baleines` | INT UNSIGNED | colonne générée, UNIQUE |
| `reservation` | `sortie_id` | INT UNSIGNED | FK, NOT NULL |
| | `bon_cadeau_id` | INT UNSIGNED | FK, NULL, UNIQUE |
| | `avoir_id` | INT UNSIGNED | FK, NULL, UNIQUE |
| | `nom_client`, `prenom_client` | VARCHAR(80) | NOT NULL |
| | `email` | VARCHAR(180) | NOT NULL |
| | `telephone_mobile` | VARCHAR(20) | NOT NULL |
| | `nombre_adultes`, `nombre_enfants` | TINYINT UNSIGNED | NOT NULL |
| | `montant` | INT | NOT NULL, CHECK >= 0, en centimes |
| | `langue` | CHAR(2) | NOT NULL, défaut `fr` |
| | `statut` | VARCHAR(30) | NOT NULL, CHECK IN (EN_ATTENTE_PAIEMENT, CONFIRMEE, ANNULEE) |
| | `date_creation` | DATETIME | NOT NULL |
| | `expire_le` | DATETIME | NOT NULL, index avec `sortie_id` et `statut` |
| `notification` | `reservation_id` | INT UNSIGNED | FK, NOT NULL |
| | `type` | VARCHAR(30) | NOT NULL, CHECK IN (RAPPEL, ALERTE, CONFIRMATION_ANNULATION) |
| | `canal` | VARCHAR(10) | NOT NULL, CHECK IN (SMS, EMAIL) |
| | `date_envoi` | DATETIME | NOT NULL |
| | `statut` | VARCHAR(20) | NOT NULL, CHECK IN (ENVOYE, ECHEC) |
| `bon_cadeau` | `code` | VARCHAR(16) | NOT NULL, UNIQUE |
| | `montant` | INT | NOT NULL, CHECK > 0, en centimes |
| | `date_achat` | DATETIME | NOT NULL |
| | `date_expiration` | DATE | NOT NULL |
| | `statut` | VARCHAR(20) | NOT NULL, CHECK IN (ACTIF, CONSOMME, EXPIRE) |
| `avoir` | `code` | VARCHAR(16) | NOT NULL, UNIQUE |
| | `montant` | INT | NOT NULL, CHECK > 0, en centimes |
| | `date_emission` | DATETIME | NOT NULL |
| | `date_expiration` | DATE | NOT NULL |
| | `statut` | VARCHAR(20) | NOT NULL, CHECK IN (ACTIF, CONSOMME, EXPIRE) |
| `choix_annulation` | `reservation_id` | INT UNSIGNED | FK, NOT NULL, UNIQUE |
| | `avoir_id` | INT UNSIGNED | FK, NULL |
| | `type` | VARCHAR(20) | NOT NULL, CHECK IN (REPORT, AVOIR, REMBOURSEMENT) |
| `tarif` | `type_sortie` | VARCHAR(20) | NOT NULL, UNIQUE |
| | `prix_adulte`, `prix_enfant` | INT | NOT NULL, CHECK > 0, en centimes |
| `jour_fermeture` | `date_fermeture` | DATE | NOT NULL, UNIQUE |
| | `recurrent_annuel` | BOOLEAN | NOT NULL, défaut faux |
| `parametre` | `heure_ouverture`, `heure_fermeture` | TIME | NOT NULL |
| | `delai_rappel_heures` | SMALLINT UNSIGNED | NOT NULL, défaut 24 |
| | `heure_alerte` | TIME | NOT NULL, défaut 18:00 |
| | `delai_confirmation_heures` | SMALLINT UNSIGNED | NOT NULL, défaut 2 |
| `gerant` | `email` | VARCHAR(180) | NOT NULL, UNIQUE |
| | `mot_de_passe` | VARCHAR(255) | NOT NULL, empreinte, jamais en clair |

Trois contraintes méritent d'être lues en détail.

**`sortie.creneau_baleines`** est une colonne générée valant `creneau_id`
quand `type_sortie = 'BALEINES'` et `NULL` sinon. MySQL ignorant les valeurs
nulles dans un index unique, cet index porte à lui seul la règle « un seul
bateau engagé en sortie baleines par créneau » (`RG-04`), sans code
applicatif et sans risque de course entre deux réservations concurrentes.

**`reservation`** porte `CHECK (bon_cadeau_id IS NULL OR avoir_id IS NULL)`
pour le non-cumul (`RG-11`), et
`CHECK (nombre_adultes + nombre_enfants >= 1 AND (nombre_enfants = 0 OR nombre_adultes >= 1))`
pour `RG-05` et `RG-06`.

**L'immobilisation est évaluée à la lecture.** Une réservation en attente
dont `expire_le` est dépassé ne compte plus dans les places prises, sans
qu'aucune tâche n'ait à passer la supprimer. Le nettoyage périodique n'est
qu'un entretien, jamais une condition de correction : une panne du
planificateur ne bloque aucune vente.

**Les statuts sont des `VARCHAR` contraints**, et non des `ENUM` MySQL :
Doctrine ne gère pas nativement le type `ENUM`, et une valeur nouvelle
imposerait une migration de type plutôt qu'une migration de contrainte.

## 8. Ce que le schéma ne porte pas

Trois règles ne sont pas exprimables en contrainte déclarative et relèvent de
la couche applicative et de la transaction. Elles sont documentées dans
`architecture.md` §5 :

| Règle | Spécification | Pourquoi le schéma ne suffit pas |
|---|---|---|
| La capacité d'un bateau n'est jamais dépassée | `SPEC-BOOKING-03` | Somme des participants d'une sortie, réservations immobilisées non échues comprises ; verrou sur la ligne `sortie` |
| Deux réservations concurrentes sur la dernière place | `SPEC-BOOKING-03` | Même verrou, pris désormais à la validation du formulaire et non à l'encaissement (`ADR-003`) |
| Envoi de l'alerte et de la confirmation aux bonnes heures | `SPEC-CANCEL-06` | Traitement planifié, hors transaction utilisateur |
| Le seuil de 6 inscrits à 24 heures du départ | `SPEC-BOOKING-03` | Contrôle périodique, hors transaction utilisateur |

## 9. Ce que la v5 a changé

Par rapport à la v1 de ce document, écrite le matin même sur le cahier des
charges v4.

| Objet | Changement |
|---|---|
| `sortie.statut` | Nouvelle valeur `EN_ALERTE`, et une colonne `date_alerte` qui horodate la mise en alerte |
| `notification` | Table nouvelle : type, canal, date d'envoi et statut de chaque message. Sans elle, rien ne dit si la confirmation d'annulation doit encore partir |
| `reservation.expire_le` | Fin de l'immobilisation des places, 15 minutes après la validation du formulaire (`ADR-003`) |
| `reservation.telephone_mobile` | Devient une donnée de contact contrôlée, puisqu'elle porte l'envoi des SMS |
| `choix_annulation` | Se rattache aux annulations demandées par le client (`SPEC-ADMIN-06`) et non plus à l'annulation météo |
| `parametre` | Deux colonnes supplémentaires, l'heure d'envoi de l'alerte et le délai de confirmation, réglables par hypothèse d'équipe |

## 10. Ce que l'écriture du code a corrigé - J8

Trois écarts entre ce document et le schéma réellement migré, découverts en
implémentant les spécifications. Ils sont ici et non dans un commentaire de
code, parce que c'est ce document que le formateur lit.

| Objet | Ce que disait le MLD | Ce que dit le schéma | Pourquoi |
|---|---|---|---|
| Tous les montants | `DECIMAL(8,2)` et `DECIMAL(6,2)` | `INT`, en centimes | Doctrine rend un `DECIMAL` sous forme de chaîne PHP, ce qui imposerait une conversion à chaque lecture et à chaque comparaison. Les 76 cas de test raisonnent en centimes, et un entier ne dépend d'aucun arrondi |
| `PREVISION_METEO` | absente | table nouvelle, une prévision par journée | `SPEC-CANCEL-05` AC-2 exige que le message de rappel porte les conditions prévues et les affaires à prévoir, saisies par le gérant. Rien ne les stockait |
| `BON_CADEAU.email_beneficiaire`, `AVOIR.email_beneficiaire` | absentes | `VARCHAR(180)`, nullable | Aucun code ne portait de donnée personnelle, ce qui rendait sans objet l'exception de `SPEC-NFR-04` : la purge des trois mois n'avait rien à épargner sur un bon cadeau vivant |

Le premier écart est un choix de conception, les deux autres sont des oublis du
modèle que seuls les cas de test ont fait apparaître.

## 11. Revue IA

Consigne utilisée :

> Compare ce modèle aux spécifications jointes. Signale les incohérences, les
> responsabilités mal placées, les règles métier absentes du modèle et les
> relations qui ne sont justifiées par aucune spécification. Ne produis pas de
> modèle corrigé.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| `Tarif` regroupait prix adulte, prix enfant et forfait de privatisation, alors que les deux premiers dépendent du type de sortie et le troisième du bateau | acceptée | forfait déplacé sur `bateau`, en colonne nullable, ce qui porte au passage `SPEC-ADMIN-05` AC-5 |
| `ChoixAnnulation` était rattaché à `Sortie` : impossible de savoir quel client a choisi quoi | acceptée | rattaché à `reservation`, avec unicité |
| La règle du naturaliste unique n'était portée par aucune contrainte | acceptée | colonne générée et index unique, plutôt qu'un contrôle applicatif sujet aux courses |
| Le non-cumul bon cadeau et avoir reposait sur du code | acceptée | contrainte `CHECK` ajoutée au schéma |
| Rien ne permettait de savoir si la confirmation d'annulation devait encore partir, ni de répondre à un client affirmant n'avoir rien reçu | acceptée | table `notification` ajoutée en v2, alors même que le client juge la non-délivrance sans objet |
| Une immobilisation dépendant d'une tâche planifiée bloquerait des ventes en cas de panne du planificateur | acceptée | l'expiration est évaluée à la lecture, le nettoyage n'étant qu'un entretien |
| Fusionner `bon_cadeau` et `avoir` en une table unique avec colonne d'origine | refusée | la fusion est défendable techniquement mais préjuge d'une question posée au client et restée sans réponse (§11, question 8) ; deux tables gardent les deux options ouvertes, la fusion détruirait l'information d'origine |
| Stocker le nombre de places restantes sur `sortie` pour éviter un calcul | refusée | donnée dérivée, donc désynchronisable ; le verrou transactionnel décrit au §8 suffit à la volumétrie attendue (`SPEC-NFR-01`) |

Les refus se reportent aussi dans `docs/journal.md`.
