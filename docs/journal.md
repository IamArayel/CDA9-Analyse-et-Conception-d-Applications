# Journal de projet — équipe `Le Trio`

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

## J1 — 2026-08-10

**Présents.** Client + équipe complète de développeurs.

**Décisions.**
- Premier entretien client mené avec le gérant de Ti Baleine, couvrant les huit
  thèmes prévus (flotte et capacités, créneaux et prestations, tarification et
  règles de réservation, paiement et annulation, aléas météo, communication,
  exploitation quotidienne, intégrations techniques) — consigné dans
  `compte-rendu-entretien-01.md` et `compte-rendu-entretien-02.md`.
- Flotte figée à 2 bateaux : *Ti Kap* (12 places) et *Grand Bleu* (24 places),
  un seul bateau affecté aux sorties baleines (un seul naturaliste), les deux
  aux sorties dauphins.
- Jauges de réservation actées : 2 personnes minimum pour réserver, 6 minimum
  pour maintenir un départ (sortie annulée sinon, avec report ou remboursement
  proposé par le gérant).
- Grille horaire actée : 3 départs/jour (7h, 10h, 14h), sorties d'environ 3h
  (2h30 baleines, 2h dauphins), saison baleines du 15 juin au 31 octobre
  (sorties dauphins le reste de l'année, sur les mêmes créneaux), 30 min à 1h
  de battement entre deux sorties, privatisation sur demi-journée (matin ou
  après-midi/sunset).
- Tarifs de référence actés (révisables chaque année par le gérant) : baleines
  65 €/adulte, 40 €/enfant ; dauphins 50 €/adulte, 30 €/enfant ; privatisation
  600 € (*Ti Kap*) / 1100 € (*Grand Bleu*) ; tranche d'âge « enfant » fixée à
  4–11 ans (sortie interdite avant 4 ans, tarif adulte dès 12 ans).
- Réservation en ligne fermée à J-12h (midi) avant le départ, y compris pour un
  départ le lendemain matin ; paiement intégral en ligne par carte bancaire
  uniquement (aucun espèces/chèque/virement, aucun prestataire de paiement
  retenu à ce stade).
- Politique d'annulation client actée : remboursement total à plus de 7 jours,
  25 % de commission retenue entre 7 jours et 48h, 50 % entre 48h et 24h.
- Annulation météo : décision et arbitrage (report vs remboursement) laissés
  au gérant, par téléphone — pas d'automatisation de la décision elle-même ;
  rappel client automatisé à J-1 (météo + consignes) ; WhatsApp conservé en
  parallèle du nouvel outil.
- Un seul compte d'accès (le gérant/admin) ; planning papier suffisant sur le
  terrain ; pas de site existant (page Facebook seulement), pas de logiciel
  comptable à intégrer, pas de charte graphique définie.
- Treize user stories rédigées (US01 à US13), réparties en 4 épiques :
  réservation/paiement client, gestion du planning et des réservations,
  aléas météo et annulations, configuration et paramétrage du système.

**Critiques de l'IA acceptées.**
- Aucune : l'IA n'intervient pas en J1.

**Critiques de l'IA refusées, et pourquoi.**
- Sans objet.

**Erreurs produites par l'IA et détectées.**
- Sans objet.

**Ce qui a été généré aujourd'hui.**
- `compte-rendu-entretien-01.md` (commits `b8b4a9b`, `09d8e22`, `ce4e7c2`,
  `489214a`)
- `compte-rendu-entretien-02.md` (commit `b9cbfed`)
- US01 à US13 (consignées dans la page Notion J1 pour le moment)

**Questions ouvertes pour le client.**
- Modalités précises de modification de la taille d'un groupe après
  réservation : réponse du client non tranchée (« pas encore de réponse »
  explicitement notée dans le compte rendu).
- Critère de répartition des passagers entre les deux bateaux quand plusieurs
  choix sont possibles : réponse du client jugée floue par l'équipe.

## J2 — 2026-08-11

**Présents.** Client + équipe complète de développeurs.

**Décisions.**
- Rédaction du cahier des charges initial de « Ti Baleine » à partir du compte
  rendu d'entretien n°1 : espace admin (gestion des tarifs révisés chaque
  année, système de réservation baleines/dauphins, export PDF des plannings à
  la demi-journée), page d'accueil (présentation des deux bateaux + moteur de
  réservation), formulaire de réservation (email, nom, prénom, téléphone,
  nombre d'adultes/enfants, date, type de sortie), rappel des modalités de
  validation (2 pers. min. pour réserver / 6 pers. min. pour maintenir la
  sortie) et de la politique d'annulation (J-7, J-7→48h, 48h→24h), génération
  de facture PDF/mail.
- Choix de stack technique actés en réunion (formalisés depuis dans
  `docs/adr/ADR-001-stack.md`, commit `84d54cf`) :
  - Symfony/PHP (pratiqué par 3 membres de l'équipe), tests via PHPUnit
    (`php bin/phpunit`).
  - Prestataire de paiement Stripe, retenu après comparaison avec PayPal et
    l'offre de paiement en ligne du Crédit Agricole (génération de facture,
    coût le plus bas des trois solutions étudiées, conforme au souhait du
    client de tout gérer en CB en ligne) ; aucune donnée de paiement sensible
    stockée côté dev.
  - SGBD MySQL (ORM Symfony adapté au relationnel, pas de données de paiement
    sensibles à traiter côté application).
  - Hébergement mutualisé Hostinger (150 €/48 mois ≈ 2,99 €/mois, nom de
    domaine offert la 1ʳᵉ année, 20 Go SSD, sauvegardes hebdomadaires).
  - RGPD : durée de conservation des données fixée à 3 mois minimum avant
    suppression.
  - Point de vigilance consigné par l'équipe : si le client reprend en charge
    le paiement de bout en bout, réévaluer PostgreSQL pour le traitement de
    données sensibles.
- Première ébauche du modèle de données (tables clients, réservations,
  utilisateur/admin, bateaux, créneaux, sorties, tarifs).
- Échéance d'équipe actée : tenir le planning « jusqu'à vendredi prochain »
  (mention explicite de la page Notion J2).

**Critiques de l'IA acceptées.**
- Sans objet documenté : aucune trace d'intervention de l'agent IA pour cette
  journée (pas de trailer `Generated-by` sur les commits du jour, page Notion
  source muette sur le sujet) — à confirmer ou compléter par l'équipe si des
  échanges ont eu lieu.

**Critiques de l'IA refusées, et pourquoi.**
- Sans objet documenté (voir ci-dessus).

**Erreurs produites par l'IA et détectées.**
- Sans objet documenté (voir ci-dessus).

**Ce qui a été généré aujourd'hui.**
- `docs/cahier-des-charges.md`, v1 (commits `8d76bc8`, `4c504d2`, `a6c22c1`,
  `3090d10`, `832a1bf`)
- `docs/cahier-des-charges.pdf` (commit `77b43a5`)
- `docs/cle-cahier-des-charges.md` (commit `e4a45ad`)
- `compte-rendu-entretien-02.md` mis à jour sur les exigences de paiement en
  ligne (commit `4c504d2`)

**Questions ouvertes pour le client.**
- Modalités de l'espace de connexion : identifiant par email ou téléphone ?
  Règles de longueur/complexité du mot de passe ?
- Jours de fermeture de l'entreprise : non mentionnés dans le cahier des
  charges — en existe-t-il, et si oui sont-ils modifiables, et par qui ?
- Le calendrier des créneaux est-il modifiable, et selon quelles modalités ?
- Faut-il ajouter un formulaire de contact pour les demandes de renseignement
  (point noté « oublié de demander » par l'équipe) ?

## J3 — 2026-08-12

**Présents.** Client + équipe complète de développeurs.

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
- `docs/traceability.md` (régénéré)

**Questions ouvertes pour le client.**
- Les quatre questions du §8 de `compte-rendu-entretien-03.md` (usage
  téléphonique exceptionnel d'un bon cadeau, champs du formulaire de
  création d'un bateau, distinction avoir/bon cadeau, prix d'achat d'un
  bon cadeau).
- Les questions déjà en attente au §11 du cahier des charges (budget,
  format de facture, durée de conservation des données, modalités de
  connexion à l'espace de gestion).


## J4 — 2026-08-13

**Présents.** Client + équipe complète de développeurs.

**Décisions.**
- Diagrammes de cas d'utilisation et de classes du domaine produits en
  PlantUML, sources versionnées et images générées laissées hors du dépôt,
  conformément à `docs/uml/README.md`.
- Support de présentation du J4 préparé sur les deux parcours sensibles,
  réservation et annulation, avec un déroulé minuté de 30 minutes.
- **Le client revient sur le bon cadeau** lors d'un échange oral : montant
  libre choisi à l'achat, plus aucun rattachement à un type de sortie ni à
  une catégorie de tarif adulte ou enfant, imputation sur le montant total
  de la réservation. Le solde se paie par carte, le surplus reste perdu.
- Le client tranche deux points en suspens : l'avoir est lui aussi valable
  un an, et une réservation porte toujours sur une seule sortie, sans
  regroupement de plusieurs sorties en une commande.
- Chaîne descendue dans l'ordre du README avant toute modification de
  spécification : `impact-CR-002.md`, puis cahier des charges v4
  (`REQ-045` inversée, `REQ-047`/`REQ-048` précisées, `REQ-051` ajoutée),
  puis `SPEC-BOOKING-09` et `SPEC-BOOKING-10` en v2, puis
  `domain.puml`, puis régénération de la matrice.
- `REQ-045` est **inversée plutôt que supprimée**, identifiant conservé :
  l'historique du changement reste lisible et la matrice ne perd pas la
  trace de l'exigence.
- Fusion de `BonCadeau` et `Avoir` **non tranchée** : les deux classes
  restent séparées tant que le client n'a pas répondu.

**Critiques de l'IA acceptées.**
- Un montant libre sans borne autorise aussi bien un bon de 3 € qu'un bon de
  5 000 € → cas limite 13 et hypothèse de bornage ajoutés, question 10
  ouverte au §11 (`specs/booking.md`, `docs/cahier-des-charges.md`).
- Depuis la validité d'un an accordée à l'avoir, `BonCadeau` et `Avoir`
  n'ont plus qu'une seule différence, leur origine → la question 8 du §11
  cesse d'être une question de vocabulaire et devient une décision de
  conception, requalifiée comme telle et tracée en `impact-CR-002.md` §8.
- Le mot « panier » employé par le client n'existe pas au glossaire et
  pouvait impliquer une classe de commande au-dessus de `Réservation` →
  question posée au client, qui a confirmé qu'une réservation porte sur une
  seule sortie ; aucune classe ajoutée.
- `REQ-045` citait « privatisation » comme un type de sortie alors que
  c'est une formule, facturée au forfait par bateau → signalé, devenu sans
  objet avec l'inversion de l'exigence, mais la même confusion reste à
  surveiller ailleurs.
- La règle de non-cumul entre bon cadeau et avoir n'est plus étayée par une
  différence de comportement → maintenue faute de règle client contraire,
  mais explicitement signalée comme arbitraire dans `impact-CR-002.md` §8.

**Critiques de l'IA refusées, et pourquoi.**
- Fusionner `BonCadeau` et `Avoir` en une classe unique porteuse d'un
  attribut d'origine → refusé, car la fusion est défendable techniquement
  mais reste une décision métier : deux dispositifs distincts étaient
  l'hypothèse de départ du client (`CR-03` §6), et l'équipe ne s'autorise
  pas à la lever seule. Le repli est documenté, la question reposée.
- Avant l'échange avec le client, l'IA proposait que la catégorie d'un bon
  cadeau fixe seulement son prix sans contraindre son usage → refusé par
  l'équipe, qui a imposé la contrainte à l'utilisation. Arbitrage devenu
  sans objet quelques heures plus tard, le client ayant supprimé la notion
  de catégorie.

**Erreurs produites par l'IA et détectées.**
- L'IA a affirmé qu'interdire à un bon « enfant » de réduire une réservation
  d'adultes obligerait à décomposer le montant en lignes par participant, ce
  qui était faux → repéré en reformulant la règle comme une condition
  d'éligibilité : `Réservation` porte déjà `nombreAdultes` et
  `nombreEnfants`, il suffisait d'exiger au moins un participant de la
  catégorie du bon → règle corrigée sans toucher au modèle, avant que le
  client ne rende le point caduc.
- En réécrivant `SPEC-BOOKING-09` et `SPEC-BOOKING-10`, l'IA a cité
  `REQ-045` et `REQ-051` dans les tableaux de revue IA, placés après des
  renvois `cf. SPEC-BOOKING-07` et `cf. SPEC-CANCEL-04` → deux paires
  fausses sont apparues dans la matrice (`SPEC-BOOKING-07 → REQ-045`,
  `SPEC-CANCEL-04 → REQ-051`), repérées en comparant la matrice régénérée à
  celle d'un worktree sur `HEAD` (78 ruptures contre 72) → identifiants
  retirés de ces lignes, conformément à la convention en tête de
  `specs/booking.md`. **C'est exactement le piège déjà consigné au J3** :
  `tools/traceability.sh` rattache une exigence au dernier `SPEC-xxx-nn`
  rencontré, y compris quand ce n'est qu'un renvoi en prose.
- Constat annexe sur l'outillage : `tools/traceability.sh` ne reconnaît
  comme source qu'un motif `CR-nn/Qnn` ou le mot « déduit ». `REQ-045` et
  `REQ-051`, d'abord sourcées sur « l'échange oral du 2026-08-13 », étaient
  donc signalées en rupture à tort → corrigé en formalisant l'entretien en
  `compte-rendu-entretien-04.md` et en réécrivant les deux sources en
  `CR-04/Q01`, `CR-04/Q02` et `CR-04/Q04`.

**Ce qui a été généré aujourd'hui.**
- `docs/uml/domain.puml`, `docs/uml/use-cases.puml` (nouveaux)
- `docs/impact-CR-002.md`, `docs/compte-rendu-entretien-04.md` (nouveaux)
- `docs/cahier-des-charges.md` (v3 → v4)
- `specs/booking.md` (`SPEC-BOOKING-09` et `SPEC-BOOKING-10` en v2)
- `docs/traceability.md` (régénéré)
- Support de présentation du J4 (hors dépôt)

**Questions ouvertes pour le client.**
- Bon cadeau et avoir sont-ils encore deux dispositifs, alors qu'ils ne
  diffèrent plus que par leur origine ? (§11, question 8)
- Le montant d'un bon cadeau est-il borné, et selon quel arrondi ?
  (§11, question 10, ouverte par la réponse du client lui-même)
- Que rend-on lorsqu'une réservation payée par bon cadeau est annulée pour
  raison météo ? Hypothèse en vigueur : un avoir de montant équivalent.
- `CR-04` doit être relu par la personne ayant mené l'échange : deux
  réponses y ont été rapportées sous forme d'arbitrages, et si elles
  relèvent d'une décision d'équipe, `REQ-051` doit être marquée `déduit`
  plutôt que sourcée `CR-04/Q04` (`CR-04` §6, ambiguïté 2).
- Les questions déjà en attente au §11 du cahier des charges (budget,
  format de facture, durée de conservation des données, modalités de
  connexion à l'espace de gestion, usage téléphonique d'un bon cadeau,
  champs du formulaire de création d'un bateau).


## J5 - 2026-08-14

**Présents.** Client + équipe complète de développeurs.

**Décisions.**
- MCD et MLD produits (`docs/mcd-mld.md`, `uml/mcd.puml`, `uml/mld.puml`),
  jusqu'ici absents du dépôt alors que le jalon de fin de semaine les
  attend. Chaque entité et chaque contrainte cite la spécification qui la
  porte.
- **Deux tables distinctes** pour `bon_cadeau` et `avoir`, malgré des
  colonnes désormais identiques : la question de leur fusion est posée au
  client et reste sans réponse (§11, question 8). Deux tables gardent les
  deux options ouvertes, une table fusionnée préjugerait de sa réponse.
- `architecture.md` rédigé en v1 : quatre couches, les douze règles métier
  sensibles rattachées chacune à sa spécification et à son emplacement dans
  `src/`, et cinq limites assumées au §9.
- `ADR-002` : MySQL confirmé, mais **contre le modèle réel** et non contre
  l'intuition de J2, comme `ADR-001` s'y était engagé.
- `ADR-003` : la concurrence sur la dernière place passe à la
  **pré-réservation de 15 minutes**, sur remarque du formateur au jalon.
- Le client revient en cours de journée avec une demande nouvelle, l'alerte
  météo préventive. Chaîne descendue dans l'ordre : `CR-05`, puis
  `impact-CR-003`, puis cahier des charges v5. Les spécifications, l'UML et
  le modèle **ne sont pas encore descendus** : ils restent alignés sur la
  v4, ce qui est écrit dans chacun d'eux.
- `REQ-023` et `REQ-024` **inversées, identifiants conservés**, comme
  `REQ-045` en v4 : la correction reste lisible dans le document et dans la
  matrice.
- `REQ-059` marquée `déduit` : elle ne vient d'aucun échange client mais
  d'une décision d'équipe. Elle est signalée comme telle plutôt que
  rattachée artificiellement à un entretien.
- Chaîne descendue jusqu'au bout dans l'après-midi : specs (`SPEC-CANCEL-06`
  et `SPEC-ADMIN-06` créées, huit spécifications reprises), puis UML, MCD/MLD
  et architecture en v2. Plus aucune exigence n'est sans spécification.
- **Second passage client en fin de journée** : les cinq hypothèses d'équipe
  du §6 de `CR-05` sont **toutes confirmées** (`Q16` à `Q20`), aucune n'est
  infirmée. Horaires d'alerte réglables, annulation possible jusqu'à l'heure
  de départ, coordonnées contrôlées à la saisie, alerte portant sur les deux
  bateaux d'un créneau, confirmation envoyée à tout client inscrit.
- **`ADR-004` sans objet** : le client conserve le forfait et le numéro
  actuels de l'entreprise pour les SMS (`CR-05/Q21`). Aucun prestataire n'est
  donc à comparer, mais la question de la passerelle d'envoi reste ouverte,
  voir les questions au client.
- `CR-02/Q04` reçoit une **note de rectification datée** plutôt qu'une
  réécriture : le compte rendu garde ce que le client a dit, et signale que
  la lecture qui en avait été faite était fausse.

**Critiques de l'IA acceptées.**
- `Tarif` regroupait prix adulte, prix enfant et forfait de privatisation,
  alors que les deux premiers dépendent du type de sortie et le troisième du
  bateau → forfait déplacé sur `bateau`, en colonne nullable, ce qui porte
  au passage `SPEC-ADMIN-05` AC-5 (`docs/mcd-mld.md` §5).
- `ChoixAnnulation` était rattaché à `Sortie` : impossible de savoir quel
  client avait choisi quoi → rattaché à `reservation`, avec unicité.
- La règle du naturaliste unique n'était portée par aucune contrainte →
  colonne générée `creneau_baleines` et index unique, plutôt qu'un contrôle
  applicatif sujet aux courses.
- Le non-cumul d'un bon cadeau et d'un avoir reposait sur du code →
  contrainte `CHECK` ajoutée au schéma.
- Aucune spécification ne disait ce qu'il advient de l'argent du client
  perdant sur la dernière place, alors que le paiement est intégral →
  `ADR-003`, pré-réservation.

**Critiques de l'IA refusées, et pourquoi.**
- Fusionner `bon_cadeau` et `avoir` en une table unique portant une colonne
  d'origine → refusé, car la fusion est défendable techniquement mais
  préjuge d'une question posée au client et restée sans réponse. La
  séparation est réversible, la fusion détruirait l'information d'origine.
- Stocker le nombre de places restantes sur `sortie` pour éviter un calcul →
  refusé, donnée dérivée donc désynchronisable ; le verrou transactionnel
  suffit à la volumétrie attendue (`SPEC-NFR-01`).

**Erreurs produites par l'IA et détectées.**
- L'IA a annoncé un MCD livré alors que seuls des tableaux d'entités et de
  cardinalités existaient dans `mcd-mld.md` : **aucun diagramme conceptuel**
  → repéré par l'équipe en cherchant le fichier, corrigé par
  `uml/mcd.puml`.
- Premier rendu de `uml/mcd.puml` : les losanges d'association sortaient
  vides, PlantUML n'affichant pas le nom d'un élément `diamond` → repéré en
  générant l'image avant de committer, corrigé par des nœuds nommés portant
  le stéréotype `association`.
- L'IA a numéroté `ADR-002` l'ADR du prestataire SMS dans
  `impact-CR-003.md`, alors que `architecture.md` réservait déjà ce numéro à
  la persistance → repéré à la relecture de l'analyse d'impact, renuméroté
  en `ADR-004` après l'insertion d'`ADR-003`.
- L'IA a ajouté vers 12h30 une note sur le cas d'usage « Payer en ligne »
  affirmant que le conflit sur la dernière place se résout au paiement, note
  rendue **fausse une heure plus tard** par `ADR-003`, qui déplace le conflit
  à la validation du formulaire → repérée en descendant l'UML l'après-midi,
  note réécrite. L'écriture était juste au moment où elle a été faite ; c'est
  de ne pas l'avoir revue après la décision qui était l'erreur.
- L'IA a conclu que l'avoir n'avait plus de fait générateur, en s'appuyant
  sur `REQ-023` et `REQ-050`. La lecture des documents était exacte, mais
  ces documents étaient faux : `CR-02/Q04` avait été transcrit comme si le
  choix report, avoir ou remboursement suivait une annulation météo. Le
  client a corrigé le 2026-08-14 → `REQ-023` et `REQ-024` inversées,
  `REQ-019` et `REQ-050` précisées. **L'erreur ne datait pas du jour même
  mais du deuxième entretien**, et elle n'aurait pas été détectée sans
  l'analyse d'impact.

**Ce qui a été généré aujourd'hui.**
- `docs/compte-rendu-entretien-05.md`, `docs/impact-CR-003.md` (nouveaux)
- `docs/mcd-mld.md`, `docs/uml/mcd.puml`, `docs/uml/mld.puml` (nouveaux)
- `docs/architecture.md` (gabarit vierge → v1)
- `docs/adr/ADR-002-persistance.md`, `docs/adr/ADR-003-concurrence-derniere-place.md` (nouveaux)
- `docs/cahier-des-charges.md` (v4 → v5)
- `docs/uml/use-cases.puml`, `docs/uml/domain.puml` (alignés v5 : deux cas
  d'usage ajoutés, état « en alerte », classe `Notification`)
- `docs/uml/sequences/annuler-creneau-meteo.puml` (réécrit),
  `reserver-payer-sortie.puml` et `controle-j-24h.puml` (mis à jour)
- `specs/cancel.md` (réécrit, `SPEC-CANCEL-06` créée), `specs/admin.md`
  (`SPEC-ADMIN-06` créée), `specs/booking.md` et `specs/non-fonctionnel.md`
- `docs/mcd-mld.md` et `docs/architecture.md` (v1 → v2 en fin de journée)
- `docs/compte-rendu-entretien-02.md` (note de rectification sur `Q04`)
- `docs/compte-rendu-entretien-05.md` (`Q16` à `Q21`, cinq ambiguïtés levées)
- `docs/traceability.md` (régénéré)
- Images PlantUML régénérées, non versionnées (`.gitignore`)

**Questions ouvertes pour le client.**
- **Les SMS partent-ils d'une passerelle d'envoi affichant le nom de
  l'entreprise ?** Conserver le forfait actuel est compatible avec un envoi
  automatique, l'envoyer depuis le téléphone du gérant ne l'est pas. C'est le
  seul point restant qui puisse faire tomber l'automatisation demandée.
- Le message associé à une annulation faute de 6 inscrits, et le consentement
  explicite à recevoir des SMS (§11, questions 13 et 14). Les questions 11 et
  12 ont trouvé réponse dans la journée.
- Le texte des trois messages automatiques, en français et en anglais,
  toujours pas fourni, y compris pour le rappel qui existe depuis `CR-02`.
- La fusion du bon cadeau et de l'avoir (§11, question 8), qui conditionne
  une ou deux tables au modèle de données.
- `CR-05` doit être relu par la personne ayant mené l'échange : comme
  `CR-04`, il repose sur des propos rapportés et non sur une source brute.


## J6 - 2026-08-17

**Présents.** Équipe complète de développeurs. Aucun retour du formateur sur
le rendu de vendredi : nous passons à l'étape suivante.

**Décisions.**
- **Stratégie de test écrite** (`docs/strategie-de-test.md`). Le barème note
  « Tests : stratégie, cas, qualité » et rien ne l'écrivait. Elle dit ce que
  nous testons, à quel niveau, et surtout **ce que nous ne testons pas** :
  le prestataire de paiement, la délivrance réelle d'un message, le rendu
  graphique, la charge, et les deux spécifications au statut brouillon.
- **Gabarit de cas créé** (`tests/cases/TEMPLATE.md`). Le README précise que
  les fichiers à créer n'ont pas de gabarit fourni et qu'il faut savoir le
  défendre : le nôtre impose de citer la spécification **et** les critères
  couverts, de fixer l'instant courant quand l'heure compte, et de déclarer
  **ce que le cas ne vérifie pas**.
- **82 cas de test écrits**, 27 spécifications couvertes sur 29, en sept
  paliers ordonnés par risque et non par facilité.
- **Triage assumé plutôt que couverture affichée.** Couvrir les 141 critères
  au rythme du premier palier demandait une centaine de cas en quatre jours,
  tout en automatisant, générant le code et préparant J10. Couverture fine
  sur l'argent et le parcours client, nominale sur le reste, trois cas
  `manuel assumé`, deux spécifications sans cas. Le tout déclaré.
- **SMS** : l'envoi depuis le téléphone du gérant est écarté, volume trop
  important pour un terminal de poche et dépendance à la couverture réseau
  et à la batterie. Une plateforme française conforme au RGPD est retenue,
  choisie sur la popularité et la simplicité, le prix n'étant plus un critère
  depuis la réponse « budget illimité ». `ADR-004` écrit.
- **Bon cadeau et avoir** : deux tables maintenues, par précaution si le
  gérant fait évoluer son produit. La question 8 du §11 reste posée au
  client, mais la conception, elle, est arrêtée.
- **Accès au temps** : `ADR-005`. Une horloge injectée pour les traitements
  déclenchés sans utilisateur, un instant en paramètre pour les calculs purs.
  La lecture de l'heure système rejoint la colonne « ce qui n'a rien à y
  faire » du domaine dans `architecture.md` §2.
- **Organisation de `tests/`** écrite au §9 de la stratégie : un outil par
  niveau, une classe par spécification, une méthode par cas, trois doublures
  et pas une de plus. C'est la dernière entrée manquante du plan de
  délégation de J7.
- Le **planning de l'équipe** est ajouté au dépôt.

**Critiques de l'IA acceptées.**
- Couvrir les 141 critères d'acceptation n'était pas tenable dans le temps
  restant → triage explicite par palier, et non-couverture déclarée dans
  `docs/traceability-trous.md` plutôt que découverte à J10.
- Les quatre critères de `SPEC-NFR-05` et `SPEC-NFR-06` ne décrivent aucun
  comportement logiciel, ce sont des actions de projet → aucune de ces deux
  spécifications n'aura de cas de test, et c'est écrit.
- Un cas déclaré `manuel assumé` produisait une rupture permanente dans la
  matrice → `tools/traceability.sh` les compte désormais à part, comme les
  exigences `déduit`. Le bruit permanent finit par masquer les vraies
  ruptures.
- L'ADR de la plateforme SMS avait été déclaré « sans objet » le 14 août sur
  une lecture trop large de `CR-05/Q21` : le client répondait sur son
  abonnement, pas sur le mode d'envoi → `ADR-004` écrit, ligne corrigée dans
  les trous.

**Critiques de l'IA refusées, et pourquoi.**
- Aucune aujourd'hui ; l'équipe a suivi les recommandations de l'agent sur les trois arbitrages du jour,
  l'ordre des paliers, l'horloge et l'organisation des tests. En revanche
  **les critères de choix de la plateforme SMS sont ceux de l'équipe**,
  française et conforme au RGPD, populaire et simple, le prix exclu : l'agent
  n'a fait que les instruire.

**Erreurs produites par l'IA et détectées.**
- `tools/traceability.sh` rattachait un cas de test à **toutes** les
  spécifications citées dans son fichier, y compris celles listées sous « ce
  que ce cas ne vérifie pas ». `SPEC-BOOKING-06` est ainsi apparue couverte
  par un cas qui ne la teste pas → repéré en régénérant la matrice après le
  premier palier, corrigé en ne lisant que la ligne « Spécification : ».
  **C'est le même piège qu'au J3, dans l'autre sens** : un identifiant cité
  en prose n'est pas un lien.
- Deux critères se sont retrouvés non couverts alors que le comportement
  l'était, `SPEC-BOOKING-10` AC-5 et `SPEC-ADMIN-02` AC-4, parce que le cas
  correspondant n'était rattaché qu'à une seule des deux spécifications qui
  décrivent la même règle → repéré par le décompte des critères couverts,
  corrigé en rattachant ces deux cas à leurs deux spécifications.
- L'agent avait laissé entendre que l'automatisation des tests pouvait
  commencer aujourd'hui. L'équipe a demandé vérification plutôt que de le
  prendre pour acquis : le README §6bis interdit toute tâche confiée à
  l'agent avant le plan de délégation, daté de J7. La lecture de l'équipe
  était la bonne.

**Ce qui a été généré aujourd'hui.**
- `docs/strategie-de-test.md`, `tests/cases/TEMPLATE.md` (nouveaux)
- 82 cas de test, `CASE-BOOKING-01` à `37`, `CASE-CANCEL-01` à `24`,
  `CASE-ADMIN-01` à `15`, `CASE-NFR-01` à `06`
- `docs/adr/ADR-004-envoi-des-sms.md`,
  `docs/adr/ADR-005-horloge-injectable.md` (nouveaux)
- `docs/architecture.md` (v2 → v3), `docs/planning.md` (nouveau)
- `tools/traceability.sh` (deux corrections), `docs/traceability-trous.md`,
  `docs/traceability.md` (régénérée)

**Questions ouvertes pour le client.**
- Le nom exact de la plateforme d'envoi dépend de trois vérifications qui ne
  se font pas depuis le dépôt : couverture du plan de numérotation du
  territoire, expéditeur au nom de l'entreprise, contrat de sous-traitance.
- Le texte des trois messages automatiques, toujours pas fourni. `ADR-004`
  ajoute une contrainte : un expéditeur alphanumérique ne reçoit pas de
  réponse, le message doit donc dire au client comment joindre le gérant.
- Les neuf autres questions du §11, inchangées depuis vendredi.

---

## J7 - 2026-08-18

**Présents.** Équipe complète de développeurs. Journée d'exécution : les arbitrages de fond ont été rendus vendredi et lundi, aujourd'hui nous les appliquons.

**Décisions.**
- **Les 25 plans de délégation sont écrits avant la première tâche confiée à l'agent**, comme le README §6bis l'impose. 81 tâches, une par cas automatisable, chacune nommant le test qui doit passer au vert, ce que l'agent reçoit, et ce qu'il ne touche pas.
- **Quatre spécifications sans plan, et c'est déclaré.** `SPEC-NFR-01` et `SPEC-NFR-03` n'ont qu'un cas `manuel assumé` et ne donnent lieu à aucune production ; `SPEC-NFR-05` et `SPEC-NFR-06` n'ont aucun cas. La distinction avec `SPEC-BOOKING-08`, qui a bien un plan alors que son cas est également manuel, tient à ce que celle-ci demande du code et n'a que sa vérification manuelle. Sans cette ligne, l'écart passerait pour une incohérence.
- **Les tests sont écrits avant le code.** Les 76 cas de niveau domaine et application sont automatisés en PHPUnit, et **tous au rouge**. C'est attendu et assumé : le socle technique est monté demain, et chaque test nomme en clair la classe de production qui lui manque.
- **Aucune classe de production écrite aujourd'hui**, alors que quelques interfaces vides auraient suffi à réduire le rouge. Cela reviendrait à produire du code hors du cadre de délégation que nous venions d'écrire, et à rendre la matrice verte sans que rien ne fonctionne.
- **Les tests figent l'API de production.** Six noms viennent de `architecture.md` §3, huit sont fixés par les tests. Ces huit sont à arbitrer en équipe avant la première tâche de demain, faute de quoi l'agent inventera les siens et les 76 tests resteront rouges pour une mauvaise raison.
- **Trois ports, et trois seulement** : `Horloge`, `Notificateur`, `PrestataireDePaiement`. Le domaine les définit, l'infrastructure les implémente.
- **Toute la technique tient dans trois fichiers** : `JeuDeDonneesDeReference` porte les chiffres, `MondeDeTest` les préconditions, `CasDapplication` les doublures. Un cas de test ne connaît rien d'autre, ce qui le laisse s'écrire en langage métier. Ce sont aussi les deux seuls points à rebrancher à Doctrine et à `KernelTestCase` quand le socle existera.
- **Le monde d'un test est monté par les services applicatifs réels**, jamais par un raccourci de test : un monde monté autrement ne prouverait rien.
- **Les montants ne s'écrivent plus, ils se composent.** Un cas qui attend 160 € l'exprime par « deux adultes et deux enfants », et le domaine calcule. Un montant écrit en dur laisserait un test passer au vert sur une grille tarifaire fausse.
- Les ruptures de traçabilité passent de **81 à 5**, toutes déclarées : trois cas de bout en bout qui relèvent de Behat et supposent un socle déployé, deux spécifications au statut brouillon.

**Critiques de l'IA acceptées.**
- Le §9 de la stratégie se contredisait : il annonçait « une classe par spécification » et donnait `SPEC-BOOKING-03` en exemple, dont les huit cas vivent sur trois niveaux → règle corrigée en « une classe par spécification **et par niveau** », le niveau commandant le rangement - `7763349`
- Écrire les montants en dur dans le montage du monde laissait passer un calcul de tarif faux → le montant est désormais calculé par le domaine, et le cas n'exprime que la composition - `e535d65`
- Un identifiant de cas écrit dans un commentaire aurait été compté comme un test par `tools/traceability.sh`, qui lit tout `tests/` → les identifiants ne vivent que dans les noms de méthode. Vérifié : le script ne remonte que les 76 vrais noms - `cdacd6a`
- `CASE-BOOKING-16` annonçait « une réservation dauphins de 60 € pour 1 adulte et 1 enfant », soit 80 € au tarif de référence, et un surplus perdu de 90 € au lieu de 70 € → cas corrigé. L'écart n'apparaissait qu'en posant le chiffre dans un test - `5645879`

**Critiques de l'IA refusées, et pourquoi.**
- Aucune aujourd'hui, et la raison est structurelle plutôt que flatteuse : les arbitrages de conception ont été rendus à J5 et J6, ordre des paliers, horloge injectable, organisation de `tests/`. La journée a consisté à les exécuter. Le seul jugement de l'équipe portait sur le grain des commits, et il a servi, voir la rubrique suivante.

**Erreurs produites par l'IA et détectées.**
- Le plan de commit désignait les fichiers **par motif** plutôt que par la liste réelle des cas. Le premier commit du dernier lot a ainsi emporté quatre classes qui appartenaient à d'autres commits, dont trois du domaine ADMIN, en laissant leurs quatre fichiers de cas derrière lui. Un même plan annonçait par ailleurs « quatorze cas » pour un lot qui en contenait dix → **repéré par l'équipe en demandant la liste exacte des cas de chaque commit, avant de pousser**. Corrigé par un `git reset HEAD~1`, aucun commit n'étant encore parti, puis refait en sept commits vérifiés un par un.
- Le test de `CASE-CANCEL-05` attendait 160 € pour deux adultes et un enfant en sortie dauphins, soit 130 € au tarif de référence. L'erreur était invisible tant que le montant était écrit à la main → repérée en passant aux montants composés, corrigée en ajustant la composition - `78c4c11`

**Ce qui a été généré aujourd'hui.**
- 25 plans de délégation, `docs/delegation-SPEC-*.md`, 81 tâches - `4eee4a4` à `8f07933`, `d78a0b3`
- `composer.json`, `phpunit.xml.dist`, deux suites de test - `c94145e`
- Socle de test : `tests/JeuDeDonneesDeReference.php`, `tests/MondeDeTest.php`, `tests/CasDapplication.php`, `tests/Doublures/` - `cdacd6a`, `e535d65`, `6f344ae`
- 76 tests PHPUnit répartis en 36 classes, `tests/Domaine/` et `tests/Application/` - `e949c9d`, `0e68db7`, `6cbc389`, `5645879`, `33c3dc1`, `f5ca249`, `ea059ab`, `065c7f2`, `210b017`, `204553c`
- `docs/strategie-de-test.md` §9 corrigé, `docs/traceability-trous.md`, `docs/traceability.md` régénérée - `7763349`, `ba62a7c`, `1f43e88`

**Questions ouvertes pour le client.**
- **Le texte des trois messages automatiques** devient bloquant pour le contenu, et non plus seulement pour la rédaction : les tests vérifient aujourd'hui qu'un message part, sur quel canal, à quel instant, avec quelle langue et quelle prévision, mais aucun ne vérifie ce qu'il dit. Le jour où le texte arrivera, il faudra étendre ces tests.
- **Le fuseau horaire du lieu d'exploitation** n'a jamais été explicité. Les cas sont tous écrits en heure locale et le code de test porte une constante unique, aujourd'hui neutre, à changer quand le client répondra. Toutes nos règles étant horaires, c'est le point le plus silencieusement risqué du projet.
- Le nom exact de la plateforme d'envoi, suspendu aux trois vérifications d'ouverture de compte.
- Les neuf autres questions du §11, inchangées.

---

## J8 - 2026-08-19

**Présents.** Équipe complète de développeurs. Journée de production de code,
interrompue en fin d'après-midi par un sixième entretien client qui renverse
une exigence `Must`.

**Décisions.**
- **Un vingtième plan de délégation a été écrit pour le socle**, alors que le
  README ne l'exigeait pas : il n'impose un plan qu'avant la première tâche
  « sur la spécification désignée », et le socle n'est pas une spécification.
  Nous l'avons écrit quand même, parce que c'est lui qui fige l'API que les
  76 tests appellent, et parce qu'un socle produit sans plan au milieu de
  vingt-cinq se lirait comme un oubli plutôt que comme une décision.
- **Persistance réelle, Doctrine et MySQL**, plutôt que des dépôts en mémoire.
  Les dépôts en mémoire auraient fait passer les 66 tests applicatifs plus
  vite, mais auraient contredit `ADR-002`, `architecture.md` §5 et la
  stratégie de test, et vidé de leur objet les deux cas qui vérifient des
  règles portées par la base.
- **Mapping XML plutôt qu'attributs Doctrine.** `architecture.md` §2 range
  Doctrine dans « ce qui n'a rien à y faire » dans le domaine. Le prix est un
  fichier de correspondance par entité ; le gain est que les treize entités
  restent du PHP nu, lisible et testable sans framework.
- **Les trois ports portent l'identifiant du service, et non un alias.** Un
  alias est résolu à la compilation : les cas de test n'auraient plus pu y
  substituer leurs doublures.
- **Deux adaptateurs échouent bruyamment.** `Notificateur` et
  `PrestataireDePaiement` devaient être liés pour que le conteneur compile,
  mais ni Brevo ni Stripe ne sont intégrés. Ils lèvent une exception nommant
  l'ADR ou la spécification qui doit livrer le vrai adaptateur : un envoi ou
  un encaissement silencieusement perdu coûterait bien plus cher.
- **Les 76 tests sont au vert**, 317 assertions. Les trois cas d'usage `Must`
  fonctionnent de bout en bout.
- **Le code ne sera pas modifié après `CR-06`**, décision prise à réception de
  l'analyse d'impact. Trois questions bloquantes sont sans réponse, douze
  tests verts deviendraient faux, et la chaîne documentaire pèse 30 % de la
  note contre 12 % pour le code. C'est un arbitrage, et il est écrit au §9 de
  `impact-CR-004.md` pour pouvoir être défendu ou contesté.

**Critiques de l'IA acceptées.**
- Le plan du socle annonçait 19 types de contrat ; l'agent en a dénombré 24 en
  écrivant, quatre n'étant atteignables que comme valeurs de retour → tableau
  « Après » du plan renseigné `repris` avec le motif, plutôt qu'un `conforme`
  qui n'aurait rien observé - `41ba442`
- `doctrine:migrations:diff` ne produit ni la colonne générée du naturaliste
  ni aucune contrainte `CHECK` → les trois éléments ont été écrits à la main
  et **vérifiés en SQL** : six sondes, six refus attendus - `734226b`
- `ExporterLePlanning` ne produit pas de PDF et `CASE-ADMIN-06` ne peut pas le
  voir, puisqu'il interroge une valeur constante → déclaré dans
  `traceability-trous.md` plutôt que masqué - `a690eab`
- Écrire une branche « si le créneau était en alerte » dans
  `EnregistrerUneIssueDannulation` laisserait croire que le barème dégressif
  vit quelque part dans le code → aucune branche, le barème reste appliqué à
  la main par le gérant - `0653f25`

**Critiques de l'IA refusées, et pourquoi.**
- Aucune, pour le troisième jour consécutif. L'en-tête de ce journal prévient
  qu'un journal parfaitement propre peut vouloir dire qu'on a tout pris : nous
  le notons plutôt que de l'ignorer. L'explication tient à la nature des trois
  journées, J7 et J8 ayant consisté à exécuter des arbitrages rendus à J5 et
  J6. **Trois choix ont bien été tranchés par l'équipe contre les options que
  l'agent avait posées** : la persistance réelle, le vingtième plan de
  délégation, et la profondeur plutôt que la largeur sur le périmètre. Ce sont
  des arbitrages, pas des refus, et la distinction est honnête.

**Erreurs produites par l'IA et détectées.**
- L'agent a affirmé qu'une réservation à moins de 24 heures du départ était
  impossible sur les créneaux de 7h et 10h, et que la demande du client ne
  concernait donc que celui de 14h. **C'est faux** : pour un départ à 7h, la
  fermeture à midi la veille tombe 19 heures avant, et la fenêtre existe donc
  bien, large de cinq heures → repéré en rédigeant `CR-06`, avant que
  l'analyse d'impact ne s'appuie dessus. Corrigé, et l'ambiguïté réelle
  qu'elle masquait est consignée au §6 de `CR-06`.
- Du **code de production dépendait d'une constante de test** :
  `SortieRepository` importait `App\Tests\JeuDeDonneesDeReference` pour lire
  le fuseau horaire → repéré par un contrôle mécanique,
  `grep -rn 'App\\Tests' src/`, corrigé en créant `FuseauDexploitation` dans
  le domaine - `fe0ed80`
- Les 76 tests instanciaient les services applicatifs avec
  `new Service($horloge)`, convention fixée à J7 avant que le socle n'existe.
  Un service qui parle à la base reçoit ses dépôts, et cette liste ne regarde
  pas le cas de test → 94 instanciations converties en lecture du conteneur,
  **sans qu'aucune assertion ne bouge** - `bbe0e2f`
- Trois assertions écrites à J7 étaient plus larges que le cas qu'elles
  servaient : deux vérifiaient qu'aucun encaissement n'avait eu lieu alors que
  le cas parle d'un client précis, une comptait tous les messages alors que le
  cas exclut explicitement le rappel → recentrées sur ce que le fichier de cas
  décrit - `6b59786`, `eba6759`
- **`compose.yaml` déclare PostgreSQL 16** alors qu'`ADR-002` retient MySQL et
  que la migration est écrite en dialecte MySQL, `AUTO_INCREMENT`, `utf8mb4`
  et `DROP FOREIGN KEY` : elle échouerait entièrement sur ce conteneur. Le
  fichier a été produit par la recette Symfony pendant l'installation et
  commité sans relecture → **converti en MySQL 9.3 le jour même**, avec
  `compose.override.yaml` qui exposait le port 5432, et une variante
  documentée dans `.env` pour ceux qui travailleront sur le conteneur plutôt
  que sur un MySQL local. Les marqueurs de recette sont conservés, et
  l'en-tête du fichier dit pourquoi il s'écarte de ce que la recette produit.

**Ce qui a été généré aujourd'hui.**
- Vingtième plan de délégation, `docs/delegation-SOCLE.md` - `1fd48f1`
- Socle : Symfony 8, Doctrine, treize entités en PHP nu, mapping XML, deux
  migrations dont les trois éléments manuels - `dcef16f`, `c787115`,
  `734226b`, `5fc6233`, `29a36e2`, `916d05e`, `ffa947b`
- 24 types de contrat, 12 politiques et services de domaine, 30 services
  applicatifs, 8 dépôts - `41ba442`, `18255d4`, `fe0ed80`, `ca42af8`,
  `6f1c01d`, `0a99c87`, `4057885`, `87cbf39`, `a4a9ed9`, `91ed181`,
  `3d8b113`, `e828447`, `0653f25`, `807f20b`
- Catalogues de traduction français et anglais, gabarits des trois messages
  **provisoires** faute de rédaction fournie - `807f20b`
- `docs/compte-rendu-entretien-06.md` et `docs/impact-CR-004.md` - `58d08f8`,
  `49acdc0`
- Mises à jour documentaires : `mcd-mld.md` §10, `architecture.md` §4,
  `traceability-trous.md`, les 26 tableaux « Après » - `b4105cf`, `66ad2f8`,
  `a690eab`, `b142809`

**Questions ouvertes pour le client.**
- **Trois questions bloquantes** empêchent d'écrire la règle de plafonnement :
  le taux de retenue en deçà de 24 heures, tranche que le barème n'a jamais
  couverte ; le sort de la part d'acompte qui excède la commission dans les
  deux tranches hautes ; et l'interdiction ou non du paiement en ligne pour
  une réservation tardive.
- Cinq autres questions au §8 de `CR-06`, dont la facture unique, qui
  contredit `REQ-018` en déléguant la facturation à un prestataire qui ne
  verra jamais un solde encaissé au quai.
- La fenêtre de paiement du solde est une **déduction d'équipe**, la seule
  réponse de `CR-06` que le client n'ait pas donnée lui-même.
- Le texte des trois messages automatiques, toujours pas fourni. Des gabarits
  provisoires sont écrits, ne disant que ce que les spécifications
  établissent.

### J8, second créneau - la descente du CR-06

Le sixième entretien est arrivé en fin d'après-midi. Plutôt que de le laisser
pour le lendemain, l'équipe a fait descendre le changement **le soir même**,
du cahier des charges jusqu'aux tests. Ce qui suit s'ajoute donc à la journée
ci-dessus, et non à J9.

**Décisions.**
- **La chaîne descend, le code ne bouge pas.** Cahier des charges v6, dix
  spécifications, modèle de données, UML, `ADR-006`, 91 cas de test et
  85 tests. Le code reste en v5, et le lot qui le mettra à jour est
  conditionné à un **point d'arrêt le 21/08 à 09h00**. Trois arguments, écrits
  au §9 de `impact-CR-004.md` : trois questions client sans réponse, douze
  tests verts qui deviendraient faux, et un barème qui note la chaîne
  documentaire 30 % contre 12 % pour le code.
- **Trois hypothèses d'équipe** rendent la rédaction possible malgré les
  questions ouvertes : l'acompte est retenu en totalité en deçà de 24 heures,
  la part d'acompte excédant la commission est remboursée, et la fenêtre de
  paiement est uniforme sans exception pour une réservation tardive. Toutes
  trois sont sourcées `déduit` et reposées au §11.
- **Une table plutôt que trois colonnes.** `PAIEMENT` porte les deux
  transactions et l'historique des pointages. Trois colonnes sur `reservation`
  auraient dit où en est une réservation, pas comment on y est arrivé, et
  `REQ-113` exige qu'un pointage annulé laisse une trace.
- **Les tests v6 sont écrits avant le code**, comme les 76 premiers l'ont été
  à J7. 21 tests rouges sur la branche `feat-modification-acompte`, qui ne
  rejoindra `main` qu'au vert.

**Critiques de l'IA acceptées.**
- Les cas de test étaient repris en v6 pendant que leurs tests restaient en
  v5 : **douze tests passaient au vert en affirmant le contraire de leur
  cas**. L'agent a signalé que c'était le seul artefact activement trompeur du
  dépôt → les 17 assertions concernées ont été reprises le soir même -
  `8baf585`
- `CASE-BOOKING-39` passait aussi bien en v5 qu'en v6, donc ne prouvait rien →
  assertion ajoutée sur le solde restant dû du sixième inscrit - `581f581`
- Le plafonnement de la retenue n'était défini que dans une tranche du barème
  sur trois ; dans les deux autres, l'acompte excède la commission et une part
  revient au client → `CASE-ADMIN-16` exerce les deux sens, et la question 19
  est posée au client - `8e50b77`
- `reservationPayee` était devenue un nom faux, le client ne payant plus que
  30 % → renommée `reservationConfirmee`, 52 occurrences - `8baf585`

**Critiques de l'IA refusées, et pourquoi.**
- **Prévenir le client qu'un solde lui reste dû.** L'agent a relevé qu'aucun
  message ne le lui dira, alors que le rappel part précisément à l'ouverture
  de la fenêtre de paiement. Refusé : le gérant l'a demandé deux fois,
  `CR-06/Q16` et `Q17`. La conséquence est écrite dans la règle de
  `SPEC-BOOKING-12`, elle n'est pas corrigée à sa place.
- **Interdire le pointage d'un solde après le départ de la sortie.** Refusé :
  un jour chargé, le gérant régularise au retour, et le lui interdire
  produirait des réservations éternellement non soldées. Écrit en cas limite 6
  de `SPEC-ADMIN-07`.

**Erreurs produites par l'IA et détectées.**
- L'agent a affirmé qu'une réservation à moins de 24 heures était impossible
  sur les créneaux de 7h et 10h. **Faux**, et il s'en est aperçu en rédigeant
  `CR-06` : pour un départ à 7h, la fermeture à midi la veille tombe 19 heures
  avant, la fenêtre existe et fait cinq heures. Corrigé avant que l'analyse
  d'impact ne s'appuie dessus.
- L'agent a d'abord recommandé de **ne pas descendre la chaîne**, puis s'est
  contredit une heure plus tard en constatant que la branche existait déjà et
  que l'ordre de la chaîne servait de filet. L'équipe a tranché sur la seconde
  version, mais le revirement est noté : une recommandation rendue trop vite
  n'avait pas pesé le dépôt réel.
- `CASE-BOOKING-39` a été écrit avec l'ancien constructeur de
  `ControlerSeuilDeMaintien`, à trois arguments → repéré par un rouge dont le
  message ne parlait pas de la v6 mais d'un `TypeError`, corrigé - `581f581`
- Un bloc de code parasite a été laissé dans `SoldeDeLaReservationTest`, un
  `new AnnulerCreneau(...)` inutile issu d'une réécriture → repéré au contrôle
  de syntaxe, retiré avant commit.
- Deux artefacts datés **J9** alors que tout s'est passé à J8, `ADR-006` et la
  ligne des 21 tests rouges → corrigés en écrivant cette entrée.

**Ce qui a été généré aujourd'hui, second créneau.**
- `docs/compte-rendu-entretien-06.md`, `docs/impact-CR-004.md` - `58d08f8`,
  `49acdc0`
- Cahier des charges v6, `REQ-108` à `REQ-119`, `R-25` à `R-30`, six questions
  au §11 - `1c9bfb2` et le commit qui le précède
- `SPEC-BOOKING-07` refondue, `SPEC-BOOKING-12` et `SPEC-ADMIN-07` créées, sept
  spécifications reprises - `916d0a6`, `be32351`, `2447e1d`
- Table `PAIEMENT` au MCD et au MLD, `etats-reservation.puml`, séquence de
  réservation en v2, `ADR-006` - `d11260b`, `73037f5`, `65371ba`, `8442411`
- Douze cas repris, neuf créés, quatre étendus - `c2eddea`, `8e50b77`,
  `48a54b8`
- Dix-sept assertions reprises, neuf tests créés - `8baf585`, `581f581`
- Déclarations de traçabilité - `0237557`, `ad83b49`

**Questions ouvertes pour le client.**
- Les huit questions du §8 de `CR-06`, dont trois sont couvertes par une
  hypothèse d'équipe et cinq restent entières.
- La plus coûteuse est la facture unique acquittée, `REQ-119`, qui contredit
  `REQ-018` : un solde encaissé au quai est invisible du prestataire, donc
  d'une facture qu'il émettrait.
- La « boutique » évoquée au point 4 de l'entretien reste un lieu inconnu de
  la mission.

---

## J9 - 2026-08-20

**Présents.** Équipe complète. Journée à deux temps : un septième entretien
client le matin, puis la descente de ses réponses jusqu'au code.

**Le choix de la journée.** Deux lots s'excluaient sur le papier : descendre
`CR-07` dans les documents, ou faire passer au vert les 21 tests rouges laissés
la veille. Nous avons tenté les deux, contre l'avis initial du poste de travail
qui recommandait de choisir. Les deux ont abouti, et la raison est que les deux
lots se recouvraient plus que prévu : quatre des réponses de `CR-07` portaient
précisément sur des règles que le code de la veille laissait en suspens. Les
descendre revenait à écrire les constantes qui manquaient.

**Décisions.**
- **Le barème de remboursement entre dans le code**, alors qu'il en était
  explicitement tenu à l'écart depuis J6. Il n'y était pas parce que nous ne
  connaissions pas ses paliers, et non par principe : `EnregistrerUneIssue`
  laissait le gérant saisir un montant. `CR-07/Q11` a donné les trois tranches,
  `Politique\RetenueDannulation` les porte, et la saisie du gérant reste
  possible, en geste commercial plutôt qu'en règle.
- **Le barème n'est pas uniforme, et il ne sera pas « harmonisé ».** Deux
  tranches portent sur l'acompte, la troisième sur le prix total. C'est ce que
  le client a dit, cela paraît bancal, et le docblock de la classe le dit en
  toutes lettres pour que personne ne le lisse plus tard.
- **La fenêtre de règlement s'ouvre avec le lien, pas 24 heures avant le
  départ.** Nous avions écrit les deux règles la veille sans voir qu'elles se
  contredisaient : elles coïncident sur les créneaux de 7h et 10h, et divergent
  de sept heures sur celui de 14h. Le client a tranché en une phrase.
- **`SPEC-CANCEL-07` a été écrite en fin de journée**, pour le lien de
  règlement. C'est la trente-deuxième et dernière spécification, et la seule
  qui demande du code sans avoir de plan de délégation : elle n'a pas été
  confiée à un agent, il n'y avait rien à cadrer.
  Écrire son plan après coup aurait fabriqué une prévision à partir du
  résultat, ce que la colonne « écart » de J10 cherche justement à mesurer.
- **Les trois scénarios de bout en bout sont écrits, et laissés non
  exécutables.** La table des trous les promettait « au plus tard à J9 » : une
  échéance intenable, puisqu'ils pilotent des écrans et qu'il n'y en a pas.
  Plutôt que de reconduire la promesse, les scénarios ont été écrits dans
  `tests/BoutEnBout/`, le Gherkin des cas repris tel quel, et **Behat 4
  installé**. La v3 ne l'était pas : aucune de ses trente-deux versions
  n'accepte Symfony 8, `v3.32.0` exigeant encore `symfony/config ^5.4 || ^6.4
  || ^7.0`. Seule la branche 4, en `v4.0.0-alpha1`, résout, et c'est un tag qui
  a été épinglé plutôt que la branche mouvante, la veille d'un gel. Behat
  rapporte 3 scénarios et 21 étapes `undefined` : rien n'est vérifié, et c'est
  le compte rendu honnête de l'état du projet. Ce que l'installation apporte
  dès aujourd'hui est l'analyse du Gherkin, qui vaut relecture.
- **Le contrôle de traçabilité aurait avalé ces trois fichiers.** Il compte
  comme automatisé tout fichier de `tests/` portant l'identifiant d'un cas :
  les trois `.feature`, qui ne vérifient rien, auraient fait tomber le compte
  de **5 ruptures à 2**. Mesuré, pas supposé. Les scénarios portent donc
  `@socle-absent`, l'outil les écarte et les signale à part, et le compte tient
  à 5. C'est la deuxième fois en deux jours qu'un contrôle est corrigé parce
  qu'il vérifiait la présence d'un fichier au lieu du fait lui-même, après le
  port qui était « déclaré » plutôt que « qui répond ».
- **La recette Symfony de `symfony/translation`**, tirée par Behat, a écrit un
  `config/packages/translation.yaml` avec `default_locale: en`. Le projet
  n'utilise pas le traducteur Symfony, il injecte son dossier de traductions
  dans ses propres services : le fichier a été retiré, avec le
  `translations/.gitignore` de la même recette. Une dépendance de test avait
  changé la langue par défaut de l'application.
- **La colonne `paiement.pointe_par` reste nulle.** Elle existe pour
  `SPEC-ADMIN-07` AC-3, mais `SessionDeGestion` ne porte qu'un jeton : aucun
  service ne sait quel compte est connecté. Laissée vide plutôt que remplie
  d'une valeur inventée.

**Ce qui a été refusé à l'agent.**
- **Aligner les trois tranches du barème sur une même assiette.** Refusé deux
  fois. La formule du client n'est pas homogène ; la rendre homogène change les
  montants rendus, et c'est de l'argent réel.
- **Supprimer la ligne de pointage rétractée au lieu de la marquer.** Refusé :
  `REQ-113` demande une trace, et un `DELETE` rend le pointage invisible autant
  que réversible.
- **Écrire un « montant zéro » en dur dans `EnregistrerUneAbsence`.** Plus court
  et faux : le service pose l'issue et laisse le barème décider, pour que le
  jour où le gérant assouplira ses tranches, l'absence suive sans qu'on y
  touche.
- **Remplir `pointe_par` avec l'e-mail du gérant de référence.** Refusé : ce
  compte est une donnée de test, pas une identité de session.

**Erreurs produites par l'IA et détectées.**
- Les citations de `CR-07` ont été écrites sur une numérotation inventée,
  `Q00` à `Q06`, alors que le compte rendu numérote de `Q01` à `Q12`. Onze
  renvois faux dans le cahier des charges, l'analyse d'impact et trois
  docblocks. **Détectées par `tools/traceability.sh`**, qui vérifie que chaque
  question citée existe dans le compte rendu : c'est le premier jour où ce
  contrôle attrape quelque chose que personne n'avait vu.
- `CASE-ADMIN-19` créait une réservation le matin du départ, à une heure où le
  créneau est fermé depuis la veille à midi. Le test échouait sur un `null`
  sans rapport avec ce qu'il vérifiait. La réservation a été remontée dans les
  préconditions, où le fichier de cas la plaçait déjà.
- La doublure `PaiementSimule` rendait le **premier** encaissement d'une
  réservation. Sans ambiguïté tant qu'il n'y en avait qu'un ; depuis l'acompte,
  elle répondait 30 € à une question qui portait sur les 70 € du solde.
  `dernierMontantEncaisse()` ajoutée, l'ancienne méthode documentée.
- `PointerLeSolde` a d'abord renseigné `pointe_par` avec le lieu d'embarquement
  du créneau, en appelant une méthode qui n'existe pas. La colonne dit **qui**,
  pas **où**.
- **Le dépôt n'était pas clonable.** Chloé, sur un poste neuf, n'a pas pu
  lancer `make presentation` : `config/packages/` était ignoré par git, avalé
  par la règle NuGet `**/[Pp]ackages/*` du modèle Visual Studio dont est tiré
  notre `.gitignore`. Cinq fichiers de configuration Symfony manquaient au
  dépôt, dont `doctrine.yaml`, et `composer install` ne les régénère pas. Le
  même modèle avait déjà avalé `.env` le matin. Reproduit sur un clone neuf,
  corrigé, et revérifié : clone, `composer install`, 87 tests verts.
- **Le contrôle du port ne contrôlait pas le bon fait.** Une fois le dépôt
  clonable, le même poste a buté sur un `Access denied for user
  'root'@'localhost'`. Sur macOS, un MySQL du poste lié à `127.0.0.1:3306`
  **cohabite sans conflit** avec un conteneur lié à `0.0.0.0:3306` : Docker
  démarre, annonce son port, et l'autre serveur capte les connexions. Vérifié en
  provoquant l'erreur : le conteneur rapporte `'root'@'192.168.65.1'`, la
  passerelle Docker, jamais `localhost`. Le `Makefile` vérifiait que Docker
  *déclarait* 3306, pas **qui répondait**. Première correction : filtrer `lsof`
  sur le mot « docker ». **Fausse bonne idée**, et Chloé l'a montré en une
  ligne : elle utilise OrbStack, que ce filtre prenait pour un intrus. Une liste
  de noms de moteurs est toujours en retard d'un outil. La version retenue prouve
  le fait au lieu de le deviner : `@@hostname` rendu par MySQL est l'identifiant
  du conteneur qui l'héberge, et on le compare à celui que rend le moteur. Éprouvé
  dans les deux sens, avec un second serveur MySQL lancé pour usurper la place.
- **Un `APP_ENV` exporté dans le terminal mettait les 76 tests d'application au
  rouge**, avec un message qui ne parlait ni d'environnement ni de base :
  « You must set the KERNEL_CLASS environment variable ». `phpunit.xml.dist`
  forçait pourtant `APP_ENV=test`. La balise `<env force>` de PHPUnit n'écrit
  que `putenv()` et `$_ENV`, jamais `$_SERVER`, alors que `Dotenv::bootEnv()`
  lit `$_SERVER` **en premier** : la valeur du shell gagnait, `.env.dev` était
  chargé au lieu de `.env.test`, et `KERNEL_CLASS` n'existait pas. Une balise
  `<server>` a été ajoutée à côté de la `<env>`. Éprouvé avec `APP_ENV` valant
  successivement rien, `dev`, `demo` et `prod` : 87 verts dans les quatre cas.
  Sans cela, un terminal ouvert la veille suffisait à faire échouer la
  démonstration de J10 pour une raison étrangère au code.
- Le cas neuf a été écrit **par-dessus `CASE-CANCEL-20`**, qui existait déjà et
  couvrait tout autre chose. Repéré parce que `git status` le montrait
  *modifié* et non *nouveau* ; restauré depuis `HEAD`, et le cas neuf renuméroté
  `CASE-CANCEL-25`. Le contrôle de traçabilité n'aurait pas attrapé l'écrasement :
  le numéro existait toujours, et un test portait toujours son nom.

**Ce qui a été généré aujourd'hui.**
- `docs/compte-rendu-entretien-07.md`, `docs/impact-CR-005.md`
- Cahier des charges **v7** : `REQ-119` renversée (deux factures), `REQ-111`
  précisée, `REQ-120` et `REQ-121` ajoutées, `R-31` à `R-33`, quatre questions
  du §11 fermées
- `SPEC-BOOKING-12` v2, `SPEC-ADMIN-06` v3 avec son barème en tableau,
  `SPEC-CANCEL-07` neuve
- `CASE-BOOKING-41` et `CASE-ADMIN-16` réécrits, `CASE-CANCEL-25` créé
- Trois politiques de domaine, `Service\EtatDuReglement`, l'entité `Paiement`,
  son mapping et sa migration
- Cinq services applicatifs neufs, six repris
- Les deux plans de délégation manquants, et leurs tableaux « Après »
- `Interface\Console\DemontrerLeParcoursCommand` et les trois adaptateurs de
  l'environnement `demo`
- Un `Makefile`, dont `make presentation` : démarrage de la base, contrôle du
  port et de qui répond dessus, création et migration des deux schémas, puis
  les trois contrôles. Idempotent, vérifié depuis un volume Docker détruit
- `README_J10.md`, la procédure de présentation : quoi lancer, dans quel ordre,
  ce qu'on montre et ce qu'on assume, avec les cinq ruptures à annoncer avant
  qu'on les trouve
- `phpunit.xml.dist` : la balise `<server>` qui neutralise un `APP_ENV` de shell
- `tests/BoutEnBout/` : les trois scénarios de bout en bout et leur contexte
  vide, `behat.dist.php`, Behat 4 en dépendance de développement, la cible
  `make behat`, et le garde-fou `@socle-absent` dans `tools/traceability.sh`

**La démonstration de J10.** Il n'y a aucun écran, et il n'y en aura pas : en
écrire sans spécification d'écran produirait du code non couvert le jour même
où la règle 1 est notée. Le parcours se montre donc de deux façons, décidées ce
soir : en exécutant les tests qui le protègent, et par une **commande console**
qui rejoue « réserver, verser l'acompte, solder » en annonçant à chaque étape sa
spécification et son cas de test. Elle n'enchaîne que des services applicatifs
existants et tourne dans une transaction jamais validée, comme les 87 cas de
test : elle est rejouable indéfiniment et ne laisse rien en base. La couche
`Interface`, qui n'existait pas, porte donc désormais une entrée.

**État du dépôt en fin de journée.** 87 tests, tous verts. 5 ruptures de
traçabilité, les mêmes qu'au 18 août : trois cas de bout en bout sans scénario
Behat et deux `SPEC-NFR` sans cas. `doctrine:schema:validate` rend deux `[OK]`.

**Questions ouvertes pour le client.** Les cinq du §8 de `CR-07`, dont trois
touchent le lien de règlement : son heure d'envoi est-elle réglable comme celle
de l'alerte, que contient le message, et le lien expire-t-il. Les deux autres
portent sur la facture, seule exigence de `CR-07` qui reste sans code.
