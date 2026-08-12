# Analyse d'impact — CR-001

**Demande du client :** ensemble de réponses et d'une contrainte nouvelle apportées
lors du troisième entretien — voir [`compte-rendu-entretien-03.md`](./compte-rendu-entretien-03.md).
**Reçue le :** 2026-08-12, au fil de l'entretien.
**Rédigée par :** l'équipe, avec revue critique de l'IA.

---

> **Interdiction de modifier le code avant que cette analyse soit complète.**
>
> La modification descend la chaîne dans cet ordre : cahier des charges → specs →
> UML → modèle de données → cas de test → tests → code. Commencer par le code,
> c'est perdre la trace de pourquoi il a changé — et c'est exactement ce que ce
> module cherche à vous faire éviter.

Contrairement au gabarit, ce CR-001 ne porte pas sur une seule demande isolée
mais sur l'ensemble du troisième entretien, reçu en un seul bloc : cinq
réponses qui tranchent des questions déjà ouvertes dans le cahier des
charges v2 (§11), une clarification qui **corrige** une exigence déjà
formalisée (`REQ-001`), et une contrainte entièrement nouvelle introduite
sans avoir été demandée (les bons cadeaux). Aucun code ni test automatisé
n'existe encore dans ce dépôt (`src/`, `tests/cases/` sont vides) : cette
analyse porte donc uniquement sur le cahier des charges, les spécifications
et la conception UML.

---

## 1. Ce que le client demande, reformulé

En langage métier, en trois à cinq phrases. Si la reformulation est floue, posez
les questions avant de descendre la chaîne.

Le client précise cinq points en attente depuis le deuxième entretien : deux
jours de fermeture fixes (25 décembre, 1ᵉʳ janvier) avec un pilotage des
horaires depuis le back-office, un site bilingue français/anglais, un
message de rappel à texte type dont l'horaire d'envoi est réglable par le
gérant, et l'ajout futur d'un bateau piloté depuis l'espace de gestion. Il
corrige aussi une exigence déjà actée : une réservation individuelle peut
porter sur une seule place, et non sur un minimum de deux personnes comme
compris jusqu'ici. Il précise enfin le mécanisme de l'avoir (un code de
réduction saisi au paiement) et introduit, de sa propre initiative, un
besoin entièrement nouveau : la vente de bons cadeaux valables un an,
spécifiques à un type de sortie, à usage unique, utilisables uniquement en
réservant sur la plateforme, avec paiement du différentiel ou perte du
surplus selon le sens de l'écart entre le montant du bon et le prix de la
sortie.

## 2. Questions posées au client

| # | Question | Réponse |
|---|---|---|
| 1 | Jours de fermeture et gestion des horaires depuis l'admin | Fermé le 25/12 et le 1ᵉʳ/01 ; section dédiée sur le dashboard (`CR-03/Q01`) |
| 2 | Langues du site | Français et anglais (`CR-03/Q02`) |
| 3 | Contenu du message de rappel à J-1 | Message type, horaire personnalisable, automatisation via le site (`CR-03/Q03`) |
| 4 | Taille minimale d'une réservation | Une personne seule peut réserver une place unique (`CR-03/Q04`) |
| 5 | Fonctionnement de l'avoir | Code de réduction unique saisi au paiement (`CR-03/Q05`) |
| 6 | Création d'un nouveau bateau depuis l'admin | Oui, si la flotte évolue (`CR-03/Q06`) |
| 7 | *(non posée — introduite par le client)* | Vente de bons cadeaux : voir §1 (`CR-03/Q07`) |

Trois lectures restent des hypothèses d'équipe non confirmées par le client
(réservation du bon cadeau strictement exclue du téléphone ; formulaire de
création d'un bateau limité à nom + capacité ; avoir et bon cadeau comme
deux dispositifs distincts) — voir `CR-03, §6` et `CR-03, §8`. Elles sont
retenues ci-dessous comme `déduit`, pas comme réponse client actée.

## 3. Impact — cahier des charges

