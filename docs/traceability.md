<!-- Généré par tools/traceability.sh — ne pas éditer à la main.
     Les sections « Exigences non couvertes » et « Trous connus » sont
     alimentées par docs/traceability-trous.md, lui tenu à la main. -->

# Matrice de traçabilité — équipe `Le Trio`

Reprise au créneau 16h15, avec le journal. C'est le seul endroit où l'état de la
chaîne se lit d'un coup d'œil.

```text
CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit
```

Une ligne par spécification. **Ce document ne se reconstitue pas la veille du
rendu** : `git log -- docs/traceability.md` montre les jours où il a été tenu.

---

## Comment la lire

| Colonne | Ce qu'on y met | Où le trouver |
|---|---|---|
| SPEC | l'identifiant de la spécification | titre de section dans `specs/<domaine>.md` |
| REQ | la ou les exigences qu'elle réalise | `docs/cahier-des-charges.md` |
| Source | l'échange dont l'exigence est issue, ou `déduit` | `docs/compte-rendu-entretien-nn.md` |
| Cas de test | le ou les cas qui la couvrent | `tests/cases/CASE-*.md` |
| Tests | le nom du test automatisé | `tests/` |
| Commits | le ou les sha courts | `git log --grep=<SPEC-ID>` |

Un maillon qui n'existe pas encore se note `—`. Plusieurs valeurs dans une case se
séparent par une virgule.

**Les six ruptures surveillées** par `tools/traceability.sh --check` : une exigence
sans source · une source citée qui n'existe pas dans nos comptes rendus · une
spécification qu'aucun cas de test ne couvre · un cas de test sans test automatisé ·
une exigence que plus aucune spécification ne reprend · un cas de test utilisé dans
`tests/` mais défini nulle part.

---

## Matrice

