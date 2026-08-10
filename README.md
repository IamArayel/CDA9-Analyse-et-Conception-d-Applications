# Projet Ti Baleine — équipe `Le Trio`

Repository de mission. Il contient l'analyse, la conception, les tests et le code.
Il est aussi le support du dossier de projet.

> Ce dépôt raconte l'histoire de la conception du logiciel. Un lecteur doit
> pouvoir partir de n'importe quelle ligne de code et remonter jusqu'au besoin du
> client, et inversement.

---

## 0. Accès au document Notion
https://app.notion.com/p/J1-Rencontre-avec-le-client-3b8ca55183dd80b28dedc7e4feaf3035

## 1. Les trois règles

Elles ne se négocient pas.

1. **Aucun code généré sans spécification.**
2. **Aucune fonctionnalité acceptée sans preuve.**
3. **Aucun code livré sans compréhension humaine.**

La troisième se vérifie à J10 : une spécification est tirée au sort et vous devez
l'expliquer jusqu'au code. *Vous pouvez livrer du code généré par une IA. Vous ne
pouvez pas livrer du code que vous ne savez pas expliquer.*

---

## 2. Qui fait quoi

| Étape | Responsable | Rôle de l'IA |
|---|---|---|
| Comprendre le besoin | **Vous** | aucun |
| Cahier des charges | **Vous** | aucun |
| Spécifier | **Vous** | revue critique |
| Concevoir (UML, BDD, archi) | **Vous** | revue critique |
| Définir les cas de test | **Vous** | revue critique |
| Automatiser les tests | IA | production |
| Reviewer les tests générés | **Vous** | aucun |
| Découper en tâches, piloter | **Vous** | aucun |
| Produire le code | IA | production |
| Reviewer le code | **Vous** | second regard possible |
| Corriger | IA | production |
| Accepter | **Vous** | aucun |

Deux boucles, dans cet ordre : **avant** le développement, l'IA critique votre
travail ; **après** la génération, vous critiquez le travail de l'IA.

---

## 3. Arborescence

```text
.
├── README.md
├── docs/
│   ├── cahier-des-charges.md          À CRÉER — exigences REQ-xxx
│   ├── compte-rendu-entretien-01.md   et -02, -03…
│   ├── architecture.md
│   ├── impact-CR-001.md               à remplir uniquement quand le client change d'avis
│   ├── delegation-GABARIT.md          gabarit — à copier, pas à remplir
│   ├── delegation-<SPEC>.md           À CRÉER — J7, avant de lancer l'agent
│   ├── journal.md                     une entrée par jour, obligatoire
│   ├── traceability.md                GÉNÉRÉ — ne pas éditer
│   ├── uml/
│   │   ├── use-cases.puml
│   │   ├── domain.puml
│   │   └── sequences/
│   └── adr/
│       ├── ADR-000-template.md
│       └── ADR-001-stack.md
├── specs/
│   └── <domaine>.md                   contient les SPEC-<DOM>-nn
├── tests/
│   ├── cases/
│   │   └── CASE-<DOM>-nn.md           un fichier par cas
│   └── …                              tests automatisés
├── src/
└── tools/
    └── traceability.sh
```

Ce qui n'est **pas** dans ce dépôt n'existe pas. Une décision prise oralement et
non écrite est une décision perdue.

Les fichiers marqués **À CRÉER** n'ont pas de gabarit fourni : c'est à vous de
décider ce qu'ils contiennent, puis de le défendre. Le nom du fichier et son
emplacement, eux, sont imposés — sans quoi rien ne s'échange entre équipes.

---

## 4. Identifiants et traçabilité

La chaîne complète :

```text
CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit
```

Elle commence à un **échange consigné** avec le client, pas à une exigence. Une
règle métier qui ne vient d'aucun échange consigné ne vient de nulle part.

