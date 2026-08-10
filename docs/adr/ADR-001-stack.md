# ADR-001 — Choix de la stack technique

**Statut :** proposé
**Date :** J2
**Décidé par :** l'équipe
**Validation formateur :** requise avant la fin de J2

À rendre en fin de journée J2. Sans ADR-001 validé, l'équipe ne démarre pas la
conception technique.

---

## 1. Contraintes d'admissibilité

Les cinq sont éliminatoires. Cochez et justifiez ; une case non cochée invalide
le choix.

- [ ] **Déjà pratiquée par au moins deux membres de l'équipe.** Le module
      n'enseigne pas la technologie ; apprendre une stack en 10 jours consommera
      le temps de l'analyse et de la conception.
      → qui, et sur quoi l'ont-ils pratiquée : …
- [ ] **Runner de tests exécutable en une commande.**
      → la commande : `…`
- [ ] **Mécanisme de migration ou de schéma versionné.**
      → lequel : …
- [ ] **Intégration possible d'un prestataire de paiement.**
      → lequel, et sous quelle forme : …
- [ ] **Déployable dans la contrainte budgétaire du client** (`REQ-1xx`).
      → où, et pour combien par mois : …

## 2. Liste admise

Symfony/PHP · Next.js/TypeScript · Spring Boot/Java · ASP.NET

Hors liste : dérogation écrite ci-dessous, accordée ou refusée par le formateur.

**Demande de dérogation :** … *(laisser vide si sans objet)*

## 3. Contexte

Ce que le problème demande réellement : nature des données, transactions,
concurrence sur les places, volumétrie et pics, support d'usage, conditions
réseau, langues, maintenance après livraison. Citer les `REQ` concernées.

## 4. Options envisagées

### Option A — …

| | |
|---|---|
| Compétences de l'équipe | … |
| Ce qu'elle facilite pour ce problème | … |
| Ce qu'elle coûte | … |
| Coût d'hébergement estimé | … €/mois |
| Ce qu'elle rend difficile plus tard | … |

### Option B — …

*(même grille)*

## 5. Décision

…

## 6. Raisons

Argumenter à partir du problème et des contraintes, pas à partir d'une préférence
générale. « C'est plus moderne » n'est pas une raison. « Le client n'a aucune
compétence technique et personne pour maintenir (`REQ-1xx`), donc nous choisissons
ce que deux d'entre nous savons déjà exploiter » en est une.

## 7. Conséquences acceptées

- …

## 8. Ce qui nous ferait revenir dessus

- …

---

> Le choix de la persistance ne se décide **pas** ici. Il fait l'objet de
> `ADR-002-persistance`, après la modélisation du domaine en J4 et le MCD/MLD en
> J5 — parce qu'on ne peut pas choisir un modèle de stockage avant de savoir ce
> qu'on stocke.
