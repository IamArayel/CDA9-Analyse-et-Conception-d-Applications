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
- `docs/uml/use-cases.puml` (note de concurrence sur UC3)
- `docs/traceability.md` (régénéré)

**Questions ouvertes pour le client.**
- Les quatre questions nouvelles du §11 (horaires d'alerte réglables ou
  figés, heure limite d'annulation, message associé à une annulation faute
  de 6 inscrits, durée d'immobilisation des places et consentement aux SMS).
- Le texte des trois messages automatiques, en français et en anglais,
  toujours pas fourni, y compris pour le rappel qui existe depuis `CR-02`.
- La fusion du bon cadeau et de l'avoir (§11, question 8), qui conditionne
  une ou deux tables au modèle de données.
- `CR-05` doit être relu par la personne ayant mené l'échange : comme
  `CR-04`, il repose sur des propos rapportés et non sur une source brute.