| Niveau | Format | Où il est défini | Ce qu'il doit citer |
|---|---|---|---|
| Échange | `CR-01/Q07` | `docs/compte-rendu-entretien-01.md`, questions numérotées `Qnn` | — |
| Exigence | `REQ-012` | `docs/cahier-des-charges.md` | un `CR-nn/Qnn`, ou `déduit` + justification |
| Spécification | `SPEC-BOOKING-04` | `specs/<domaine>.md`, en titre de section | au moins un `REQ` |
| Cas de test | `CASE-BOOKING-17` | `tests/cases/CASE-BOOKING-17.md` | au moins un `SPEC` |
| Test automatisé | l'ID `CASE` **dans le nom du test** | `tests/` | — |
| Commit | l'ID `SPEC` dans le message | git | — |

Le domaine `<DOM>` est en majuscules, court, stable : `BOOKING`, `CANCEL`,
`PAYMENT`, `ADMIN`. Les numéros sont sur deux chiffres, les `REQ` sur trois.

### Citer sa source

Vous pouvez parler avec les autres équipes — c'est même normal. Mais **chaque
client est différent** : une réponse obtenue par une autre équipe est une réponse
au client d'une autre équipe. C'est une hypothèse, pas un fait.

Une information venue d'ailleurs se confirme auprès de votre client, au passage
suivant, puis se cite comme n'importe quelle autre. C'est trente secondes de
question, et c'est ce qui sépare une règle métier d'une rumeur.

`déduit` reste possible pour ce qui n'a pas été discuté et que vous inférez — avec
une justification. Un cahier des charges majoritairement déduit n'est pas une
faute mécanique, mais il se lit comme un aveu.

Exemple de nom de test :

```text
test_CASE_BOOKING_17_annulation_moins_48h_retient_50_pourcent
```

### Vérifier la chaîne

```bash
./tools/traceability.sh            # régénère docs/traceability.md
./tools/traceability.sh --check    # même chose, mais sort en erreur si rupture
```

Une rupture est un maillon vide : une exigence sans source, une source citée qui
n'existe pas dans vos comptes rendus, une spec sans cas de test, un cas sans test
automatisé, un `REQ` que plus aucune spec ne couvre, un cas de test orphelin.

`--check` est ce que le formateur exécute en review. Faites-le tourner
vous-mêmes **avant** chaque commit de fin de journée.

---

## 5. Commits

```text
<type>(<domaine>): <SPEC-ID> <description courte>

<pourquoi, si ce n'est pas évident>

Generated-by: <outil>          ← uniquement si le contenu a été produit par l'agent
```

Types : `spec`, `design`, `test`, `feat`, `fix`, `docs`, `chore`.

Exemples :

```text
spec(cancel): SPEC-CANCEL-03 annulation client à moins de 48h
test(cancel): CASE-CANCEL-11 retenue de 50 % sur le montant total
feat(cancel): SPEC-CANCEL-03 règle de retenue dans CancellationPolicy

Generated-by: <outil>
```

Activer le gabarit de message une fois pour toutes :

```bash
git config commit.template .gitmessage
```

Le trailer `Generated-by:` est ce qui permet de répondre à J10 à
« quelle partie du code a été générée ? ». Sans lui, la question est sans
réponse et la démonstration se fait contre vous.

---

## 6. Journal

`docs/journal.md`, une entrée par jour, remplie au créneau 16h15. C'est le seul
endroit où se trouve la trace de ce que vous avez **refusé** à l'IA, et de ce que
vos acceptations ont changé — et ce sont deux des trois questions obligatoires de la
présentation de J10.

Quatre rubriques, aucune ne peut rester vide sans justification :

- décisions prises ;
- critiques de l'IA acceptées, **et ce qu'elles ont changé** ;
- critiques de l'IA **refusées, et pourquoi** ;
- erreurs produites par l'IA et détectées.

Refuser demande un motif ; accepter demande un changement. Sans quoi accepter ne
coûte rien, et un journal parfaitement propre peut vouloir dire qu'on a tout pris.

---

## 6bis. Plan de délégation

