# Passation — Interface Ti Baleine (Symfony 8 + Twig)

## Ce que contient ce dossier

| Fichier | À quoi il sert |
| --- | --- |
| `README.md` | Ce document : plan d'implémentation complet, écran par écran. |
| `CLAUDE.md` | À copier à la racine du dépôt Symfony : les règles que Claude Code doit respecter (couches, i18n, tokens). |
| `maquettes/Ti Baleine - Parcours client.dc.html` | Maquette haute fidélité du site public. **Référence visuelle, pas du code à copier.** |
| `maquettes/Ti Baleine - Espace de gestion.dc.html` | Maquette haute fidélité de l'espace gérant. Idem. |
| `translations/messages.fr.yaml` | Catalogue français complet : clés existantes du dépôt **plus** tout le texte des maquettes. Prêt à remplacer le fichier du dépôt. |
| `translations/messages.en.yaml` | Le miroir anglais, clé pour clé (le test de parité du dépôt doit rester vert). |
| `assets/tokens.css` | Les variables CSS de la charte, à poser dans `assets/styles/`. |
| `assets/logo-ti-baleine.jpg`, `assets/logo-ti-baleine-anime.mp4` | Le logo fixe et sa version animée. |
| `plan/routes.md` | Table des routes, contrôleurs, services applicatifs appelés, gabarits. |
| `plan/templates.md` | Arborescence Twig cible, bloc par bloc. |

## À propos des maquettes

Les deux fichiers `.dc.html` sont des **prototypes de design**, écrits en HTML avec des styles en ligne pour montrer l'intention visuelle et le comportement attendu. Ils ne se déposent pas dans `templates/` : le travail consiste à **les reconstruire en Twig** dans le dépôt `le-trio/ti-baleine`, en suivant l'architecture en couches déjà en place (`docs/architecture.md`) et en sortant tout le texte dans les catalogues de traduction.

Fidélité : **haute**. Les couleurs, tailles, graisses et espacements des maquettes sont ceux à reproduire. Les emplacements gris marqués « PHOTOGRAPHIE — … » sont des trous à remplir avec de vraies images, à traiter en `.halftone` (trame de similigravure) côté interface.

## Contraintes non négociables du dépôt

Elles viennent de `docs/architecture.md` §2 et §4 ; les enfreindre casse la revue.

1. `src/Interface/` **reçoit une requête, valide la forme, affiche** — rien d'autre. Aucun calcul de montant, aucune requête SQL, aucun appel à Stripe dans un contrôleur ou un gabarit.
2. Un contrôleur appelle un service de `src/Application/` et passe la **Vue** retournée (`VueDeJournee`, `VueDeCreneau`, `VueDeReservation`, `VueDeCode`) au gabarit. Il ne touche jamais une entité du domaine directement.
3. Le domaine ne lit jamais l'heure système (`ADR-005`) : l'horloge est injectée. Un contrôleur n'appelle pas `new \DateTimeImmutable()` non plus — il passe par `App\Domaine\Horloge`.
4. **Aucune chaîne de texte en dur** dans un gabarit ou un contrôleur. Tout passe par le catalogue. Un test compare les clés FR et EN une à une : une clé ajoutée sans sa traduction casse la construction.
5. Montants, dates et heures : `format_currency`, `format_date`, `format_time` avec la locale. Jamais un format écrit à la main (`10 h 00` en FR, `10:00 AM` en EN sortent du même appel).

## Ce qu'il faut ajouter au dépôt

```
composer require symfony/twig-bundle symfony/asset symfony/form \
                 symfony/validator symfony/security-bundle symfony/intl \
                 twig/intl-extra twig/extra-bundle
composer require --dev symfony/maker-bundle symfony/web-profiler-bundle
```

`config/packages/translation.yaml` : `default_locale: fr`, `enabled_locales: [fr, en]`, `fallbacks: [fr]`.

## Ordre de travail conseillé

1. **Socle** — `base.html.twig`, `tokens.css`, en-tête, pied de page, sélecteur de langue, routes préfixées par la locale.
2. **Calendrier public** — valide d'un coup `OffreDeCreneaux`, `FermetureDesReservations` et l'affichage des places restantes.
3. **Réservation + paiement + confirmation** — le tunnel, avec l'immobilisation de 15 minutes et l'acompte.
4. **Bon cadeau** — court, réutilise le paiement.
5. **Espace de gestion** — connexion, journée, créneau, alerte météo, annulation, réglages.
6. **Pages éditoriales** — sorties, flotte, charte, tarifs. Sans logique, à faire en dernier.

Chaque étape se termine par un test fonctionnel Behat ou WebTestCase sur le parcours correspondant.

## Design tokens

Repris de la charte Broadsheet et déclinés sur la palette marine du projet. Le fichier `assets/tokens.css` les porte ; **aucune valeur en dur** dans les gabarits.

