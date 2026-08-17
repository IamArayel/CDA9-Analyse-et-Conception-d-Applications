# Stratégie de test - Ti Baleine

**Version :** v1 - 2026-08-17 (J6)
**Périmètre :** cahier des charges v5, 29 spécifications réparties sur quatre
domaines, `ADMIN`, `BOOKING`, `CANCEL`, `NFR`.
**Gabarit de cas :** `tests/cases/TEMPLATE.md`

Ce document dit **ce que nous testons, à quel niveau, et surtout ce que nous
ne testons pas**. Il précède les cas de test : sans lui, on écrit des cas là
où c'est facile plutôt que là où c'est risqué.

---

## 1. Ce qui décide de ce que nous testons

Le risque, pas la couverture. Trois questions, dans cet ordre :

1. **Qu'est-ce qui coûte de l'argent au client si ça casse ?** Une place
   vendue deux fois, un remboursement qui ne part pas, un bon cadeau
   réutilisé.
2. **Qu'est-ce qui coûte la confiance d'un passager ?** Un client débité pour
   une sortie qu'il n'aura pas, un message d'annulation qui n'arrive jamais.
3. **Qu'est-ce que personne ne verra avant la production ?** Tout ce qui se
   déclenche seul, sans utilisateur devant l'écran.

Les cinq spécifications les plus exposées à ces trois questions sont
`SPEC-BOOKING-03` (capacité, concurrence, immobilisation),
`SPEC-BOOKING-07` (paiement), `SPEC-BOOKING-09` (bon cadeau),
`SPEC-CANCEL-06` (alerte météo) et `SPEC-CANCEL-04` (remboursement). Elles
sont couvertes en premier.

## 2. Niveaux de test

| Niveau | Ce qu'on y met | Ce qu'on remplace |
|---|---|---|
| Domaine | les règles métier pures : calcul du montant, saison, fermeture des réservations, éligibilité d'un code, seuil de maintien | rien, ce niveau ne touche ni base ni réseau |
| Application | les cas d'usage complets, avec la base : capacité, concurrence sur la dernière place, immobilisation, changements d'état d'une sortie | le prestataire de paiement et les envois de messages, remplacés par des doublures |
| Bout en bout | trois parcours seulement : réserver et payer, mettre en alerte puis annuler, acheter puis utiliser un bon cadeau | rien n'est remplacé, sauf le prestataire de paiement en mode test |

La majorité des cas vit au niveau **domaine**, parce que la majorité de nos
règles y vivent aussi, et que `architecture.md` §2 impose un domaine sans
framework ni base de données, donc directement testable.

## 3. Le point dur de ce projet : le temps

Presque toutes nos règles sont horaires. Fermeture des réservations à midi la
veille ou le jour même, contrôle du seuil à 24 heures du départ, message de
rappel à un horaire réglable, alerte la veille à 18h, confirmation 2 heures
avant, immobilisation des places pendant 15 minutes.

**Aucune de ces règles n'est testable si le code lit l'heure système.** La
stratégie impose donc que le domaine reçoive l'instant courant au lieu d'aller
le chercher. Un cas de test fixe l'instant, avance d'une minute, et observe.
C'est la seule contrainte que cette stratégie fait peser sur la conception du
code, et elle est assumée comme telle.

Le moyen a été tranché en `ADR-005` : une **horloge injectée** pour les
traitements déclenchés sans utilisateur, un **instant passé en paramètre**
pour les calculs purs.

Le fuseau de référence est celui de l'exploitation, conformément à
`SPEC-BOOKING-04`. Tous les cas l'expriment en heure locale.

## 4. Ce que nous ne testons pas, et pourquoi

- **Le prestataire de paiement.** Nous testons nos réactions à ses réponses,
  acceptée, refusée, perdue, pas son fonctionnement. Il est doublé partout
  sauf dans les trois parcours de bout en bout.
- **L'envoi réel d'un SMS ou d'un e-mail.** Nous vérifions qu'un envoi est
  demandé, sur les deux canaux, avec le bon type et le bon destinataire, et
  qu'il laisse une trace. La délivrance ne dépend pas de nous, et le client a
  explicitement placé la non-délivrance hors de notre responsabilité.
- **Le rendu graphique.** `SPEC-BOOKING-08` est vérifiée à la main sur trois
  appareils, une fois, avant la présentation. Automatiser un rendu visuel
  coûterait plus que le risque couvert.
- **La charge.** `SPEC-NFR-01` fixe 30 parcours simultanés et 2 secondes de
  réponse. Ce sont des hypothèses d'équipe, pas un engagement client : une
  mesure ponctuelle suffira, sans test automatisé permanent.
- **Les spécifications au statut brouillon.** `SPEC-NFR-05` et `SPEC-NFR-06`
  n'ont aucun critère technique à vérifier, seulement des questions à poser
  au client. Elles resteront sans cas de test, et c'est déclaré comme tel
  dans `docs/traceability-trous.md`.

## 5. Du critère d'acceptation au cas de test

