# J10 - procédure de présentation

- **Ti Baleine**, équipe Le Trio (Chloé Baisse, Arnaud Maxime, Anthony Dégeilh).
- Vendredi 21/08/2026,
- **dépôt gelé à 11h30**, présentation devant la promo.

Ce document est la seule chose à ouvrir demain matin. Il dit quoi lancer, dans
quel ordre, ce qu'on montre, et ce qu'on assume.

---

## 1. Avant tout, une seule commande

```bash
make presentation
```

Elle fait tout, dans l'ordre, et elle est **idempotente** : la rejouer sur une
machine déjà prête ne fait que revérifier.

| Elle enchaîne | Attendu |
|---|---|
| démarre le conteneur et attend qu'il réponde | `attente ok` |
| **vérifie que la base est bien sur 3306** | `base publiée sur 0.0.0.0:3306` |
| crée `ti_baleine` et `ti_baleine_test` si elles manquent, puis migre | `[OK] Already at the latest version` |
| affiche à quelle base on parle réellement | `ti_baleine_test`, `3306` |
| passe les tests | `OK (87 tests, 360 assertions)` |
| régénère la traçabilité | `5 rupture(s)`, pas une de plus |
| valide le mapping contre le MLD | deux `[OK]` |

Vérifié le 2026-08-20 **volume Docker détruit**, donc depuis une machine
réellement vierge : 37 secondes, 87 tests verts.

Les autres cibles, avec `make` seul pour la liste :

```bash
make demo          # rejoue le parcours complet, cf. §2
make verifier      # les trois contrôles, sans toucher à la base
make arreter       # arrête la base
```

### Pourquoi une vérification du port

**La base du projet est le conteneur de `compose.yaml`, publié sur le port 3306
de l'hôte.** C'est vrai depuis le 2026-08-20 seulement : avant, Docker tirait un
port au hasard à chaque démarrage et le projet visait en réalité un MySQL
installé sur le poste. Deux bases coexistaient sans que rien ne le dise, et cela
a coûté une fausse alerte la veille du rendu. `make presentation` s'arrête net si
le port n'est pas le bon, plutôt que de laisser croire que la base est démarrée.


> **Si `make presentation` n'est pas vert, on le dit.** Un rouge annoncé et
> expliqué coûte moins qu'un rouge découvert par le formateur.

---

## 2. La démonstration

Il n'y a **aucun écran**, et c'est une décision, pas un oubli : écrire des vues
sans spécification d'écran aurait produit du code non couvert le jour même où la
règle 1 est notée. Le parcours se montre donc de deux façons.

### 2.1 La commande, à lancer en direct

```bash
make demo
```

Elle rejoue **« réserver, verser l'acompte, solder »** en huit étapes et annonce
à chaque fois **la spécification et le cas de test** qui la protègent. C'est
exactement ce que le barème exige : une fonctionnalité montrée sans spec ni test
ne compte pas.

Ce qu'elle affiche, dans l'ordre :

| Étape | Spécification | Cas de test | Ce qu'on souligne à voix haute |
|---|---|---|---|
| Le gérant déclare flotte et tarifs | `SPEC-ADMIN-05`, `SPEC-ADMIN-02` | `CASE-ADMIN-12`, `CASE-ADMIN-05` | rien n'est inséré en base à la main |
| Il programme une sortie à 14h | `SPEC-BOOKING-02` | `CASE-BOOKING-25` | **mise en place** : ce geste n'a pas de spec propre, voir §4 |
| Marie réserve 2 places | `SPEC-BOOKING-01`, `SPEC-BOOKING-03` | `CASE-BOOKING-20`, `CASE-BOOKING-01` | 100 € dus, places immobilisées |
| **Elle verse son acompte** | `SPEC-BOOKING-07` | `CASE-BOOKING-38` | **30 € encaissés sur 100 € dus** |
| Le planning avant le solde | `SPEC-ADMIN-03`, `SPEC-ADMIN-07` | `CASE-ADMIN-06`, `CASE-ADMIN-18` | « solde À ENCAISSER » |
| Le lien part à 7h la veille | `SPEC-CANCEL-07` | `CASE-CANCEL-25` | par courriel seul |
| **Elle solde en ligne** | `SPEC-BOOKING-12` | `CASE-BOOKING-40` | **deux transactions, pas une** |
| Le planning après le solde | `SPEC-ADMIN-03`, `SPEC-ADMIN-07` | `CASE-ADMIN-06`, `CASE-ADMIN-18` | « solde réglé » |

**Trois choses à dire pendant qu'elle tourne :**

1. La commande **ne contient aucune règle métier**. Elle enchaîne des services
   applicatifs et affiche ce qu'ils rendent. Tout montant affiché vient du
   domaine ; s'il était calculé dans la commande, la démonstration ne prouverait
   rien.
2. Elle tourne dans une **transaction jamais validée**, comme les 87 cas de test.
   Elle se rejoue indéfiniment et ne laisse rien en base.
3. L'horloge avance de trois jours en une seconde parce qu'elle est **injectée**
   (`ADR-005`). C'est la même décision qui rend les tests déterministes.