| Rôle | Valeur | Usage |
| --- | --- | --- |
| `--encre` | `#0d2233` | Texte courant. |
| `--papier` | `#f4f1e8` | Fond principal. |
| `--papier-ombre` | `#ece7d9` | Second fond, sections alternées et encarts. |
| `--marine` | `#0f2436` | En-tête, pied de page, sections sombres, bouton secondaire. |
| `--marine-clair` | `#1c4a68` | Cartes sombres, liens sur papier, bordure de champ actif. |
| `--ambre` | `#e08c4d` | Accent unique : bouton principal, kicker sur fond sombre, curseur du rail. |
| `--vert` | `#2f7d55` | Places disponibles, réservation confirmée. |
| `--alerte` | `#c9702a` | Alerte météo, immobilisation en cours, seuil non atteint. |
| `--rouge` | `#8c2f2f` | Annulation — bouton destructeur uniquement. |
| `--gris` | `#9aa4ab` | Complet, fermé, code déjà utilisé. |
| `--texte-doux` | `#4c5b66` | Paragraphes secondaires. |
| `--texte-faible` | `#5b6b76` | Légendes, notes de bas de champ. |
| `--sable` | `#8a6a49` | Kickers et intitulés de colonne sur papier. |

Typographie : **Source Serif 4** partout, une seule famille — c'est elle qui fait le chrome. Titre d'accueil 82 px / 0.94 ; titre de section 52 px / 1.02 ; titre de section gestion 38 px ; sous-titre 32 px ; corps 15–16 px / 1.7 ; kicker 11 px, `letter-spacing: .22em`, capitales ; intitulé de colonne 10 px, `.16em`, capitales. Chiffres en `font-variant-numeric: tabular-nums` dans les tableaux.

Formes : **aucun arrondi** (`border-radius: 0`), sauf les médaillons de logo qui sont des cercles. Aucune ombre portée. Les séparations sont des filets `1px` à `#0d223322` ou des grilles `gap:1px` sur fond `#0d223322`. Hauteur de champ 48–52 px ; bouton 13–16 px de padding vertical.

## Écrans — site public

### 1. Accueil — `GET /{_locale}/`

Sans appel applicatif, sauf le prochain départ disponible si vous le voulez en bandeau.

- En-tête collant, fond `--marine` : logo 34 px en médaillon rond, nav (Les sorties, Le bateau, Tarifs, Bon cadeau), sélecteur FR/EN (la locale active soulignée en `--ambre`), bouton « Réserver » plein `--ambre`.
- Section pleine hauteur `--marine` : grille `1.15fr .85fr`, gouttière 48 px, largeur maximale 1180 px.
  - Colonne gauche : ligne de kicker « SAINT-GILLES-LES-BAINS / LA RÉUNION / SAISON 15 JUIN — 31 OCT. » séparée par des barres obliques ; titre 82 px sur deux lignes ; paragraphe 19 px sur 34 caractères de mesure ; deux boutons ; bande de quatre chiffres-clés séparée par un filet haut.
  - Colonne droite : médaillon rond de 360 px, fond `--papier`, portant la vidéo du logo en boucle muette (`autoplay muted loop playsinline`), avec le JPG en `poster`.
- Transition vers le fond papier : une bande de 54 px découpée en vagues par `clip-path` (polygone à neuf sommets).
- **Rail de profondeur** : colonne fixe à gauche, 74 px, graduée 0 / 50 / 100 / 200 / 300 m, avec un curseur ambre qui descend proportionnellement au défilement et affiche la profondeur en mètres. L'encre du rail bascule en `--papier` quand la section sous le curseur est sombre. Masqué sous 1400 px de large. Côté Symfony : un petit contrôleur Stimulus (`depth_rail_controller.js`), pas de dépendance.

### 2. Les sorties — ancre `#sorties`

Deux cartes côte à côte sans gouttière : baleines sur `--marine-clair`, dauphins sur `--papier-ombre`. Chacune porte sa fenêtre de saison, un emplacement photo de 170 px, un paragraphe, et trois chiffres (adulte, enfant, durée) sous un filet.

Dessous, une frise de saison : douze colonnes en grille, ambre pour les mois baleines (juin à octobre, juin en demi-teinte car la saison ouvre le 15), `#2f6b8f` pour les mois dauphins, légende à droite du titre. **Cette frise se calcule depuis `OffreDeCreneaux`**, elle ne se code pas en dur.

### 3. La flotte — ancre `#flotte`

Deux cartes, même grammaire, avec l'image en haut cette fois. Données depuis `Bateau` : nom, capacité, forfait de privatisation.

### 4. Charte d'observation

