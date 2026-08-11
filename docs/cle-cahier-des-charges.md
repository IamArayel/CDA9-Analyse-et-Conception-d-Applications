# Cahier des charges — <projet>

> **Clé de correction.** Distribuée après la première tentative, jamais avant.
> Ce n'est pas un moule : une équipe qui a produit mieux doit pouvoir le dire
> et le défendre.

**Équipe :** …
**Version :** v1 — <date>
**Sources :** `compte-rendu-entretien-01.md`, `-02.md`

Ce document formalise le **problème compris**, pas la solution. Aucun nom de
technologie, aucun nom de framework, aucune structure de base de données ici.

---

## 1. Contexte

Qui est le client, ce qu'il fait, comment il travaille aujourd'hui, et ce qui ne
va pas. En quelques paragraphes, dans ses mots.

## 2. Problème

Une formulation en trois à cinq phrases. Si vous n'y arrivez pas, c'est que
l'entretien n'est pas terminé.

## 3. Objectifs

Ce que le client veut obtenir, pas ce que l'application doit faire.

| # | Objectif | Comment on saura que c'est atteint |
|---|---|---|
| 1 | … | … |

## 4. Parties prenantes

| Partie prenante | Rôle | Ce qu'elle attend | Utilise l'application ? |
|---|---|---|---|
| … | … | … | oui / non |

N'oubliez pas celles qui n'utilisent pas l'application mais subissent ses effets.

## 5. Personas

Trois maximum. Chacun avec un contexte d'usage concret : où, sur quel support,
dans quelles conditions.

### <Prénom> — <rôle>

- Contexte d'usage :
- Objectif :
- Ce qui le bloque aujourd'hui :

## 6. Périmètre

### Dans le périmètre

- …

### Hors périmètre

Aussi important que la liste précédente. Chaque ligne cite la raison ou la
réponse du client.

| Élément écarté | Motif |
|---|---|
| … | le client a répondu … |

## 7. Contraintes

| # | Contrainte | Nature | Source |
|---|---|---|---|
| 1 | … | budget / délai / technique / réglementaire / humaine | entretien du … |

## 8. Règles métier

Les règles telles que le client les a énoncées, avant toute mise en forme de
spécification. C'est la matière première des `specs/`.

| # | Règle | Source |
|---|---|---|
| R-01 | … | CR-01/Q07 |

## 9. Exigences fonctionnelles

Une ligne par exigence. L'identifiant `REQ-xxx` est définitif : il sera cité par
les spécifications et ne se renumérote jamais.

**La colonne Source est obligatoire.** Elle contient soit la référence de
l'échange dont l'exigence est issue — `CR-01/Q07` —, soit la mention `déduit`
suivie d'une ligne de justification. Une règle métier qui ne vient d'aucun échange
consigné ne vient de nulle part : si vous l'avez apprise ailleurs, allez la
confirmer au prochain passage client, puis citez cet échange-là.

`./tools/traceability.sh` vérifie que chaque `REQ` a une source et que l'échange
cité existe réellement dans vos comptes rendus.

| ID | Exigence | Priorité | Persona | Source |
|---|---|---|---|---|
| REQ-001 | … | Must / Should / Could / Won't | … | CR-01/Q07 |

**Rappel :** le client plafonne le Must have à 3 cas d'usage.

## 10. Exigences non fonctionnelles

Trop souvent oubliées, et c'est là que les projets se cassent. Chacune doit être
vérifiable, et sourcée comme les autres.

| ID | Exigence | Comment on la vérifie | Source |
|---|---|---|---|
| REQ-1xx | … | … | CR-01/Q12 ou `déduit — <justification>` |

Passer en revue au minimum : volumétrie et pics de charge, support et conditions
réseau, langues, coût d'hébergement, sécurité et contrôle d'accès, données
personnelles et durée de conservation, déploiement, maintenance après livraison.

## 11. Questions restées ouvertes

| # | Question | Posée le | Réponse | Hypothèse retenue en attendant |
|---|---|---|---|---|
| 1 | … | … | en attente | … |

Une question sans réponse n'interdit pas d'avancer, à condition que l'hypothèse
soit écrite. Une hypothèse non écrite est une erreur en attente.

## 12. Validation client

| Version | Date | Présentée au client | Retour |
|---|---|---|---|
| v1 | … | oui / non | … |