| Exigence | Impact | Action |
|---|---|---|
| REQ-001 | modifiée | Retirer le seuil « à partir de 2 personnes » ; une réservation peut porter sur 1 personne. Source complétée avec `CR-03/Q04`. |
| REQ-025 | modifiée | Priorité `Should` → `Must` (contenu et automatisation désormais définis) ; source complétée avec `CR-03/Q03`. |
| REQ-030 | modifiée | La restriction « ni la composition de la flotte » ne vaut plus telle quelle : l'ajout d'un bateau devient possible (`REQ-041`, nouvelle) ; la modification/suppression des bateaux existants et des créneaux reste hors périmètre. |
| REQ-033 | modifiée | Composition de flotte toujours fixe *pour les deux bateaux existants* ; ajout précisé comme possible via `REQ-041`. |
| REQ-102 | modifiée | Français uniquement → français et anglais ; sort du statut `déduit`, sourcée `CR-03/Q02`. |
| REQ-038 *(nouvelle)* | ajoutée | Jours de fermeture fixes (25/12, 1ᵉʳ/01). |
| REQ-039 *(nouvelle)* | ajoutée | Le gérant modifie les horaires et jours de fermeture depuis l'espace de gestion. |
| REQ-040 *(nouvelle)* | ajoutée | Le site est utilisable en français et en anglais, au choix du client. |
| REQ-041 *(nouvelle)* | ajoutée | Le gérant peut créer un nouveau bateau depuis l'espace de gestion. |
| REQ-042 *(nouvelle)* | ajoutée | Le gérant personnalise l'horaire d'envoi du message de rappel. |
| REQ-043 à REQ-049 *(nouvelles)* | ajoutées | Vente, validité, spécificité, saisie, paiement du différentiel, perte du surplus, usage unique du bon cadeau. |
| REQ-050 *(nouvelle)* | ajoutée | L'avoir est délivré sous forme de code de réduction saisi au paiement. |

Le reste des exigences (REQ-002 à REQ-024, REQ-026 à REQ-029, REQ-031,
REQ-032, REQ-034 à REQ-037, REQ-100, REQ-101, REQ-103 à REQ-107) est
**inchangé** : aucune des réponses de CR-03 ne les contredit ni ne les
précise.

## 4. Impact — spécifications

| Spécification | Impact | Ce qui change exactement |
|---|---|---|
| SPEC-BOOKING-01 | modifiée | Retrait de la règle « minimum 2 personnes » et du critère d'acceptation associé ; ajout d'un critère pour la réservation à 1 personne. |
| SPEC-BOOKING-09 *(nouvelle)* | ajoutée | Achat et usage d'un bon cadeau (REQ-043 à REQ-049). |
| SPEC-BOOKING-10 *(nouvelle)* | ajoutée | Saisie d'un code d'avoir au paiement (REQ-050). |
| SPEC-ADMIN-01 | inchangée | La connexion reste un compte unique ; aucune réponse de CR-03 ne touche à l'authentification elle-même. |
| SPEC-ADMIN-03 (hors périmètre) | modifiée | La note « pas de gestion de flotte » ne couvre plus l'ajout d'un bateau ; reformulée pour ne plus citer REQ-033 comme excluant toute évolution de flotte. |
| SPEC-ADMIN-04 *(nouvelle)* | ajoutée | Gestion des horaires d'ouverture et des jours de fermeture (REQ-038, REQ-039). |
| SPEC-ADMIN-05 *(nouvelle)* | ajoutée | Création d'un nouveau bateau depuis l'espace de gestion (REQ-041). |
| SPEC-CANCEL-04 | inchangée | Le choix (report/avoir/remboursement) enregistré par le gérant ne change pas de mécanique ; seule la matérialisation de l'avoir (code de réduction) est désormais définie ailleurs (SPEC-BOOKING-10). |
| SPEC-CANCEL-05 *(nouvelle)* | ajoutée | Message de rappel à J-1 : contenu type et horaire personnalisable (REQ-025, REQ-042) — sort du statut « hors périmètre applicatif » où il était faute de contenu connu. |
| SPEC-NFR-02 | modifiée | Langue française uniquement → français/anglais ; critère de vérification revu. |