Fond `--papier-ombre`. Quatre encarts en grille `gap:1px` : 300 m (zone d'approche), 100 m (distance minimale), 0 (mise à l'eau), 30 min (temps maximal — celui-ci sur fond vert `#79c6a4`). Contenu éditorial fixe, mais **en catalogue**.

### 5. Calendrier des départs — `GET /{_locale}/reserver`

Appelle `Application\ConsulterLeCalendrier` (à écrire si absent) → `VueDeJournee[]`.

- Barre de navigation de semaine : flèches, libellé « Semaine du 20 au 26 juillet 2026 » (`format_date`), filtres Toutes / Baleines / Dauphins.
- Grille de 7 colonnes : une ligne d'en-tête (jour abrégé + quantième) puis trois lignes de créneaux (07:00, 10:00, 14:00), soit 21 cellules.
- Chaque cellule : heure, type en petites capitales, nom du bateau, état + pastille de couleur. États : *N places* (vert), *Alerte météo* (ambre), *Complet* / *Fermé à 12 h* (gris). Une cellule fermée ou complète n'est pas cliquable.
- Légende de trois entrées sous la grille.
- Un jour de fermeture ne montre **aucun créneau**, pas même grisé.

### 6. Formulaire de réservation — `GET|POST /{_locale}/reserver/{sortie}`

Fond `--marine`. Grille `1.25fr .75fr`.

- **Panneau blanc à gauche** : rappel du départ choisi avec un lien « Changer » ; deux compteurs (adultes, enfants) en champs à trois cases de 52 px avec le prix unitaire dessous ; encart d'avertissement d'âge (bande ambre de 3 px à gauche, fond `--papier-ombre`) ; quatre champs de coordonnées ; note expliquant pourquoi le mobile est obligatoire ; champ de code (bon cadeau ou avoir) avec bouton « Appliquer » et la mention de non-cumul.
- **Colonne récapitulative collante à droite**, encadrée d'un filet clair : lignes de détail, total, encart ambre portant l'acompte (30 %) et le solde à bord, **compte à rebours de l'immobilisation** (`14 : 22`), bouton de paiement plein papier, mention Stripe. Dessous, si le créneau est en alerte, un encart bordé d'ambre à gauche.
- Validation : au moins un adulte ; pas d'enfant de moins de 4 ans (case à cocher de déclaration, ou simple mention) ; mobile au format réunionnais ; un seul code.
- Le compte à rebours vient de `Reservation::expireA` ; à zéro, la page repart vers le calendrier avec un message.

### 7. Paiement

Redirection vers Stripe Checkout — aucun écran propre à dessiner. Retour sur `/{_locale}/reservation/{reference}`.

### 8. Confirmation — `GET /{_locale}/reservation/{reference}`

Grille `1.1fr .9fr`. À gauche : titre « C'est réservé. Rendez-vous au quai. », paragraphe d'instructions (quinze minutes avant, ponton n° 3), quatre chiffres en grille (référence, départ, acompte versé, solde à bord), deux boutons (agenda `.ics`, justificatif PDF). À droite, carte `--marine-clair` « Ce qui peut encore arriver » : rappel à 24 h, alerte météo à 18 h, annulation par l'entreprise, annulation par le client avec le barème.

### 9. Tarifs — `GET /{_locale}/tarifs`

Un tableau à quatre colonnes (prestation, durée, adulte, enfant), en-tête à filet plein et lignes à filet léger. Cinq lignes, dont la dernière porte la règle des moins de 4 ans avec des tirets. Sous le tableau, trois colonnes de notes : acomptes, montant figé à la réservation, règles de bon cadeau.

### 10. Bon cadeau — `GET|POST /{_locale}/bon-cadeau`

Fond `--marine-clair`. À gauche : titre, explication (un montant, pas une sortie), trois pastilles de montant dont une sélectionnée (bordure ambre de 2 px), note sur l'envoi et l'usage unique. À droite, panneau papier : montant, bénéficiaire, courriel de l'acheteur, message facultatif, total, bouton, date d'expiration calculée à un an.

### 11. Mobile

Le même parcours en trois écrans de 320 px : liste de créneaux du jour, formulaire compact avec les compteurs en ligne, confirmation. La maquette en montre le rendu ; en Twig c'est la même page, la grille passant en une colonne sous 900 px, la colonne récapitulative devenant une barre collante en bas.

## Écrans — espace de gestion

Préfixe `/{_locale}/gestion`, protégé par le pare-feu du compte unique.

### G1. Connexion — `GET|POST /{_locale}/gestion/connexion`