Chaque spécification porte des critères numérotés `AC-n`. La règle est
simple :

- **tout `AC` doit être couvert par au moins un `CASE`** ;
- un `CASE` peut couvrir plusieurs `AC` s'ils décrivent le même comportement
  observé sous deux angles ;
- un `CASE` cite la spécification **et** les `AC` qu'il couvre, ce qui rend la
  chaîne vérifiable dans les deux sens ;
- les cas limites du tableau d'une spécification sont la première source de
  cas de test : ils ont été écrits pour ça.

Un `AC` qu'aucun cas ne couvre est une rupture, au même titre qu'une
spécification sans cas.

## 6. Nommage et traçabilité

```text
CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit
```

- Un cas par fichier, `tests/cases/CASE-<DOM>-nn.md`, numérotation continue
  par domaine, jamais réattribuée.
- Le nom du test automatisé **contient l'identifiant du cas**, séparateurs
  adaptés au langage : `test_CASE_BOOKING_17_...`.
- Le message de commit porte l'identifiant, `SPEC` ou `CASE`, conformément au
  README §5.

`tools/traceability.sh --check` vérifie les six ruptures avant chaque commit
de fin de journée.

## 7. Jeu de données de référence

Le même dans tous les cas, pour qu'un chiffre inattendu saute aux yeux.

| Donnée | Valeur |
|---|---|
| Bateaux | Ti Kap, 12 places, forfait 600 € ; Le Grand Bleu, 24 places, forfait 1 100 € |
| Tarifs | baleines 65 € et 40 € ; dauphins 50 € et 30 € |
| Créneaux | 7h, 10h et 14h |
| Date pivot en saison | 20 juillet, sortie baleines possible |
| Date pivot hors saison | 1er décembre, dauphins seulement |
| Jours de fermeture | 25 décembre et 1er janvier |

## 8. Ce qui reste à faire, et dans quel ordre

1. `SPEC-BOOKING-03` et `SPEC-CANCEL-06`, les deux plus exposées, à J6.
2. `SPEC-BOOKING-07`, `SPEC-BOOKING-09`, `SPEC-CANCEL-04`.
3. Le reste des spécifications `Must`.
4. Les spécifications `Should`, puis les `NFR` vérifiables.

Le suivi se lit dans la matrice, colonne `Cas de test`. Ce qui n'est pas
encore couvert est déclaré dans `docs/traceability-trous.md`, pas laissé au
hasard d'une relecture.

## 9. Organisation de `tests/`

Cette section est la dernière entrée du plan de délégation de J7 : sans elle,
l'agent ne sait pas où écrire, ni sous quelle forme.

```text
tests/
├── cases/            les cas de test, en markdown, tenus par l'équipe
│   ├── TEMPLATE.md
│   └── CASE-<DOM>-nn.md
├── Domaine/          PHPUnit, une classe par spécification
├── Application/      PHPUnit, une classe par spécification, avec la base
├── BoutEnBout/       Behat, un fichier .feature par parcours
└── Doublures/        horloge figée, paiement, envois
```

**Un outil par niveau**, conformément à `ADR-001` :

| Niveau | Outil | Ce qui s'y écrit |
|---|---|---|
| Domaine | PHPUnit | les règles pures, sans base ni réseau |
| Application | PHPUnit | les cas d'usage avec la base, doublures branchées |
| Bout en bout | Behat | les trois parcours, le scénario Gherkin du cas repris tel quel |

**Une classe de test par spécification, une méthode par cas.**
`SPEC-BOOKING-03` donne une classe qui contient huit méthodes, une par cas
de `CASE-BOOKING-01` à `08`. La matrice se remplit alors toute seule, et un
cas orphelin se voit immédiatement.

Le nom de la méthode **contient l'identifiant du cas**, avec des tirets bas :

```text
test_CASE_BOOKING_03_derniere_place_second_client_refuse_avant_paiement
```

Pour les scénarios Behat, l'identifiant figure dans le nom du fichier et dans
le titre du scénario, ce qui suffit à `tools/traceability.sh`.

**Trois doublures, et seulement trois :**

- **l'horloge**, figée par le cas de test, conformément à `ADR-005` ;
- **le prestataire de paiement**, dont on simule les quatre réponses,
  acceptée, refusée, abandonnée, perdue ;
- **les envois**, qui n'expédient rien mais enregistrent ce qui aurait été
  envoyé, avec son type, son canal et son destinataire, ce que
  `SPEC-CANCEL-04` AC-6 exige de toute façon en production.

Tout le reste est réel, base de données comprise : c'est elle qui porte deux
règles métier, l'unicité du naturaliste et le non-cumul des codes.

**Le jeu de données de référence du §7 est construit à un seul endroit**, et
chaque test part de lui. Un chiffre inattendu dans un test signale alors une
régression, pas un jeu de données différent.

**Exécution** : `php bin/phpunit` pour les deux premiers niveaux,
`vendor/bin/behat` pour le troisième. Les deux commandes doivent passer avant
tout commit de fin de journée, au même titre que
`tools/traceability.sh --check`.