Toutes les autres spécifications (`SPEC-BOOKING-02` à `08`, `SPEC-CANCEL-01`
à `03`, `SPEC-ADMIN-02`, `SPEC-NFR-01`, `03` à `06`) sont **inchangées** :
justifié par le fait qu'aucune réponse de CR-03 ne touche à la
tarification, au moteur de créneaux/capacité (hors nouveau bateau, traité
séparément), au paiement standard, à l'annulation météo elle-même, ou aux
questions non fonctionnelles autres que la langue.

## 5. Impact — conception

| Artefact | Impact | Ce qui change |
|---|---|---|
| `uml/domain.puml` | modifié | Nouvelle classe `BonCadeau` (code, typeSortie, montant, dateAchat, dateExpiration, statut) liée à `Réservation` ; nouvelle classe `CodeAvoir` (ou attribut équivalent) liée à `ChoixAnnulation` ; nouvel attribut `horairesOuverture`/jours de fermeture sur un concept d'exploitation (porté par `Gérant` ou une classe `Horaires` dédiée) ; retrait de la note « minimum 2 personnes » sur `Réservation`. |
| `uml/sequences/…` | à créer | Aucun diagramme de séquence n'existe encore (dépôt en amont de J4) ; le futur diagramme « Réserver une sortie » devra intégrer la branche « avec bon cadeau/avoir ». |
| MCD / MLD | non commencé | Pas encore produit dans ce dépôt ; cette analyse sert d'intrant direct pour sa première version (tables `bon_cadeau`, `code_avoir`, et gestion des horaires). |
| `architecture.md` | non rempli | Gabarit encore vide dans ce dépôt ; aucun impact à documenter avant sa première rédaction. |

**État nouveau ou donnée nouvelle ?** Oui, sur deux points : le bon cadeau
est une donnée entièrement nouvelle (code, montant, spécificité de sortie,
expiration à 1 an, statut utilisé/non utilisé) qui n'existait dans aucune
version antérieure du modèle ; les horaires/jours de fermeture éditables
sont également une donnée nouvelle (jusqu'ici, les trois créneaux
quotidiens étaient considérés comme fixes tous les jours de l'année). Ces
deux ajouts ne peuvent pas être absorbés par le code seul : ils doivent
apparaître dans `domain.puml` et le futur MCD/MLD avant toute
implémentation.

## 6. Impact — tests

Aucun cas de test n'existe à ce jour (`tests/cases/` ne contient qu'un
`.gitkeep`) : ce CR n'invalide donc aucun test existant. Il fixe en
revanche le périmètre des premiers `CASE-BOOKING-*`, `CASE-ADMIN-*` et
`CASE-CANCEL-*` à écrire lors du prochain cycle, notamment :

| Cas de test | Impact |
|---|---|
| `CASE-BOOKING-…` *(à écrire)* | réservation à 1 seule personne acceptée |
| `CASE-BOOKING-…` *(à écrire)* | achat d'un bon cadeau, usage avec paiement du différentiel, usage avec perte du surplus, usage après expiration (1 an), tentative de réutilisation |
| `CASE-BOOKING-…` *(à écrire)* | saisie d'un code d'avoir au paiement |
| `CASE-ADMIN-…` *(à écrire)* | modification des horaires/jours de fermeture, création d'un nouveau bateau |
| `CASE-CANCEL-…` *(à écrire)* | envoi du message de rappel à l'horaire personnalisé |

## 7. Impact — code

Aucun composant n'existe encore (`src/` ne contient qu'un `.gitkeep`) :
sans objet pour ce CR. Les futurs composants concernés (à anticiper lors du
découpage de l'architecture) : gestion de créneau/disponibilité (nouveau
bateau), moteur de tarification/paiement (application d'un bon cadeau ou
d'un avoir au montant dû), planificateur de messages (horaire de rappel
personnalisé), gestion du calendrier d'ouverture.

## 8. Effets de bord identifiés

