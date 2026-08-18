# CASE-ADMIN-03 - un mot de passe non conforme est refusé à la définition

**Spécification :** `SPEC-ADMIN-01`
**Critères couverts :** AC-2
**Type :** limite
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- La règle : 8 caractères au moins, une majuscule, une minuscule, un chiffre, un caractère spécial.

## Scénario

```gherkin
Étant donné la définition d'un mot de passe
Quand il compte 7 caractères mais respecte les quatre autres conditions
Alors il est refusé
Quand il compte 12 caractères sans caractère spécial
Alors il est refusé
Quand il compte 8 caractères avec majuscule, minuscule, chiffre et caractère spécial
Alors il est accepté
```

## Résultat attendu

- Chaque condition manquante suffit à refuser, la longueur comme la composition.
- Huit caractères exactement suffisent : la borne est inclusive.
- Le mot de passe n'est jamais stocké en clair.

## Ce que ce cas ne vérifie pas

- La connexion elle-même → `CASE-ADMIN-01`.
- Une éventuelle authentification à deux facteurs, écartée en revue.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_03_mot_de_passe_non_conforme_refuse` |
| Emplacement | `tests/Domaine/ComplexiteDuMotDePasseTest.php` |
| Doublures | aucune |
