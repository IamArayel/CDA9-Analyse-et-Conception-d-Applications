# Journal de projet — équipe `<NOM>`

Une entrée par jour, remplie au créneau 16h15. Aucune rubrique ne reste vide sans
justification.

Ce document est la seule trace de ce que vous avez **refusé** à l'IA, et de ce que
vos **acceptations ont changé**. Deux des trois questions obligatoires de la
présentation de J10 y trouvent leur réponse — ou n'en trouvent pas.

Une critique acceptée qui n'a rien changé est une acceptation fictive. À J9, une
autre équipe ira le vérifier dans votre dépôt.

---

## Gabarit d'entrée

```markdown
## J<n> — <date>

**Présents.** …

**Décisions.**
- …

**Critiques de l'IA acceptées.**
- <ce qu'elle a signalé> → <ce que nous avons changé> — <fichier ou sha court>

**Critiques de l'IA refusées, et pourquoi.**
- <ce qu'elle a signalé> → refusé, car <raison métier ou de conception>

**Erreurs produites par l'IA et détectées.**
- <ce qu'elle a produit> → <comment nous l'avons repéré> → <correction>

**Ce qui a été généré aujourd'hui.**
- <fichiers ou portions> — commits <sha courts>

**Questions ouvertes pour le client.**
- …
```

Le rattachement de la ligne « acceptées » à un fichier ou un commit n'est pas
décoratif : c'est ce qui permet de distinguer un arbitrage d'un acquiescement.

---

## J1 — <date>

**Présents.**

**Décisions.**

**Critiques de l'IA acceptées.**
- Aucune : l'IA n'intervient pas en J1.

**Critiques de l'IA refusées, et pourquoi.**
- Sans objet.

**Erreurs produites par l'IA et détectées.**
- Sans objet.

**Ce qui a été généré aujourd'hui.**
- Rien.

**Questions ouvertes pour le client.**
-

## J3 — 2026-08-12

**Présents.** …

**Décisions.**
- Troisième entretien client mené (jours de fermeture, langues, message de
  rappel, taille minimale d'une réservation, mécanique de l'avoir, création
  d'un bateau, et une contrainte nouvelle apportée par le client : les bons
  cadeaux) — consigné dans `compte-rendu-entretien-03.md`.
- Analyse d'impact `impact-CR-001.md` remplie avant toute modification des
  specs, conformément à l'ordre imposé par le README (cahier des charges →
  specs → UML → modèle de données → tests → code).
- Cahier des charges passé en v3 : `REQ-001` corrigée (suppression du
  minimum de 2 personnes), `REQ-025`/`REQ-030`/`REQ-033`/`REQ-102`
  modifiées, `REQ-038` à `REQ-050` ajoutées.
- `specs/booking.md`, `specs/admin.md`, `specs/cancel.md`,
  `specs/non-fonctionnel.md`, `uml/domain.puml` et `uml/use-cases.puml` mis
  à jour en conséquence ; `traceability.md` régénéré.

**Critiques de l'IA acceptées.**
- Sans objet aujourd'hui : le travail du jour a été produit par l'IA sous
  supervision, pas de revue croisée d'un travail d'équipe préexistant.

**Critiques de l'IA refusées, et pourquoi.**
- Sans objet.

**Erreurs produites par l'IA et détectées.**
- Premier jet de `compte-rendu-entretien-03.md` : trois ambiguïtés (§6)
  laissées non tranchées faute de réponse client explicite. L'équipe a
  demandé de retenir une lecture précise pour chacune (bon cadeau exclu du
  téléphone ; formulaire de création d'un bateau limité à nom + capacité ;
  avoir et bon cadeau comme dispositifs distincts) → le compte rendu a été
  réécrit pour documenter ces trois lectures comme des **hypothèses
  d'équipe**, explicitement non confirmées par le client, plutôt que comme
  des ambiguïtés purement ouvertes — impact répercuté sur `REQ-041` et
  `REQ-046` (cahier des charges) et sur `§8` (questions à reposer au
  prochain entretien).
- En croisant plusieurs `specs/*.md`, des références croisées du type
  « `specs/booking.md` (`SPEC-BOOKING-03`) » écrites au milieu d'une
  section d'un autre domaine faussaient la génération de
  `traceability.md` : `tools/traceability.sh` associe une exigence au
  *dernier* identifiant `SPEC-xxx-nn` rencontré dans le fichier, y compris
  quand ce n'est qu'une mention en prose et non un titre de section. Des
  exigences d'un domaine se retrouvaient donc attribuées à une spec d'un
  autre domaine (ex. `REQ-025` rattachée à `SPEC-BOOKING-11` au lieu de
  `SPEC-NFR-02`) → détecté en comparant la matrice régénérée à un calcul
  manuel des paires SPEC/REQ, corrigé en reformulant les renvois
  inter-domaines sans répéter l'identifiant `SPEC-xxx-nn` en dehors de son
  propre titre de section.

**Ce qui a été généré aujourd'hui.**
- `docs/compte-rendu-entretien-03.md` (nouveau)
- `docs/impact-CR-001.md` (rempli)
- `docs/cahier-des-charges.md` (v2 → v3)
- `specs/booking.md`, `specs/admin.md`, `specs/cancel.md`,
  `specs/non-fonctionnel.md` (mis à jour)
- `docs/uml/domain.puml`, `docs/uml/use-cases.puml` (mis à jour)
- `docs/traceability.md` (régénéré)

**Questions ouvertes pour le client.**
- Les quatre questions du §8 de `compte-rendu-entretien-03.md` (usage
  téléphonique exceptionnel d'un bon cadeau, champs du formulaire de
  création d'un bateau, distinction avoir/bon cadeau, prix d'achat d'un
  bon cadeau).
- Les questions déjà en attente au §11 du cahier des charges (budget,
  format de facture, durée de conservation des données, modalités de
  connexion à l'espace de gestion).