Ce que la demande touche sans que le client l'ait envisagé. C'est la partie qui
distingue une analyse d'impact d'une liste de tâches.

- Le bilinguisme (`REQ-040`, `REQ-102`) touche potentiellement le contenu
  du message de rappel (`REQ-025`) et celui du bon cadeau (reçu par le
  bénéficiaire) : leur contenu type doit lui aussi exister en deux langues,
  ce que le client n'a pas explicitement précisé.
- L'ajout d'un jour de fermeture éditable (`REQ-038`/`REQ-039`) interagit
  avec le moteur de créneaux existant (`SPEC-BOOKING-02`) : un créneau déjà
  réservé sur une date qui deviendrait un jour de fermeture n'a pas de
  règle définie (le client n'a pas anticipé ce cas — fermeture annoncée à
  l'avance vs. fermeture d'une date déjà ouverte à la réservation).
- Le paiement partiel par bon cadeau (différentiel payé en carte bancaire,
  `REQ-047`) introduit, pour la première fois, un paiement **mixte** dans
  le parcours de réservation ; jusqu'ici `REQ-017` posait un paiement
  intégral par un seul moyen. Cela impacte potentiellement l'intégration
  Stripe (`ADR-001`) au-delà de ce que ce CR peut trancher seul.
- Le cumul d'un bon cadeau et d'un code d'avoir sur une même réservation
  n'a pas été abordé par le client (un seul code à la fois, ou cumul
  possible ?) — traité par défaut comme mutuellement exclusifs, hypothèse
  d'équipe à confirmer.
- La création d'un bateau depuis l'admin (`REQ-041`) pose la question,
  restée sans réponse ferme, de son habilitation ou non aux sorties
  baleines (contrainte du naturaliste unique, `REQ-007`) — traité par
  défaut comme habilité à tous les types de sortie, hypothèse d'équipe à
  confirmer (`CR-03, §8, question 3`).

## 9. Ce que nous ne ferons pas dans le temps restant

Assumé, et à annoncer au client lors de la présentation de J10.

- Aucune internationalisation au-delà du contenu textuel du site et des
  messages automatiques (REQ-040) : pas de gestion de devise multiple, le
  tarif reste en euros quelle que soit la langue choisie.
- Aucune interface de définition, par bateau, des types de sorties
  compatibles lors de sa création (`REQ-041`) : tout bateau créé est
  habilité à tous les types de sortie, en cohérence avec les deux bateaux
  existants — sauf confirmation contraire du client.
- Aucun cumul d'un bon cadeau avec un code d'avoir sur une même
  réservation, faute de règle client sur ce cas.
- Aucune gestion du cas d'un créneau déjà réservé qui tomberait sur une
  date de fermeture ajoutée après coup par le gérant (cf. §8) : ce cas
  limite reste, comme le reste des annulations, traité manuellement par le
  gérant hors outil, en l'absence de règle client.

## 10. Ordre d'exécution retenu

| # | Étape | Qui |
|---|---|---|
| 1 | Mettre à jour `docs/cahier-des-charges.md` (v3) : REQ-001 corrigée, REQ-025/030/033/102 modifiées, REQ-038 à REQ-050 ajoutées, §6/§8/§11/§13 mis à jour | équipe |
| 2 | Mettre à jour `specs/booking.md`, `specs/admin.md`, `specs/cancel.md`, `specs/non-fonctionnel.md` en conséquence | équipe |
| 3 | Mettre à jour `docs/uml/domain.puml` (classes `BonCadeau`, avoir/code de réduction, horaires) et `docs/uml/use-cases.puml` (nouveaux cas d'usage) | équipe |
| 4 | Régénérer `docs/traceability.md` via `./tools/traceability.sh` et vérifier l'absence de rupture | équipe |
| 5 | Reposer au client les points restés en hypothèse d'équipe (`CR-03, §8`) avant d'écrire le MCD/MLD définitif | équipe |
| 6 | Écrire les `CASE-*` et les tests correspondants une fois le MCD/MLD stabilisé | IA (production), équipe (revue) |
