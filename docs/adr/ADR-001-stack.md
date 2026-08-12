# ADR-001 — Choix de la stack technique

**Statut :** adopté
**Date :** J2 (2026-08-11) — formalisé J3 (2026-08-12)
**Décidé par :** l'équipe (Chloe Baisse, Arnaud Maxime, Anthony Dégeilh)
**Validation formateur :** requise avant la fin de J2

---

## 1. Contraintes d'admissibilité

- [x] **Déjà pratiquée par au moins deux membres de l'équipe.**
      → Symfony/PHP est pratiqué par les trois membres de l'équipe.
- [x] **Runner de tests exécutable en une commande.**
      → `php bin/phpunit` + **Behat** 
- [x] **Mécanisme de migration ou de schéma versionné.**
      → Doctrine Migrations (bundle standard Symfony/Doctrine ORM)
      
- [x] **Intégration possible d'un prestataire de paiement.**
      → Stripe, via son SDK PHP officiel / API REST + webhooks ; aucune
      donnée de paiement sensible stockée côté application (REQ-018).
- [x] **Déployable dans la contrainte budgétaire du client** (`REQ-103`).
      → Hébergement mutualisé Hostinger, 150 €/48 mois (≈ 2,99 €/mois),
      nom de domaine gratuit la 1ʳᵉ année, 20 Go SSD, sauvegarde
      hebdomadaire gratuite.

## 2. Liste admise

Symfony/PHP · Next.js/TypeScript · Spring Boot/Java · ASP.NET

**Demande de dérogation :** sans objet — Symfony/PHP fait partie de la
liste admise.

## 3. Contexte

Le problème demande : la gestion de données très relationnelles (clients,
réservations, créneaux, sorties, bateaux, tarifs — cf. §5 du cahier des
charges) avec une contrainte de concurrence forte sur la dernière place
disponible d'un créneau (`REQ-002`, `REQ-004`) et sur la disponibilité
d'un seul bateau à la fois pour les sorties baleines (`REQ-007`) ; une
volumétrie faible avec un pic saisonnier (`REQ-100`) ; un paiement en
ligne intégral délégué à un tiers (`REQ-017`, `REQ-018`) sans stockage de
donnée sensible ; un support multi-device en français uniquement
(`REQ-035`, `REQ-101`, `REQ-102`) ; un hébergement à faible coût
(`REQ-103`) ; une maintenance assurée par une équipe de trois personnes
déjà formées à PHP/Symfony, sans possibilité d'apprendre une nouvelle
stack dans le temps du module.

## 4. Options envisagées

### Option A — Symfony/PHP (retenue)

| | |
|---|---|
| Compétences de l'équipe | pratiqué par les 3 membres |
| Ce qu'elle facilite pour ce problème | ORM Doctrine adapté au relationnel et aux contraintes d'unicité (ex. empêcher une double réservation sur le même créneau/place) ; architecture MVC structurante pour séparer règles métier et contrôleurs |
| Ce qu'elle coûte | montée en charge plus lourde à l'écriture qu'un framework plus léger, non pénalisant ici vu `REQ-100` |
| Coût d'hébergement estimé | 2,99 €/mois (Hostinger mutualisé) |
| Ce qu'elle rend difficile plus tard | scaling horizontal sur hébergement mutualisé si la volumétrie dépassait largement `REQ-100` |

### Option B — Next.js/TypeScript (écartée)

| | |
|---|---|
| Compétences de l'équipe | non pratiquée par au moins deux membres — élimine l'option au critère d'admissibilité n°1 |
| Ce qu'elle facilite pour ce problème | — |
| Ce qu'elle coûte | temps d'apprentissage incompatible avec la durée du module (règle « aucune technologie non maîtrisée ») |
| Coût d'hébergement estimé | — |
| Ce qu'elle rend difficile plus tard | — |

*(Spring Boot/Java et ASP.NET écartés pour le même motif : critère
d'admissibilité n°1 non rempli par l'équipe.)*

## 5. Décision

Symfony/PHP + MySQL, paiement délégué à Stripe, hébergement mutualisé
Hostinger.

## 6. Raisons

Les trois développeurs pratiquent déjà Symfony/PHP, ce qui garantit une
régularité de production même en cas d'absence d'un membre de l'équipe.
Symfony impose une architecture MVC qui aide à séparer les règles métier
du reste, et l'ORM Doctrine est pensé pour du relationnel, ce qui limite
les bugs de concurrence (ex. empêcher une double réservation sur le même
créneau). MySQL est retenu comme SGBD pour la nature très relationnelle
des données collectées, à l'exclusion de toute donnée de paiement
sensible, entièrement déléguée à Stripe. Stripe a été retenu après
comparaison avec PayPal et l'offre de paiement en ligne du Crédit
Agricole, sur trois critères : génération automatique de facture pour le
client, coût le plus bas des trois solutions étudiées, conformité aux
souhaits exprimés par le client (`CR-01/Q11`, `CR-02/Q19`).

## 7. Conséquences acceptées

- Hébergement mutualisé : suffisant pour la volumétrie attendue
  (`REQ-100`), mais limite la scalabilité si le trafic dépassait
  largement les hypothèses retenues.
- Architecture Symfony plus structurée qu'un micro-framework : légèrement
  plus lourde à démarrer, compensée par la maîtrise déjà acquise par
  l'équipe.
- Aucune donnée de paiement sensible stockée côté application — toute
  logique de facturation dépend de la disponibilité de Stripe.

## 8. Ce qui nous ferait revenir dessus

- Si le client décidait finalement de gérer le paiement de bout en bout
  (sans prestataire tiers), réévaluer PostgreSQL pour son traitement plus
  strict des données sensibles, à la place de MySQL.
- Si la volumétrie dépassait largement les hypothèses de `REQ-100` (ex.
  ouverture à plusieurs gérants ou salariés), réévaluer l'hébergement
  mutualisé Hostinger.

---

> Le choix de la persistance ne se décide **pas** ici formellement même si
> MySQL est déjà pressenti ci-dessus : `ADR-002-persistance` sera rédigé à
> J5, après la modélisation du domaine (J4) et le MCD/MLD (J5), pour
> confirmer ce choix contre le modèle de données réel plutôt que contre une
> intuition de J2.
