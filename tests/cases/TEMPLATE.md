# CASE-<DOM>-nn - <ce que le cas vérifie, en une ligne>

**Spécification :** `SPEC-<DOM>-nn`
**Critères couverts :** AC-n, AC-n
**Type :** nominal | limite | erreur
**Niveau :** domaine | application | bout en bout
**Statut :** à automatiser | automatisé | manuel assumé

<!--
Un cas par fichier. Le nom du fichier est l'identifiant, il ne se renumérote
jamais. Le gabarit est décrit et justifié dans docs/strategie-de-test.md.

Règles :
- un cas vérifie UN comportement observable, pas un enchaînement d'écrans ;
- il cite la spécification ET les critères d'acceptation qu'il couvre ;
- il est écrit pour être lisible par le client, donc en vocabulaire métier ;
- s'il dépend de l'heure, il fixe l'instant courant explicitement.
-->

## Préconditions

L'état du monde avant le cas, en partant du jeu de données de référence
(`docs/strategie-de-test.md` §7). Ne lister que ce qui compte pour ce cas.

- …

## Scénario

```gherkin
Étant donné …
Et que nous sommes le … à …h…
Quand …
Alors …
Et …
```

## Résultat attendu

Ce qui doit être vrai à la fin, exprimé de façon observable et sans
interprétation. Un chiffre plutôt qu'un adjectif.

- …

## Ce que ce cas ne vérifie pas

Les voisins immédiats, avec le cas qui s'en charge. C'est ce qui empêche un
cas de grossir jusqu'à ne plus rien prouver.

- … → `CASE-<DOM>-nn`

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_<DOM>_nn_<comportement>` |
| Emplacement | `tests/…` |
| Doublures | prestataire de paiement, envoi de messages, horloge |