Deux colonnes pleine hauteur : à gauche fond `--marine` avec le titre et la mention de fermeture de session ; à droite fond papier avec le formulaire (courriel, mot de passe avec bascule d'affichage), le bouton, et la règle de complexité (`SPEC-ADMIN-01` : huit caractères, majuscule, chiffre, caractère spécial ; suspension quinze minutes après cinq échecs).

### G2. La journée — `GET /{_locale}/gestion`

Barre latérale fixe de 232 px, fond `--marine`, entrée active sur fond ambre en encre marine.

- Quatre indicateurs en grille `gap:1px` : inscrits du jour, sorties programmées, décisions à prendre, encaissé.
- **Bandeau d'action** pleine largeur en `--alerte` quand un créneau est sous le seuil de 6 à 24 h du départ : rappel de la règle et deux boutons (Maintenir / Annuler la sortie).
- Six cartes de sortie en grille de trois : heure, statut coloré, ligne descriptive, barre de remplissage, compte d'inscrits, lien « Ouvrir ». Statuts : PARTIE (gris), OUVERTE (vert), COMPLÈTE (marine clair), EN ALERTE (ambre).

### G3. Créneau détaillé — `GET /{_locale}/gestion/creneau/{id}`

`VueDeCreneau` + `VueDeReservation[]`.

Trois boutons d'action en tête, dont « Mettre en alerte météo » (contour ambre) et « Annuler le créneau » (plein rouge). Grille `1.55fr .45fr` : à gauche le tableau des inscrits (passager, mobile, composition, montant, solde dû, état) avec une ligne d'immobilisation en cours marquée en ambre et **non comptée dans les inscrits** ; à droite trois encarts (occupation avec barre et rappel du seuil, météo saisie avec sa date de saisie, fermeture des ventes calculée par `FermetureDesReservations`).

### G4. Alerte météo — `GET|POST /{_locale}/gestion/alerte`

Appelle `Application\MettreEnAlerte`. À gauche, panneau `--marine` : prévision saisie, choix de l'envoi (ce soir à 18 h / immédiatement), aperçu du message tel qu'il partira — **rendu depuis `message.alerte_meteo.corps` du catalogue, pas retapé**. À droite : liste des créneaux en alerte avec leurs actions, et un encart des trois réglages d'envoi (18 h, 2 h, 24 h) issus de `Parametre`.

L'alerte vaut pour tout le créneau, les deux bateaux compris ; elle n'annule rien.

### G5. Annulations — `GET|POST /{_locale}/gestion/creneau/{id}/annuler`

À gauche, panneau bordé de rouge : récapitulatif chiffré de ce que l'annulation déclenche (réservations concernées, total remboursé, messages envoyés), motif consigné, deux boutons. L'opération est irréversible et le dit.

À droite, le barème de retenue (`RetenueDannulation`) en quatre lignes — 100 % au-delà de 7 jours, 75 % entre 7 jours et 48 h, versé moins 50 % du prix total en deçà avec plancher à zéro, 0 € en cas d'absence — puis le sélecteur d'issue pour un client donné (report / avoir / remboursement) avec les montants calculés.

### G6. Réglages — `GET|POST /{_locale}/gestion/reglages`

Grille tarifaire éditable (une ligne par type de sortie, champs alignés à droite, colonne « modifié le » qui passe en ambre sur « non enregistré »), flotte en deux cartes avec la mention qu'un bateau créé ne se modifie plus sauf son forfait, horaires d'exploitation, jours de fermeture avec ajout et retrait, et la liste des bons et avoirs en circulation.

Rappel à afficher : un tarif modifié ne rattrape aucune réservation existante, et les taux d'acompte ne se règlent pas ici.

## États et interactions à couvrir

| Situation | Ce qui doit se produire |
| --- | --- |
| Dernière place prise par un autre pendant la saisie | Message clair au retour du `POST`, retour au calendrier, aucune place décomptée. |
| Immobilisation expirée | Le compte à rebours atteint zéro, la page bascule vers le calendrier avec un message ; la place est reprise au comptage à la lecture. |
| Créneau fermé aux ventes | Cellule grise, non cliquable ; un `POST` direct est refusé côté Application. |
| Code invalide, expiré ou déjà utilisé | Trois messages distincts sous le champ, la réservation reste saisissable. |
| Bon cadeau supérieur au montant | Le surplus n'est pas rendu, et c'est écrit sous le récapitulatif. |
| Stripe indisponible | Le parcours s'arrête avant l'écriture, message explicite, aucune place décomptée. |
| Session gérant expirée | Retour à la connexion, avec un message. |

## Assets

- `logo-ti-baleine.jpg` — logo baleine, à poser sur fond `--papier` en médaillon rond ; **pas de mode de fusion**, il l'abîme.
- `logo-ti-baleine-anime.mp4` — la version animée, uniquement dans le médaillon d'accueil, muette et en boucle, avec le JPG en poster.
- Les emplacements « PHOTOGRAPHIE — … » attendent de vraies images : baleine à bosse, dauphins, les deux bateaux. À traiter en `.halftone`.
