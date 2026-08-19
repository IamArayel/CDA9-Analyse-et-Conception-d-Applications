# ADR-002 - Choix du moteur de persistance

**Statut :** accepté
**Date :** J5 (2026-08-14)
**Décidé par :** l'équipe (Chloe Baisse, Arnaud Maxime, Anthony Dégeilh)
**Complète :** `ADR-001-stack.md`, qui pressentait MySQL sans le décider

---

`ADR-001` a volontairement laissé cette décision ouverte : le moteur devait
être choisi **contre le modèle de données réel**, pas contre une intuition de
J2. Ce modèle existe depuis ce matin (`docs/mcd-mld.md`, `docs/uml/mld.puml`),
la décision peut donc être prise sur des faits.

## Contexte

Le modèle compte onze tables, sept associations et une majorité de clés
étrangères obligatoires : les données sont denses et relationnelles, sans
document ni contenu libre. Trois éléments du modèle pèsent plus que le reste
dans ce choix.

- **La dernière place d'un créneau est un point de contention réel.** Deux
  clients peuvent voir la même place libre et engager tous les deux le
  paiement ; une seule réservation doit aboutir (`REQ-002`, `REQ-004`,
  `SPEC-BOOKING-03` AC-3). Il faut un verrouillage de ligne fiable à
  l'intérieur d'une transaction.
- **Deux règles métier sont portées par des contraintes de schéma** plutôt
  que par du code : le naturaliste unique (`REQ-007`) par un index unique, et
  le non-cumul d'un bon cadeau et d'un avoir (`REQ-046`, `REQ-050`) par une
  contrainte `CHECK`.
- **Les montants doivent être exacts** (`REQ-014`, `REQ-017`) : type décimal,
  jamais flottant.

Deux contraintes extérieures encadrent le choix : l'hébergement mutualisé
Hostinger retenu en `ADR-001` pour le coût (`REQ-103`), qui ne propose que
MySQL ou MariaDB, et la volumétrie faible avec un pic saisonnier
(`REQ-100`), qui n'impose aucune performance particulière.

## Options envisagées

### Option A - MySQL 8 (InnoDB), via Doctrine ORM (retenue)

| | |
|---|---|
| Ce qu'elle apporte | Verrouillage de ligne InnoDB et transactions, ce qui règle la dernière place ; contraintes `CHECK` depuis 8.0.16 ; colonnes générées indexables ; `DECIMAL` exact ; inclus dans l'offre d'hébergement déjà retenue, donc sans coût supplémentaire ; pratiqué par les trois membres de l'équipe |
| Ce qu'elle coûte | Pas d'index partiel : la règle du naturaliste unique passe par une colonne générée `creneau_baleines` plutôt que par un `WHERE` sur l'index, ce qui demande un commentaire dans le schéma pour rester lisible |
| Ce qu'elle rend difficile plus tard | Les contraintes plus expressives (exclusion, index partiels) resteront hors de portée ; un besoin de ce type imposerait de changer de moteur, donc d'hébergeur |

### Option B - PostgreSQL, via Doctrine ORM

| | |
|---|---|
| Ce qu'elle apporte | Index uniques partiels, qui exprimeraient la règle du naturaliste unique sans colonne générée ; contraintes `EXCLUDE` qui exprimeraient nativement le non-chevauchement sur un créneau ; typage plus strict |
| Ce qu'elle coûte | Indisponible sur l'hébergement mutualisé retenu : il faudrait un hébergement dédié ou un service géré, donc sortir de la contrainte de coût qui a fondé `ADR-001` ; deux membres de l'équipe sur trois ne l'ont jamais exploité en production |
| Ce qu'elle rend difficile plus tard | Rien techniquement, mais la décision rouvrirait `ADR-001` et le budget d'hébergement |

### Option C - SQLite, fichier embarqué

| | |
|---|---|
| Ce qu'elle apporte | Coût nul, aucune administration, suffisant pour la volumétrie annoncée |
| Ce qu'elle coûte | Verrou d'écriture global au fichier : toute confirmation de paiement bloque l'ensemble des écritures, y compris celles de l'espace de gestion. La contention de la dernière place serait « résolue » par une sérialisation totale, au prix d'un comportement imprévisible en pic de saison |
| Ce qu'elle rend difficile plus tard | Sauvegarde et restauration à chaud, et toute évolution vers plusieurs utilisateurs simultanés |

Aucune option NoSQL n'a été examinée : le modèle est intégralement
relationnel, sans document, sans contenu libre et sans donnée dénormalisée
(`docs/mcd-mld.md` §5).

## Décision

MySQL 8 avec le moteur InnoDB, accédé par Doctrine ORM, schéma versionné par
Doctrine Migrations, sur l'hébergement mutualisé déjà retenu en `ADR-001`.

## Raisons

Le seul argument technique sérieux en faveur de PostgreSQL est l'expressivité
de ses contraintes. Or les deux règles concernées sont déjà portées de façon
satisfaisante par MySQL : la colonne générée `creneau_baleines` avec index
unique garantit le naturaliste unique aussi sûrement qu'un index partiel, et
la contrainte `CHECK` couvre le non-cumul depuis MySQL 8.0.16. Le gain
résiduel de PostgreSQL est un confort d'écriture du schéma, payé par une
sortie de la contrainte de coût qui a fondé toute la stack et par une
compétence que l'équipe n'a pas.

La contention sur la dernière place, qui est le vrai risque fonctionnel du
projet, se traite identiquement dans les deux moteurs : une transaction qui
verrouille la ligne `sortie`, recompte et écrit. InnoDB le fait depuis
longtemps et l'équipe l'a déjà pratiqué.

SQLite est écarté sur un point précis et non sur un principe : son verrou
d'écriture global transformerait chaque paiement en point de sérialisation de
toute l'application.

## Conséquences acceptées

- La règle du naturaliste unique n'est pas lisible directement dans le
  schéma : elle repose sur une colonne générée, dont le rôle est documenté
  dans `mcd-mld.md` §7 et en note du diagramme `uml/mld.puml`.
- Les contraintes `CHECK` supposent **MySQL 8.0.16 ou plus**, ou MariaDB
  10.2 ou plus. **Vérifié à J8 sur l'environnement de développement, avant la
  première migration : MySQL 9.3.** Le non-cumul et les bornes de participants
  restent donc portés par la base. La version fournie par Hostinger reste à
  contrôler à l'ouverture de l'hébergement : si elle était antérieure, ces
  deux règles redescendraient dans la couche applicative et la conséquence
  serait à écrire dans `architecture.md` §5.
- Le verrouillage pessimiste sérialise les confirmations de paiement d'un
  même créneau. C'est sans effet à la volumétrie attendue (`REQ-100`), et ce
  serait le premier point à mesurer si elle augmentait.
- Les sauvegardes se limitent à la sauvegarde hebdomadaire de l'hébergeur,
  déjà assumée en `ADR-001` et rappelée dans `architecture.md` §9.

## Ce qui nous ferait revenir dessus

- L'hébergement fournit une version de MySQL ou MariaDB sans support des
  contraintes `CHECK` : à mesurer dès l'ouverture de l'hébergement, avant la
  première migration.
- Le client décide de traiter le paiement de bout en bout sans prestataire
  tiers : `ADR-001` prévoit déjà de réévaluer PostgreSQL dans ce cas, pour
  son traitement plus strict des données sensibles.
- L'ouverture de l'outil à plusieurs utilisateurs de gestion, ou une
  volumétrie très supérieure à `REQ-100`, ferait du verrou pessimiste un
  point de contention mesurable.