### 2.2 Les tests, pour prouver ce que la commande montre

```bash
vendor/bin/phpunit --filter SoldeDeLaReservation   # SPEC-BOOKING-12
vendor/bin/phpunit --filter PointageDuSolde        # SPEC-ADMIN-07
vendor/bin/phpunit --filter LienDeReglement        # SPEC-CANCEL-07
```

---

## 3. L'état du projet, chiffré

À citer tel quel, sans arrondir vers le haut.

| | |
|---|---|
| Cahier des charges | **v7**, 82 exigences, 7 comptes rendus d'entretien |
| Spécifications | **32**, dont 27 avec plan de délégation |
| Cas de test | **92**, dont 3 « manuel assumé » |
| Tests automatisés | **87 verts** : 11 domaine, 76 application, 360 assertions |
| Ruptures de traçabilité | **5** |
| Conception | 6 ADR, 8 diagrammes PlantUML dont le MCD et le MLD |
| Code | 118 fichiers PHP, aucune couche web |
| Analyses d'impact | 5, une par retour client structurant |

**Les 5 ruptures, à annoncer avant qu'on les trouve :**

- `CASE-BOOKING-08`, `-22`, `-35` : les trois cas **de bout en bout** n'ont pas
  de scénario Behat. Ils supposent un socle déployé, qui n'existe pas.
- `SPEC-NFR-05` et `-06` : sans cas de test, et il n'y en aura pas. Leurs
  critères sont des actions de projet (poser une question au client, consigner
  la réponse), pas des comportements logiciels.

---

## 4. Ce qu'on assume, et qu'on annonce nous-mêmes

Tout est dans [`docs/traceability-trous.md`](./docs/traceability-trous.md). Les
cinq qu'il faut savoir dire sans notes :

1. **Aucun écran.** La couche `Interface` ne porte qu'une commande console. Voir
   §2 pour la raison.
2. **`REQ-119`, les deux factures, n'est pas codée.** C'est la seule exigence de
   `CR-07` sans code. Elle est *Should*, elle touche la TVA, et une facture à
   moitié juste vaut moins qu'aucune facture. Quatre points restent d'ailleurs
   ouverts au §8 de `CR-07`, dont la date à porter sur la facture d'un solde
   encaissé au comptoir.
3. **`SPEC-CANCEL-07` n'a pas de plan de délégation.** Elle est née à J9 d'un
   trou constaté en fin de descente et a été écrite directement, sans agent : il
   n'y avait rien à cadrer. En rédiger le plan après coup fabriquerait une
   prévision à partir du résultat, ce que la troisième question de J10 cherche
   justement à mesurer.
4. **Programmer une sortie n'a aucune spécification.** `ProgrammerUneSortie`
   existe et porte deux règles réelles, la saison des baleines et l'unicité du
   naturaliste, mais **le client n'a jamais décrit le geste du gérant**. Le
   service n'existe que pour monter le monde des tests. Trouvé le 20/08 en
   vérifiant les identifiants annoncés par la commande de démonstration.
5. **`paiement.pointe_par` reste vide.** La colonne existe pour `SPEC-ADMIN-07`
   AC-3, qui veut savoir qui a pointé. `SessionDeGestion` ne porte qu'un jeton :
   aucun service ne sait quel compte est connecté. Laissée nulle plutôt que
   remplie d'une valeur inventée.

**Les trois adaptateurs de l'environnement `demo`** (horloge réglable,
notificateur qui n'écrit à personne, prestataire qui accepte tout) sont cantonnés
au bloc `when@demo` de `config/services.yaml`. En production, les deux ports
réels échouent bruyamment : un encaissement silencieusement perdu coûte plus
cher qu'une erreur visible.

---

## 5. Les trois questions obligatoires

### 5.1 « Quelle partie du code a été générée ? »

Le trailer `Generated-by:` des messages de commit répond seul :

```bash
git log --grep="Generated-by" --oneline | wc -l    # commits à contenu généré
git log --oneline | wc -l                          # commits au total
```

Le partage est constant depuis J7 et il est dans `README` §2 : l'équipe conduit
la découverte, spécifie, conçoit et **définit les cas de test** ; l'agent produit
les tests automatisés et le code ; chaque génération est relue.

### 5.2 « Quel est l'écart entre la prévision et le résultat ? »

Le tableau **« Après »** des 28 plans de délégation. L'écart principal est le
même partout et il vaut d'être dit franchement :

> Le découpage prévoyait **une tâche par cas de test, confiée séparément**. Dans
> les faits, le code a été produit **spécification par spécification** : un même
> service applicatif satisfait plusieurs cas, et le scinder aurait produit du
> code jetable entre deux passages. **L'écart tient à notre découpage, pas à
> l'agent.**

Deux écarts individuels valent la peine d'être cités :

- `SPEC-BOOKING-12` tâche 1 : **le plan décrivait une règle fausse**. Il demandait
  une fenêtre ouverte 24 heures avant le départ ; `CR-07/Q12`, reçu le lendemain,
  l'a fait ouvrir avec le lien de règlement, à 7h la veille.