| SPEC | REQ | Source | Cas de test | Tests | Commits |
|---|---|---|---|---|---|
| `SPEC-ADMIN-01` | `REQ-031`, `REQ-032`, `REQ-034`, `REQ-104` | `CR-02/Q03`, `CR-02/Q10`, `déduit` | `CASE-ADMIN-01`, `CASE-ADMIN-02`, `CASE-ADMIN-03` | — | `3877256` |
| `SPEC-ADMIN-02` | `REQ-016`, `REQ-028` | `CR-01/Q07`, `CR-02/Q03` | `CASE-ADMIN-04`, `CASE-ADMIN-05`, `CASE-BOOKING-32` | — | `ba29c7a`, `ab844db` |
| `SPEC-ADMIN-03` | `REQ-029` | `CR-02/Q03` | `CASE-ADMIN-06`, `CASE-ADMIN-07` | — | `277679f` |
| `SPEC-ADMIN-04` | `REQ-038`, `REQ-039` | `CR-03/Q01` | `CASE-ADMIN-08`, `CASE-ADMIN-09` | — | `036f3f4`, `6195bfd` |
| `SPEC-ADMIN-05` | `REQ-041` | `CR-03/Q06` | `CASE-ADMIN-10`, `CASE-ADMIN-11`, `CASE-ADMIN-12` | — | `1ef9ddd` |
| `SPEC-ADMIN-06` | `REQ-019`, `REQ-050`, `REQ-056` | `CR-01/Q13`, `CR-03/Q05`, `CR-05/Q10` | `CASE-ADMIN-13`, `CASE-ADMIN-14`, `CASE-ADMIN-15` | — | `548ad16`, `51dba2a` |
| `SPEC-BOOKING-01` | `REQ-001`, `REQ-008`, `REQ-009`, `REQ-015`, `REQ-036` | `CR-01/Q02`, `CR-02/Q02`, `CR-02/Q12`, `CR-02/Q18` | `CASE-BOOKING-20`, `CASE-BOOKING-21`, `CASE-BOOKING-22`, `CASE-BOOKING-23` | — | `27aa920`, `89943cf`, `db1251e` |
| `SPEC-BOOKING-02` | `REQ-010`, `REQ-011`, `REQ-038` | `CR-01/Q05`, `CR-03/Q01` | `CASE-BOOKING-24`, `CASE-BOOKING-25`, `CASE-BOOKING-26` | — | `6ef0ea9` |
| `SPEC-BOOKING-03` | `REQ-002`, `REQ-003`, `REQ-004`, `REQ-007`, `REQ-033`, `REQ-059` | `CR-01/Q01`, `CR-01/Q02`, `CR-01/Q08`, `CR-02/Q10`, `CR-02/Q16`, `déduit` | `CASE-BOOKING-01`, `CASE-BOOKING-02`, `CASE-BOOKING-03`, `CASE-BOOKING-04`, `CASE-BOOKING-05`, `CASE-BOOKING-06`, `CASE-BOOKING-07`, `CASE-BOOKING-08` | — | `bee544a` |
| `SPEC-BOOKING-04` | `REQ-005` | `CR-01/Q09` | `CASE-BOOKING-27`, `CASE-BOOKING-28` | — | `67e6509` |
| `SPEC-BOOKING-05` | `REQ-006`, `REQ-014` | `CR-01/Q03`, `CR-01/Q07` | `CASE-BOOKING-29`, `CASE-BOOKING-30` | — | `f376bb8` |
| `SPEC-BOOKING-06` | `REQ-012`, `REQ-014`, `REQ-015` | `CR-01/Q04`, `CR-01/Q07`, `CR-02/Q02` | `CASE-BOOKING-31`, `CASE-BOOKING-32` | — | `d297463` |
| `SPEC-BOOKING-07` | `REQ-017`, `REQ-018` | `CR-01/Q10`, `CR-01/Q11` | `CASE-BOOKING-09`, `CASE-BOOKING-10`, `CASE-BOOKING-11`, `CASE-BOOKING-12`, `CASE-BOOKING-13` | — | `0d28879` |
| `SPEC-BOOKING-08` | `REQ-035`, `REQ-101` | `déduit` | `CASE-BOOKING-37` | — | `8091450` |
| `SPEC-BOOKING-09` | `REQ-043`, `REQ-044`, `REQ-045`, `REQ-046`, `REQ-047`, `REQ-048`, `REQ-049` | `CR-03/Q07`, `CR-04/Q01` | `CASE-BOOKING-14`, `CASE-BOOKING-15`, `CASE-BOOKING-16`, `CASE-BOOKING-17`, `CASE-BOOKING-18`, `CASE-BOOKING-19` | — | `33656ed`, `deaf28b`, `392a2ab` |
| `SPEC-BOOKING-10` | `REQ-050`, `REQ-051` | `CR-03/Q05`, `CR-04/Q04` | `CASE-BOOKING-19`, `CASE-BOOKING-33`, `CASE-BOOKING-34` | — | `dfee14e`, `deaf28b`, `721ed6e` |
| `SPEC-BOOKING-11` | `REQ-040`, `REQ-102` | `CR-03/Q02` | `CASE-BOOKING-35`, `CASE-BOOKING-36` | — | `5b321ff` |
| `SPEC-CANCEL-01` | `REQ-022` | `CR-02/Q05` | `CASE-CANCEL-14`, `CASE-CANCEL-15` | — | `bc37c97`, `4ae1077` |
| `SPEC-CANCEL-02` | `REQ-021` | `CR-02/Q04` | `CASE-CANCEL-16`, `CASE-CANCEL-17`, `CASE-CANCEL-18` | — | `8e58d34` |
| `SPEC-CANCEL-03` | `REQ-004` | `CR-01/Q08` | `CASE-CANCEL-19`, `CASE-CANCEL-20` | — | `41f7eba` |
| `SPEC-CANCEL-04` | `REQ-023`, `REQ-026`, `REQ-058` | `CR-05/Q03`, `CR-05/Q11`, `CR-05/Q12` | `CASE-CANCEL-10`, `CASE-CANCEL-11`, `CASE-CANCEL-12`, `CASE-CANCEL-13` | — | `98a7f23` |
| `SPEC-CANCEL-05` | `REQ-025`, `REQ-042`, `REQ-057` | `CR-02/Q08`, `CR-03/Q03`, `CR-05/Q02` | `CASE-CANCEL-21`, `CASE-CANCEL-22`, `CASE-CANCEL-23`, `CASE-CANCEL-24` | — | `c7abfe7`, `ab148cc` |
| `SPEC-CANCEL-06` | `REQ-052`, `REQ-053`, `REQ-054`, `REQ-055`, `REQ-060` | `CR-05/Q01`, `CR-05/Q06`, `CR-05/Q08`, `CR-05/Q16` | `CASE-CANCEL-01`, `CASE-CANCEL-02`, `CASE-CANCEL-03`, `CASE-CANCEL-04`, `CASE-CANCEL-05`, `CASE-CANCEL-06`, `CASE-CANCEL-07`, `CASE-CANCEL-08`, `CASE-CANCEL-09` | — | `cdb26fa` |
| `SPEC-NFR-01` | `REQ-100` | `déduit` | `CASE-NFR-05` | — | `347a598` |
| `SPEC-NFR-02` | `REQ-040`, `REQ-102` | `CR-03/Q02` | `CASE-NFR-01`, `CASE-NFR-02` | — | `15d9de4`, `0944f07` |
| `SPEC-NFR-03` | `REQ-103` | `déduit` | `CASE-NFR-06` | — | `347a598`, `ffc7ad3` |
| `SPEC-NFR-04` | `REQ-105` | `déduit` | `CASE-NFR-03`, `CASE-NFR-04` | — | `72861fe`, `ffc7ad3` |
| `SPEC-NFR-05` | `REQ-106` | `déduit` | — | — | — |
| `SPEC-NFR-06` | `REQ-107` | `déduit` | — | — | — |

