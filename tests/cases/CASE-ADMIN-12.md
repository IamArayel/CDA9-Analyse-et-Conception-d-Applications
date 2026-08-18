# CASE-ADMIN-12 - un nom déjà pris ou une capacité invalide sont refusés

**Spécification :** `SPEC-ADMIN-05`
**Critères couverts :** AC-3, AC-4
**Type :** erreur
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Flotte comprenant « Ti Kap » et « Le Grand Bleu ».

## Scénario

```gherkin
Étant donné la flotte existante
Quand le gérant crée un bateau nommé « Ti Kap »
Alors la création est refusée
Quand il crée un bateau avec une capacité de 0
Alors la création est refusée
Quand il crée un bateau avec une capacité de 8,5
Alors la création est refusée
```

## Résultat attendu

- Aucun bateau n'est créé dans les trois cas.
- Le nom identifie le bateau sur le planning et pour le gérant, il doit rester unique.

## Ce que ce cas ne vérifie pas

- La suppression d'un bateau créé par erreur, non prévue.
- La modification d'un bateau existant, hors périmètre.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_12_nom_pris_ou_capacite_invalide_refuses` |
| Emplacement | `tests/Domaine/CreationDunBateauTest.php` |
| Doublures | aucune |
