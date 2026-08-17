# Matrice de traçabilité — équipe `<NOM>`

Reprise au créneau 16h15, avec le journal. C'est le seul endroit où l'état de la
chaîne se lit d'un coup d'œil.

```text
CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit
```

Une ligne par spécification. La remplir n'est pas une formalité de fin de
journée : c'est parcourir votre propre chaîne et voir où elle s'arrête. Personne
ne vous signalera un maillon manquant à votre place.

**Ce document ne se reconstitue pas la veille du rendu.** `git log` sur ce fichier
montre les jours où il a été tenu.

---

## Comment la remplir

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

**Les six ruptures à surveiller :** une exigence sans source · une source citée
qui n'existe pas dans vos comptes rendus · une spécification qu'aucun cas de test
ne couvre · un cas de test sans test automatisé · une exigence que plus aucune
spécification ne reprend · un cas de test utilisé dans `tests/` mais défini nulle
part.

---

## Matrice

| SPEC | REQ | Source | Cas de test | Tests | Commits |
|---|---|---|---|---|---|
| `SPEC-CANCEL-03` | `REQ-014` | `CR-02/Q11` | `CASE-CANCEL-11` | `test_CASE_CANCEL_11_annulation_moins_48h_retient_50_pourcent` | `a3f1c2e` |
| `SPEC-CANCEL-04` | `REQ-015` | `déduit` | — | — | — |
| | | | | | |
| | | | | | |
| | | | | | |

> Les deux premières lignes sont des exemples de forme. Remplacez-les.

---

## Exigences non couvertes

Une exigence qu'aucune spécification ne reprend n'apparaît nulle part dans le
tableau ci-dessus. C'est la rupture la plus facile à ne pas voir, et elle se
crée toute seule quand le client change d'avis.

| REQ | Priorité | Pourquoi elle n'est pas encore spécifiée |
|---|---|---|
| | | |

---

## Trous connus

Ce que vous savez incomplet, et ce que vous comptez en faire. **Un trou déclaré
n'est pas une faute. Un trou qu'on découvre à votre place en est une.**

Une matrice sans aucun trou en cours de projet est plus suspecte qu'une matrice
qui en annonce trois.

| Quoi | Depuis | Pourquoi | Ce qu'on en fait |
|---|---|---|---|
| `SPEC-PAYMENT-02` sans cas de test | J6 | régime de remboursement pas tranché avec le client | question posée au prochain passage |
| | | | |

> Ligne d'exemple. Remplacez-la.
