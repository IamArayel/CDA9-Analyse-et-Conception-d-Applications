# Architecture - Ti Baleine

**Version :** v3 - 2026-08-17 (J6)
**Décisions associées :** `adr/ADR-001-stack.md`, `adr/ADR-002-persistance.md`
(MySQL confirmé contre le MLD réel), `adr/ADR-003-concurrence-derniere-place.md`
(immobilisation des places), `adr/ADR-004-envoi-des-sms.md`,
`adr/ADR-005-horloge-injectable.md`
**Modèle de données :** `mcd-mld.md`, diagrammes `uml/mcd.puml` et `uml/mld.puml`

Décrit comment le système est organisé et pourquoi. Chaque choix se rattache
à une exigence (`REQ`) ou à une contrainte du client.

**Alignement.** Cette version décrit le cahier des charges **v5** et les
spécifications qui en découlent. La v1 et la v2, écrites à J5, ont intégré
l'alerte météo, l'envoi par SMS et l'immobilisation des places. La v3 ajoute
les deux décisions prises à J6 : la plateforme d'envoi retenue en `ADR-004`
et l'accès au temps tranché en `ADR-005`.

---

## 1. Vue d'ensemble

Une application web unique, servie par Symfony, qui expose deux parcours
sans rapport l'un avec l'autre : un site public de réservation, accessible
sans compte, et un espace de gestion protégé par le compte unique du gérant.
Le paiement est intégralement délégué à Stripe, qui reste le seul système à
manipuler une donnée bancaire. Trois traitements tournent sans utilisateur
devant l'écran : le contrôle du seuil de 6 inscrits à 24 heures du départ,
l'envoi des messages programmés (rappel, alerte météo, confirmation
d'annulation) et la libération des places immobilisées non payées.

```text
   Client (mobile, tablette, ordinateur)      Gérant (ordinateur)
              |                                      |
        site public                          espace de gestion
              \                                      /
               \______  application Symfony  _______/
                              |        \
                        MySQL (Doctrine) \____ Stripe (paiement, remboursement)
                              |               \___ envoi d'e-mails et de SMS
                     tâches planifiées
       (seuil J-24h, messages programmés, places libérées)
```

## 2. Couches

