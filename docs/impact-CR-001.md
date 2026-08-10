# Analyse d'impact — CR-001

**À remplir en J8 matin. Ne pas remplir avant.**

**Demande du client :** …
**Reçue le :** J7, 16h15
**Rédigée par :** …

---

> **Interdiction de modifier le code avant que cette analyse soit complète.**
>
> La modification descend la chaîne dans cet ordre : cahier des charges → specs →
> UML → modèle de données → cas de test → tests → code. Commencer par le code,
> c'est perdre la trace de pourquoi il a changé — et c'est exactement ce que ce
> module cherche à vous faire éviter.

---

## 1. Ce que le client demande, reformulé

En langage métier, en trois à cinq phrases. Si la reformulation est floue, posez
les questions avant de descendre la chaîne.

## 2. Questions posées au client

| # | Question | Réponse |
|---|---|---|
| 1 | … | … |

## 3. Impact — cahier des charges

| Exigence | Impact | Action |
|---|---|---|
| REQ-0xx | modifiée / supprimée / inchangée | … |
| REQ-0xx *(nouvelle)* | ajoutée | … |

## 4. Impact — spécifications

| Spécification | Impact | Ce qui change exactement |
|---|---|---|
| SPEC-…-0x | modifiée | … |
| SPEC-…-0x | inchangée | *pourquoi elle ne bouge pas, malgré les apparences* |

La colonne de droite compte autant pour les specs inchangées : justifier une
non-modification est un résultat d'analyse.

## 5. Impact — conception

| Artefact | Impact | Ce qui change |
|---|---|---|
| `uml/domain.puml` | … | … |
| `uml/sequences/…` | … | … |
| MCD / MLD | … | … |
| architecture | … | … |

Question à traiter explicitement : la demande introduit-elle un **état nouveau**
ou une **donnée nouvelle** qui n'existaient pas dans le modèle ? Si oui, elle ne
peut pas être absorbée par le code seul.

## 6. Impact — tests

| Cas de test | Impact |
|---|---|
| CASE-…-xx | modifié / obsolète / inchangé |
| CASE-…-xx *(nouveau)* | à écrire |

Les tests devenus rouges après cette étape sont un résultat normal et attendu.

## 7. Impact — code

Seulement maintenant.

| Composant | Impact |
|---|---|
| … | … |

## 8. Effets de bord identifiés

Ce que la demande touche sans que le client l'ait envisagé. C'est la partie qui
distingue une analyse d'impact d'une liste de tâches.

- …

## 9. Ce que nous ne ferons pas dans le temps restant

Assumé, et à annoncer au client lors de la présentation de J10.

- …

## 10. Ordre d'exécution retenu

| # | Étape | Qui |
|---|---|---|
| 1 | … | … |
