# Plan de délégation - `SPEC-ADMIN-01`

- **Spécification :** connexion à l'espace de gestion
- **Date :** J7 (2026-08-18), écrit **avant** la première tâche confiée à l'agent
- **Pilote :** l'équipe
- **Cas couverts :** `CASE-ADMIN-01`, `CASE-ADMIN-02`, `CASE-ADMIN-03`

C'est une prévision, pas un compte rendu.

**Dépendance.** Aucune, et tout le reste du domaine ADMIN en dépend : sans session, aucun autre écran de gestion n'est atteignable. À déléguer en premier.

---

## Ce que l'agent reçoit dans tous les cas

- `specs/admin.md`, section `SPEC-ADMIN-01` uniquement.
- Les fichiers de cas cités, en lecture.
- `docs/architecture.md` §2, §4 et §7.
- `docs/mcd-mld.md` §6 et §7.

## Ce qu'il ne touche jamais, quelle que soit la tâche

`tests/cases/**`, `specs/**`, `docs/**`, `tools/traceability.sh`, et le parcours client, qui ne demande aucun compte, et les autres écrans de gestion.

---

## Avant - le découpage

| # | Tâche | Test qui doit passer au vert | Ce que l'agent reçoit | Ce qu'il ne touche pas |
|---|---|---|---|---|
| 1 | Ouvrir une session pour le compte unique du gérant à partir d'un e-mail et d'un mot de passe corrects, et donner accès aux sections de gestion | `CASE-ADMIN-01` | le socle commun ci-dessus | les écrans de gestion eux-mêmes, le parcours client |
| 2 | Refuser un e-mail inconnu et un mot de passe erroné **avec le même message**, et refuser toute page de gestion demandée sans session ouverte | `CASE-ADMIN-02` | le socle, plus la tâche 1 | l'ouverture de session écrite en 1 |
| 3 | Refuser à la définition tout mot de passe qui ne respecte pas les quatre conditions : 8 caractères, une majuscule, une minuscule, un chiffre, un caractère spécial | `CASE-ADMIN-03` | le socle | l'authentification, qui est un autre sujet que la robustesse du secret |

**Découpage retenu :** une ouverture, deux refus. La tâche 3 est séparée parce qu'elle porte sur la définition du mot de passe, pas sur la connexion, et se teste sans session.

---

## Après - ce qui s'est passé

**Rempli au rituel de 16h15 du J8.**

Le découpage prévoyait **une tâche par cas de test, confiée séparément**. Dans
les faits, le code a été produit **spécification par spécification** : un même
service applicatif satisfait plusieurs cas, et le scinder en autant de tâches
aurait produit du code jetable entre deux passages. Les tests attendus sont
passés au vert dans l'ordre prévu par les dépendances, mais **les tâches n'ont
pas été des unités de délégation distinctes**. C'est l'écart principal de la
journée, il vaut pour les vingt-six plans, et il tient au découpage que nous
avons écrit, pas à l'agent.

| # | Résultat | Ce qui a fait reprendre la main |
|---|---|---|
| 1 | `conforme` |  |
| 2 | `conforme` |  |
| 3 | `conforme` |  |

| Résultat | Sens |
|---|---|
| `conforme` | la tâche a produit ce qui était prévu, le test attendu est passé au vert |
| `repris` | le résultat a demandé une intervention manuelle avant d'être gardé |
| `redécoupé` | la tâche a dû être scindée ou reformulée, puis relancée |
| `abandonné` | la tâche a été retirée à l'agent et faite à la main |

## Ce que nous surveillons particulièrement ici

- **Un mot de passe stocké en clair ou avec un algorithme faible.** Le modèle impose une empreinte, et c'est irrattrapable une fois en production.
- **Un message d'erreur qui distingue e-mail inconnu et mot de passe erroné.** Cela permet d'énumérer les comptes, et le cas 2 l'interdit explicitement.
- Un écran de création de compte : il n'y en a qu'un, celui du gérant, et aucun formulaire ne doit permettre d'en créer un second.
- Une page de gestion accessible en tapant son adresse directement, sans session.
