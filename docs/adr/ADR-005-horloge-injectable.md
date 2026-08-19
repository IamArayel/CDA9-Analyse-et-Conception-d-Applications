# ADR-005 - Accès au temps dans le domaine

**Statut :** accepté
**Date :** J6 (2026-08-17)
**Décidé par :** l'équipe (Chloe Baisse, Arnaud Maxime, Anthony Dégeilh)
**Complète :** `docs/strategie-de-test.md` §3, qui posait la contrainte sans
trancher le moyen

---

## Contexte

La quasi-totalité des règles de ce projet sont des règles d'heure :

| Règle | Spécification |
|---|---|
| Fermeture des réservations à midi, la veille ou le jour même | `SPEC-BOOKING-04` |
| Contrôle du seuil de 6 inscrits 24 heures avant le départ | `SPEC-BOOKING-03` |
| Immobilisation des places pendant 15 minutes | `SPEC-BOOKING-03` |
| Message de rappel à un horaire réglable | `SPEC-CANCEL-05` |
| Alerte la veille à 18h, confirmation 2 heures avant le départ | `SPEC-CANCEL-06` |
| Expiration d'un bon cadeau et d'un avoir à un an | `SPEC-BOOKING-09`, `SPEC-BOOKING-10` |
| Saison des sorties baleines, du 15 juin au 31 octobre | `SPEC-BOOKING-02` |
| Purge des données personnelles à trois mois | `SPEC-NFR-04` |

**Si le code lit l'heure système, aucune de ces règles n'est testable.**
Vérifier qu'un créneau ferme à 12h00 imposerait d'attendre midi ; vérifier
qu'un bon cadeau expire au bout d'un an imposerait d'attendre un an.
`CASE-BOOKING-18` teste exactement ce dernier cas, sur deux instants séparés
d'une heure et un an après l'achat.

Le fuseau de référence est celui de l'exploitation, conformément à
`SPEC-BOOKING-04`.

## Options envisagées

### Option A - L'instant passé en paramètre (retenue pour les calculs purs)

| | |
|---|---|
| Ce qu'elle apporte | la dépendance au temps est visible dans la signature ; une fonction qui reçoit une date se teste sans aucune machinerie |
| Ce qu'elle coûte | l'instant remonte de proche en proche jusqu'à l'appelant, ce qui alourdit les chaînes d'appel profondes |
| Ce qu'elle rend difficile plus tard | rien |

### Option B - Une horloge injectée (retenue pour les traitements déclenchés seuls)

| | |
|---|---|
| Ce qu'elle apporte | une interface d'horloge, implémentée par une horloge système en production et par une horloge figée en test. L'instant devient un service comme un autre, et un test avance le temps sans attendre |
| Ce qu'elle coûte | une abstraction supplémentaire, et un service à injecter là où le temps compte |
| Ce qu'elle rend difficile plus tard | rien |

### Option C - Figer le temps globalement pendant les tests (écartée)

| | |
|---|---|
| Ce qu'elle apporte | aucune modification du code de production ; le temps est détourné le temps du test |
| Ce qu'elle coûte | le code continue de prétendre qu'il ne dépend pas de l'heure, alors que c'est faux. L'astuce dépend de l'outil de test et ne couvre pas les trois parcours de bout en bout |
| Ce qu'elle rend difficile plus tard | tout ce qui relève de la conception : la contrainte disparaît, donc le code n'est jamais forcé de traiter le temps comme une donnée |

## Décision

**Le domaine ne lit jamais l'heure système.**

- **Option B** pour tout ce qui se déclenche sans utilisateur devant l'écran :
  envoi des trois messages automatiques, contrôle du seuil de 6 inscrits,
  expiration des immobilisations. Ces traitements reçoivent une horloge.
- **Option A** pour les calculs purs : saison, heure de fermeture, validité
  d'un code, purge des données. Ces fonctions reçoivent un instant en
  paramètre.

En production, l'horloge rend l'heure réelle du fuseau d'exploitation. En
test, elle rend l'instant que le cas de test a fixé.

## Raisons

Les deux options ne s'opposent pas, elles s'appliquent à deux situations
différentes. Une règle qui répond à la question « cette date est-elle en
saison » n'a pas besoin d'un service : lui passer la date est plus simple et
plus honnête. Un traitement planifié, lui, ne reçoit sa date de personne : il
lui faut une source, et cette source doit être remplaçable.

L'option C est écartée sur un point de fond. Elle procure la testabilité sans
la contrainte de conception, or c'est la contrainte qui a de la valeur : elle
oblige à écrire un code où le temps est une donnée explicite. Elle ne
fonctionne pas non plus sous l'outil retenu pour les parcours de bout en
bout.

## Conséquences acceptées

- **Une règle de revue supplémentaire**, à porter dans `architecture.md` §2 :
  tout appel direct à l'heure système dans le domaine est un défaut, au même
  titre qu'une requête SQL dans un contrôleur.
- **Le plan de délégation doit l'énoncer**, sans quoi l'agent produira du
  code qui lit l'heure système et les 82 cas de test deviendront
  inexécutables.
- **Une interface de plus** dans le domaine, et deux implémentations dans
  l'infrastructure.
- **Aucune version minimale de framework n'en découle.** Le point était laissé
  ouvert à J6, à vérifier avant la première tâche confiée à l'agent. Vérifié à
  J8 : les 76 tests écrits à J7 s'appuient sur `App\Domaine\Horloge`, une
  interface écrite à la main, et non sur le composant d'horloge du framework.
  L'idée ne dépend d'aucune bibliothèque, et la contrainte de version tombe.

## Ce qui nous ferait revenir dessus

- Un cas de test qui, malgré cette décision, exigerait d'attendre : ce serait
  le signe qu'un appel direct à l'heure système subsiste quelque part.
- L'apparition d'une règle dépendant d'un fuseau différent de celui de
  l'exploitation, ce qui obligerait à traiter le fuseau comme une donnée et
  non comme une constante.
