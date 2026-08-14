# Architecture - Ti Baleine

**Version :** v1 - 2026-08-14 (J5)
**Décisions associées :** `adr/ADR-001-stack.md` (adoptée),
`adr/ADR-002-persistance.md` (à rédiger ce jour, confirme MySQL contre le
MLD réel plutôt que contre l'intuition de J2)
**Modèle de données :** `mcd-mld.md`, diagramme `uml/mld.puml`

Décrit comment le système est organisé et pourquoi. Chaque choix se rattache
à une exigence (`REQ`) ou à une contrainte du client.

**Alignement.** Cette version décrit l'architecture du dossier validé, cahier
des charges **v4**. Les effets de l'entretien du 2026-08-14 sont analysés
dans `impact-CR-003.md` et signalés au [§9](#9-ce-que-cette-architecture-ne-fait-pas).

---

## 1. Vue d'ensemble

Une application web unique, servie par Symfony, qui expose deux parcours
sans rapport l'un avec l'autre : un site public de réservation, accessible
sans compte, et un espace de gestion protégé par le compte unique du gérant.
Le paiement est intégralement délégué à Stripe, qui reste le seul système à
manipuler une donnée bancaire. Deux traitements tournent sans utilisateur
devant l'écran : le contrôle du seuil de 6 inscrits à 24 heures du départ et
l'envoi du message de rappel.

```text
   Client (mobile, tablette, ordinateur)      Gérant (ordinateur)
              |                                      |
        site public                          espace de gestion
              \                                      /
               \______  application Symfony  _______/
                              |        \
                        MySQL (Doctrine) \____ Stripe (paiement, remboursement)
                              |               \___ envoi d'e-mails
                     tâches planifiées
              (seuil J-24h, message de rappel)
```

## 2. Couches

| Couche | Responsabilité | Ce qu'elle a le droit d'appeler | Ce qui n'a rien à y faire |
|---|---|---|---|
| Interface (`Controller`, Twig, formulaires) | recevoir une requête, valider la forme des données, afficher | Application | règle métier, requête SQL, appel à Stripe |
| Application (services de cas d'usage) | orchestrer un cas d'usage, ouvrir et fermer la transaction | Domaine, Infrastructure | règle métier, gabarit HTML |
| Domaine (entités, politiques, services de domaine) | les règles métier, et elles seules | rien | framework, Doctrine, HTTP |
| Infrastructure (repositories Doctrine, client Stripe, envoi d'e-mails, planificateur) | persistance et systèmes extérieurs | services externes | règle métier, décision de cas d'usage |

La colonne de droite est celle qui sert en revue : c'est elle qui permet de
dire si une portion de code générée est à sa place. Deux exemples concrets de
ce qu'elle interdit : un contrôleur qui calcule un montant, et une entité
Doctrine qui appelle Stripe.

Le sens des dépendances est unique, de l'interface vers le domaine. Le
domaine ne connaît ni Doctrine ni Symfony : il reçoit des données et rend des
décisions, ce qui le rend testable sans base de données.

## 3. Où vivent les règles métier

| Règle | Spécification | Où elle est implémentée |
|---|---|---|
| La capacité d'un bateau n'est jamais dépassée | `SPEC-BOOKING-03` | `Domaine\Politique\Capacite`, appelée par `Application\ConfirmerReservation` dans la transaction de paiement |
| Deux réservations concurrentes sur la dernière place | `SPEC-BOOKING-03` | `Application\ConfirmerReservation`, verrou pessimiste sur la ligne `sortie`, voir §5 |
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

## 4. Arborescence applicative

```text
src/
├── Domaine/          entités, politiques, services de domaine, sans framework
├── Application/      un service par cas d'usage, plus les tâches planifiées
├── Infrastructure/   repositories Doctrine, client Stripe, envoi d'e-mails
└── Interface/        contrôleurs, formulaires, gabarits Twig
```

Le rangement suit la couche, pas la fonctionnalité : on trouve une règle en
sachant de quelle nature elle est, et une revue peut vérifier d'un coup d'œil
qu'aucun appel ne remonte les couches.

## 5. Modèle de données

Le schéma complet est décrit dans `mcd-mld.md` et dessiné dans
`uml/mld.puml`. Ne figurent ici que les points qui ont une conséquence
architecturale.

> La concurrence sur la dernière place disponible (`SPEC-BOOKING-03`) est
> traitée par une transaction unique, ouverte au moment où Stripe confirme le
> paiement, qui verrouille la ligne `sortie` (`SELECT ... FOR UPDATE`),
> recompte les places vendues, puis écrit la réservation ; elle est garantie
> par le fait que la vérification et l'écriture ne peuvent pas être séparées
> par une autre transaction.

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
- **Aucune donnée dérivée stockée** : le nombre de places restantes est
  toujours recalculé. La volumétrie attendue (`SPEC-NFR-01`) ne justifie pas
  un compteur dénormalisé, qui se désynchroniserait.
- **Migrations versionnées** avec Doctrine Migrations : aucun schéma modifié
  à la main, y compris en production.

## 6. Services externes

| Service | Usage | Ce qui se passe s'il est indisponible |
|---|---|---|
| Stripe | encaissement, remboursement, justificatif au client | Aucune réservation ne peut être confirmée : le parcours s'arrête avant l'écriture, aucune place n'est décomptée et le client voit un message explicite. Les réservations déjà confirmées ne sont pas affectées. Un remboursement décidé par le gérant est mis en attente et rejoué |
| Envoi d'e-mails | message de rappel avant la sortie, confirmation de réservation | Le rappel n'est pas envoyé et l'échec est journalisé. Le gérant conserve le téléphone comme filet, c'est l'état du dossier en v4 ; `CR-05` supprime ce filet, voir §9 |
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
- **Elle ne conserve aucune trace des envois.** Un e-mail parti, non parti ou
  refusé n'est pas historisé, seulement journalisé. C'est acceptable tant que
  le gérant appelle ses clients, et cela cesse de l'être avec `CR-05`, qui
  supprime l'appel : `impact-CR-003` prévoit une table `notification` et un
  canal SMS, à intégrer en v2 après le passage du cahier des charges en v5.
- **Elle n'isole pas un environnement de recette.** Toute correction est
  poussée directement en production, ce qui reste tenable sur un site à un
  seul utilisateur de gestion, et ne le serait plus en pleine saison.
- **Elle ne prévoit aucune reprise après incident au-delà de la sauvegarde
  hebdomadaire** de l'hébergeur. Une panne le samedi d'un week-end de juillet
  coûterait les réservations de la semaine.