- `SPEC-ADMIN-07` tâche 3 : le plan a tenu sur son point de vigilance (une table
  plutôt qu'un booléen), mais ne disait pas **combien** d'écritures un pointage
  rétracté devait laisser.

### 5.3 « Expliquez une spécification tirée au sort »

**C'est la note individuelle, de −4 à +1, et l'échelle est asymétrique :
expliquer une spécification de son propre projet est le niveau attendu, pas un
bonus.**

32 spécifications, tirage au hasard. Pour chacune, cinq choses à savoir dire :

| Ce qu'on demande | Où c'est |
|---|---|
| ce qu'elle dit | `specs/<domaine>.md`, section « Règle » |
| de quel besoin client elle vient | ses `REQ-0xx`, puis le `CR-0n/Qnn` en source |
| quel test la protège | `tests/cases/CASE-*.md`, ligne « Test automatisé » |
| où elle vit dans le code | le service applicatif ou la politique de domaine |
| ce qui a été arbitré | l'ADR, ou la section « Ce qui n'est pas défini » de la spec |

Le chemin se retrouve toujours de la même façon :

```bash
grep -rn "SPEC-BOOKING-12" docs/cahier-des-charges.md specs/ tests/cases/ src/
```

**À réviser en priorité**, parce que ce sont celles qui portent un arbitrage
qu'il faut pouvoir défendre :

- `SPEC-BOOKING-07` - l'acompte. `REQ-017` a été **renversée** en v6 : elle
  imposait le paiement intégral et l'interdisait explicitement.
- `SPEC-BOOKING-12` - la fenêtre de règlement. Deux règles se contredisaient sans
  qu'on le voie ; elles coïncident sur les créneaux de 7h et 10h et divergent de
  sept heures sur celui de 14h.
- `SPEC-ADMIN-06` - le barème. **Il n'est pas uniforme et ce n'est pas une
  erreur** : deux tranches portent sur l'acompte, la troisième sur le prix total.
  C'est ce que le client a dit, et `Politique\RetenueDannulation` le dit en
  toutes lettres pour que personne ne le lisse.
- `SPEC-ADMIN-07` - le pointage réversible. Pourquoi une table et non un booléen.
- `SPEC-BOOKING-03` - l'immobilisation des places. `ADR-003` : le verrou est pris
  **à la validation du formulaire**, pas à l'encaissement.

---

## 6. Trame de présentation

10 % de la note de groupe. Une seule règle : **chaque fonctionnalité montrée est
annoncée avec son identifiant de spécification et le test qui la protège.**

| Temps | Contenu | Qui |
|---|---|---|
| 2 min | Le besoin, en une phrase du client. Les 3 cas d'usage `Must` | |
| 3 min | La chaîne : `CR → REQ → SPEC → CASE → test → code`, et `tools/traceability.sh` qui la vérifie | |
| 4 min | **La démonstration** (§2), commande lancée en direct | |
| 3 min | **L'analyse d'impact** : `CR-06` renverse une exigence `Must` la veille du gel. Ce qu'on a descendu, dans quel ordre, et pourquoi jamais l'inverse | |
| 2 min | Ce qu'on assume (§4), dit par nous et pas découvert | |
| 1 min | Le pilotage de l'agent : l'écart des plans (§5.2) | |

**Le moment le plus fort du dossier est l'analyse d'impact**, 8 % à lui seul et
5 notes rédigées. `CR-06` est arrivé le soir de la journée d'implémentation et a
renversé `REQ-017`. Nous avons refusé de corriger le code d'abord : le cahier des
charges est passé en v6, puis les specs, puis le MCD, puis les cas, puis les
tests, puis le code. Les 21 tests rouges assumés pendant une nuit sont la preuve
que l'ordre a été tenu, et `main` n'a jamais reçu de rouge.

---

## 7. Si quelque chose casse

| Symptôme | Cause probable | Geste |
|---|---|---|
| **`Tests: 87, Assertions: 39, Errors: 76`** | les 11 tests de domaine passent, les 76 applicatifs meurent tous sur la connexion : **la base n'est pas joignable** | `make presentation`, qui démarre la base, la vérifie et rejoue les contrôles |
| `make presentation` : *port is already allocated*, ou « publiée sur ... et non sur 3306 » | un autre MySQL a pris 3306 avant Docker | `lsof -nP -iTCP:3306 -sTCP:LISTEN` pour voir qui, puis l'arrêter |
| `phpunit` : table inconnue | la base de test n'est pas migrée | `make bases` |
| `make demo` : environnement non autorisé | cache d'une ancienne version | `php bin/console cache:clear --env=demo` |
| `make demo` : nom de bateau déjà pris | la base de développement porte un « Ti Kap » | rien : la commande le détecte et le réutilise |

**Si la démonstration ne part pas du tout**, on se replie sur §2.2 : les trois
`--filter` prouvent exactement les mêmes règles, et un test vert est une preuve
plus forte qu'une sortie console.