---

## Exigences non couvertes

Une exigence qu'aucune spécification ne reprend n'apparaît nulle part dans le
tableau ci-dessus. C'est la rupture la plus facile à ne pas voir, et elle se
crée toute seule quand le client change d'avis.

| REQ | Priorité | Pourquoi elle n'est pas encore spécifiée |
|---|---|---|
| — | — | Aucune : toutes les exigences du cahier des charges sont reprises par au moins une spécification. |

---

## Trous connus

Ce que nous savons incomplet, et ce que nous comptons en faire. **Un trou déclaré
n'est pas une faute. Un trou qu'on découvre à notre place en est une.**

| Quoi | Depuis | Pourquoi | Ce qu'on en fait |
|---|---|---|---|
| `SPEC-NFR-05` et `SPEC-NFR-06` sont sans cas de test | J3 | statut brouillon, aucun critère technique : leurs `AC` sont des actions de projet, poser une question au client et consigner sa réponse, pas des comportements logiciels | aucun cas ne sera écrit ; la vérification est de reposer les deux questions au prochain entretien |
| 3 cas de test sont `manuel assumé` | J6 | rendu multi-support, charge et coût documenté ne se testent pas en continu, motifs au §4 de `docs/strategie-de-test.md` | `CASE-BOOKING-37` avant J10, `CASE-NFR-05` avant la mise en production, `CASE-NFR-06` à la revue croisée de J9 |
| Les 79 cas automatisables n'ont aucun test | J6 | les cas viennent d'être écrits ; l'automatisation est une tâche de production de l'agent, qui ne peut pas être lancée avant le plan de délégation (README §6bis) | à partir de J7, dans l'ordre de `docs/strategie-de-test.md` §8 |
| Texte des trois messages automatiques, en français et en anglais | J3 | jamais fourni par le client, ni pour le rappel, ni pour l'alerte, ni pour la confirmation d'annulation (`CR-05/Q15`) | reposé au prochain entretien ; sans lui, `SPEC-CANCEL-05` et `SPEC-CANCEL-06` ne sont testables que sur leur déclenchement, pas sur leur contenu |
| Mode d'envoi des SMS | J5 | `CR-05/Q21` répond sur le forfait conservé, pas sur la passerelle d'envoi. La lecture retenue est la seule compatible avec un envoi automatique | question 1 du §8 de `CR-05`, prioritaire : c'est le seul point qui puisse encore faire tomber l'automatisation demandée |
| Fusion de `BonCadeau` et `Avoir` | J4 | les deux dispositifs ne diffèrent plus que par leur origine depuis la v4 (question 8 du §11) | deux tables maintenues tant que le client n'a pas répondu, choix réversible documenté dans `mcd-mld.md` §5 |
| Nom exact de la plateforme d'envoi | J6 | `ADR-004` retient une plateforme française multicanal et pressent Brevo, mais trois vérifications ne peuvent pas se faire depuis le dépôt : couverture du plan de numérotation du territoire, expéditeur alphanumérique, contrat de sous-traitance RGPD | à confirmer à l'ouverture du compte ; si l'une des trois manque, l'option C de l'ADR reprend la main |
| Message associé à une annulation faute de 6 inscrits | J5 | cas non abordé par le client (`CR-05/Q14`), alors que c'est la seule annulation automatique de l'outil | question 13 du §11, à reposer ; en attendant, aucun message spécifique n'est spécifié |