| Couche | Responsabilité | Ce qu'elle a le droit d'appeler | Ce qui n'a rien à y faire |
|---|---|---|---|
| Interface (`Controller`, Twig, formulaires) | recevoir une requête, valider la forme des données, afficher | Application | règle métier, requête SQL, appel à Stripe |
| Application (services de cas d'usage) | orchestrer un cas d'usage, ouvrir et fermer la transaction | Domaine, Infrastructure | règle métier, gabarit HTML |
| Domaine (entités, politiques, services de domaine) | les règles métier, et elles seules | rien | framework, Doctrine, HTTP, **lecture de l'heure système** |
| Infrastructure (repositories Doctrine, client Stripe, envoi d'e-mails et de SMS, planificateur) | persistance et systèmes extérieurs | services externes | règle métier, décision de cas d'usage |

La colonne de droite est celle qui sert en revue : c'est elle qui permet de
dire si une portion de code générée est à sa place. Deux exemples concrets de
ce qu'elle interdit : un contrôleur qui calcule un montant, et une entité
Doctrine qui appelle Stripe.

Le sens des dépendances est unique, de l'interface vers le domaine. Le
domaine ne connaît ni Doctrine ni Symfony : il reçoit des données et rend des
décisions, ce qui le rend testable sans base de données.

**Le temps est une donnée, pas un service ambiant** (`ADR-005`). Le domaine ne
lit jamais l'heure système. Les traitements déclenchés sans utilisateur
reçoivent une horloge injectée ; les calculs purs reçoivent un instant en
paramètre. Un appel direct à l'heure système dans le domaine est un défaut de
revue, au même titre qu'une requête SQL dans un contrôleur. Sans cette règle,
aucune des huit règles horaires du projet n'est testable : vérifier
l'expiration d'un bon cadeau à un an imposerait d'attendre un an.

## 3. Où vivent les règles métier

| Règle | Spécification | Où elle est implémentée |
|---|---|---|
| La capacité d'un bateau n'est jamais dépassée | `SPEC-BOOKING-03` | `Domaine\Politique\Capacite`, appelée par `Application\CreerReservation` dans la transaction qui immobilise les places |
| Deux réservations concurrentes sur la dernière place | `SPEC-BOOKING-03` | `Application\CreerReservation`, verrou pessimiste sur la ligne `sortie` au moment d'immobiliser les places, voir §5 |
| Immobilisation des places pendant 15 minutes | `SPEC-BOOKING-03` | `Domaine\Politique\Immobilisation`, évaluée à la lecture ; `Application\Tache\LibererLesPlacesEchues` pour l'entretien |
| Alerte météo et messages programmés | `SPEC-CANCEL-06` | `Application\MettreEnAlerte` pour la décision, `Application\Tache\EnvoyerLesMessagesProgrammes` pour les envois |
| Remboursement intégral après annulation par le gérant | `SPEC-CANCEL-04` | `Application\AnnulerCreneau`, qui déclenche le remboursement auprès de l'infrastructure de paiement |
| Un seul bateau engagé en sortie baleines par créneau | `SPEC-BOOKING-03` | contrainte d'unicité en base (`sortie.creneau_baleines`), traduite en refus métier par `Infrastructure\Persistance\SortieRepository` |
| Sortie maintenue à partir de 6 inscrits, contrôle à 24 heures | `SPEC-BOOKING-03` | `Application\Tache\ControlerSeuilDeMaintien`, tâche planifiée |
| Saison des sorties baleines et jours de fermeture | `SPEC-BOOKING-02`, `SPEC-ADMIN-04` | `Domaine\Politique\OffreDeCreneaux` |
| Fermeture des réservations selon le créneau | `SPEC-BOOKING-04` | `Domaine\Politique\FermetureDesReservations` |
| Calcul du montant d'une réservation | `SPEC-BOOKING-06` | `Domaine\Service\CalculDuMontant` |
| Application d'un bon cadeau ou d'un avoir, non-cumul, surplus perdu | `SPEC-BOOKING-09`, `SPEC-BOOKING-10` | `Domaine\Service\ApplicationDunCode`, doublée de la contrainte `CHECK` du §5 |
| Montant figé à la réservation malgré un changement de tarif | `SPEC-ADMIN-02` | `Domaine\Service\CalculDuMontant`, dont le résultat est recopié sur la réservation |
| Annulation météo décidée par le gérant, jamais automatique | `SPEC-CANCEL-02` | `Application\AnnulerCreneau`, appelée uniquement depuis l'espace de gestion |
| Message de rappel avant la sortie | `SPEC-CANCEL-05` | `Application\Tache\EnvoyerLeRappel`, tâche planifiée |
| Règle de complexité du mot de passe | `SPEC-ADMIN-01` | contrainte de validation Symfony sur le formulaire de mot de passe |
| Accès au temps | `ADR-005` | `Domaine\Horloge` en interface, `Infrastructure\Horloge\HorlogeSysteme` en production, horloge figée en test |

## 4. Arborescence applicative

```text
src/
├── Domaine/              sans framework, sans Doctrine, sans heure système
│   ├── Entite/           les treize entités, en PHP nu
│   ├── Politique/        les règles pures : saison, fermeture, seuil, codes,
│   │                     composition, coordonnées, immobilisation, envois
│   ├── Service/          CalculDuMontant
│   └── *.php             les trois ports, les énumérations, les exceptions,
│                         les résultats et les vues
├── Application/          un service par cas d'usage
│   ├── Envoi/            ce qui est commun aux trois messages automatiques
│   └── Tache/            les traitements déclenchés sans utilisateur
├── Infrastructure/
│   ├── Horloge/          la seule classe qui lit l'heure système
│   ├── Notification/     l'adaptateur d'envoi
│   ├── Paiement/         l'adaptateur du prestataire
│   └── Persistance/      les dépôts Doctrine
└── Interface/            contrôleurs, formulaires, gabarits Twig

config/doctrine/          la correspondance entre entités et tables, en XML
translations/             les catalogues français et anglais
```

Le rangement suit la couche, pas la fonctionnalité : on trouve une règle en
sachant de quelle nature elle est, et une revue peut vérifier d'un coup d'œil
qu'aucun appel ne remonte les couches.

**La correspondance objet-relationnel est en XML, hors de `src/`.** C'est la
conséquence directe de la ligne « Doctrine » dans la colonne « ce qui n'a rien
à y faire » du §2 : des attributs sur les entités mettraient l'ORM dans le
domaine. Le prix à payer est un fichier de mapping par entité ; le gain est
qu'une entité du domaine reste lisible et testable sans framework.

**`Interface/` n'est pas encore écrite** à J8 : les cas de test entrent par la
couche Application, et la couche de présentation n'a donc pas de raison
d'exister avant qu'un écran ne soit demandé. C'est déclaré dans
`docs/traceability-trous.md`.

## 5. Modèle de données

Le schéma complet est décrit dans `mcd-mld.md` et dessiné dans
`uml/mld.puml`. Ne figurent ici que les points qui ont une conséquence
architecturale.

> La concurrence sur la dernière place disponible (`SPEC-BOOKING-03`) est
> traitée par une transaction unique, ouverte **à la validation du
> formulaire** et non plus à l'encaissement (`ADR-003`), qui verrouille la
> ligne `sortie` (`SELECT ... FOR UPDATE`), recompte les places prises,
> immobilisations non échues comprises, puis écrit la réservation avec sa
> date d'expiration ; elle est garantie par le fait que la vérification et
> l'écriture ne peuvent pas être séparées par une autre transaction.

- **Unicité portée par la base, pas par le code.** La règle du naturaliste
  unique est un index unique sur une colonne générée : deux réservations
  simultanées ne peuvent pas engager deux bateaux en sortie baleines sur le
  même créneau, même sous course.
- **Non-cumul d'un bon cadeau et d'un avoir** : contrainte `CHECK` sur
  `reservation`, doublée d'une vérification applicative pour produire un
  message clair au client.
- **Usage unique d'un code** : contrainte d'unicité sur les clés étrangères
  `bon_cadeau_id` et `avoir_id`, ce qui rend un second usage impossible même
  en cas de double soumission.
- **L'expiration d'une immobilisation est évaluée à la lecture** : une
  réservation échue ne compte plus dans les places prises, même si aucune
  tâche n'est encore passée. Une panne du planificateur ne bloque donc aucune
  vente.
- **Aucune donnée dérivée stockée** : le nombre de places restantes est
  toujours recalculé. La volumétrie attendue (`SPEC-NFR-01`) ne justifie pas
  un compteur dénormalisé, qui se désynchroniserait.
- **Migrations versionnées** avec Doctrine Migrations : aucun schéma modifié
  à la main, y compris en production.

## 6. Services externes

| Service | Usage | Ce qui se passe s'il est indisponible |
|---|---|---|
| Stripe | encaissement, remboursement, justificatif au client | Aucune réservation ne peut être confirmée : le parcours s'arrête avant l'écriture, aucune place n'est décomptée et le client voit un message explicite. Les réservations déjà confirmées ne sont pas affectées. Un remboursement décidé par le gérant est mis en attente et rejoué |
| Envoi d'e-mails | rappel, alerte météo, confirmation d'annulation, confirmation de réservation | Le message n'est pas envoyé, l'échec est enregistré dans `notification`, et l'autre canal part quand même |
| Envoi de SMS | mêmes messages, canal que le client lit en premier | Idem. Depuis la v5 le gérant ne téléphone plus : un message perdu sur les deux canaux n'est rattrapé par personne, ce que le client assume |

`ADR-004` retient **un seul prestataire pour les deux canaux**, ce qui unifie la trace des envois mais crée un point de défaillance commun : une panne de la plateforme prive l'application du SMS et de l'e-mail à la fois. `SPEC-CANCEL-05` AC-6 ne couvre qu'un échec unitaire, un numéro invalide par exemple, pas une panne globale.
| Hébergement Hostinger (MySQL, cron) | persistance et tâches planifiées | Le site est indisponible : ni réservation, ni espace de gestion. Le gérant bascule sur le téléphone, comme avant le projet |

La colonne de droite n'est pas facultative : le client annule des sorties
pour cause de météo et doit joindre des gens déjà en route.

## 7. Sécurité et contrôle d'accès

| Rôle | Ce qu'il peut faire | Ce qu'il ne peut pas faire |
|---|---|---|
| Visiteur | consulter les créneaux, les places restantes et les tarifs | réserver sans passer par le formulaire et le paiement |
| Client | remplir une réservation, payer, utiliser un bon cadeau ou un avoir | accéder à une autre réservation que la sienne, annuler ou reporter en ligne (`REQ-019`, `REQ-020`, hors périmètre) |
| Gérant | tout l'espace de gestion : tarifs, planning, horaires, création d'un bateau, annulation d'un créneau | modifier une réservation existante, saisir une réservation, modifier un bateau existant |

Il n'existe **aucun compte client** : le parcours de réservation est anonyme
jusqu'au paiement, ce qui supprime toute gestion de mot de passe côté public
et réduit la surface de risque. Le seul compte de l'application est celui du
gérant (`SPEC-ADMIN-01`), avec une empreinte de mot de passe et jamais un mot
de passe en clair. Aucune donnée bancaire ne transite par l'application
(`SPEC-BOOKING-07`).

## 8. Déploiement

Hébergement mutualisé Hostinger, environ 2,99 € par mois (`ADR-001`), avec
une base MySQL et des tâches planifiées par le cron de l'hébergeur. Le
déploiement est manuel, effectué par l'équipe, migrations Doctrine
appliquées avant la mise en ligne.

Ni la fréquence des mises à jour, ni l'existence d'un environnement de
recette, ni le responsable de la mise en ligne n'ont été discutés avec le
client : c'est écrit comme tel dans `SPEC-NFR-05`, qui reste au statut
brouillon, et la question est portée à l'ordre du jour du prochain entretien.

## 9. Ce que cette architecture ne fait pas

Les limites assumées, et à partir de quel moment elles deviendraient
gênantes.

- **Elle ne monte pas en charge horizontalement.** Un hébergement mutualisé
  suffit à quelques dizaines de réservations simultanées (`SPEC-NFR-01`).
  Au-delà, le verrou pessimiste du §5 deviendrait le point de contention.
- **Elle ne garantit aucune disponibilité.** Aucun taux n'a été négocié avec
  le client, et l'offre retenue n'en permet pas.
- **Elle ne garantit pas la délivrance d'un message.** Les envois sont
  historisés dans `notification`, mais aucune relance ni canal de repli n'est
  prévu, et le gérant ne téléphone plus. Un client dont les deux coordonnées
  sont erronées ne sera prévenu de rien, ce que le client a explicitement
  assumé.
- **Elle immobilise des places sans les vendre.** Une place peut rester
  indisponible quinze minutes après un panier abandonné, en pleine saison.
  C'est le prix assumé de `ADR-003`.
- **Elle n'isole pas un environnement de recette.** Toute correction est
  poussée directement en production, ce qui reste tenable sur un site à un
  seul utilisateur de gestion, et ne le serait plus en pleine saison.
- **Elle ne prévoit aucune reprise après incident au-delà de la sauvegarde
  hebdomadaire** de l'hébergeur. Une panne le samedi d'un week-end de juillet
  coûterait les réservations de la semaine.