`docs/delegation-<SPEC>.md`, écrit à J7 **avant** de confier la première tâche à
l'agent sur la spécification désignée. Gabarit : `docs/delegation-GABARIT.md`.

Un tableau avant — les tâches, le test qui doit passer au vert, ce que l'agent
reçoit, ce qu'il ne touche pas. Un tableau après, rempli le soir même — ce qui s'est
réellement passé, tâche par tâche.

Ce n'est pas le nombre de tâches conformes qui compte, c'est **l'écart entre la
prévision et le résultat**. C'est la troisième question obligatoire de J10.

---

## 7. Rythme

Vous organisez votre travail. Ce qui est fixé :

- les **rendez-vous client**, annoncés à l'avance ;
- la **revue croisée** de J9 ;
- le **jalon de fin de semaine 1** (§8) ;
- les **présentations de J10** ;
- le **push et le journal de 16h15**, tous les jours.

Vous commitez souvent dans la journée pour ne rien perdre ; vous poussez le soir
pour ne pas dépendre d'un poste. Un dépôt sans push du jour est traité comme une
journée sans production.

Le client peut revenir en cours de mission. Vous ne saurez pas quand.

Vous rendez votre propre planning à J2 et vous le tenez. Comme rien n'est ramassé
en cours de route, l'historique poussé chaque soir est la seule preuve que le
travail a été fait au fil de l'eau.

---

## 8. Jalon de fin de semaine 1

Vendredi à partir de 15h00, le formateur passe sur chaque équipe — une douzaine de
minutes — pendant que les autres travaillent : cahier des charges, specs, UML,
MCD/MLD, architecture, ADR. Les verdicts tombent à 16h15.

**Une équipe dont les artefacts ne sont pas recevables ne passe pas en génération
de code le lundi.** Elle corrige d'abord. Ce n'est pas une sanction, c'est
l'application de la règle 1.

---

## 9. Ce qui n'est pas demandé

- Un design graphique abouti.
- Un périmètre large. Le client plafonne le Must have à **3 cas d'usage**.
- Des technologies que vous ne connaissez pas. La stack se choisit en `ADR-001`
  et doit être déjà pratiquée par au moins deux membres de l'équipe.
- Du prompt engineering. Ce qui est évalué, c'est votre capacité à obtenir un
  logiciel correct par la qualité de vos spécifications, de votre conception, de
  vos tests et de votre pilotage — pas votre collection de prompts.

---

## 10. Comment on vous note

**Une seule évaluation, à J10**, sur deux supports : le **rendu global** — dépôt
gelé à 11:30, cahier des charges, specs, UML, tests et code — et la
**présentation devant toute la promo**. Rien n'est ramassé en cours de route : les
comptes rendus, les versions successives du cahier des charges et des specs et le
journal se lisent dans l'historique du dépôt.

### Note de groupe

| Bloc | Poids |
|---|---|
| Conduite de la découverte + cahier des charges | 15 % |
| Specs, critères d'acceptation, traçabilité | 22 % |
| UML, MCD/MLD, architecture, ADR | 17 % |
| Tests : stratégie, cas, qualité | 16 % |
| Code : conformité et qualité du logiciel | 12 % |
| Analyse d'impact du changement de besoin | 8 % |
| Présentation | 10 % |

Chaque fonctionnalité montrée en démo est annoncée avec son identifiant de
spécification et le test qui la protège. Une fonctionnalité démontrée sans spec ni
test ne compte pas.

### Individualisation

À la fin de votre présentation, **chacun tire une spécification du projet au
hasard** et l'explique : ce qu'elle dit, de quel besoin client elle vient, quel
test la protège, où elle vit dans le code, et ce qui a été arbitré pour arriver
là. Cette explication ajuste votre note individuelle de **−4 à +1 point** autour
de la note de groupe.

L'échelle est asymétrique : expliquer une spécification de votre propre projet est
le niveau attendu, pas un bonus.

Une application impressionnante construite sur une mauvaise compréhension du
besoin n'obtient pas une bonne note.
