# CASE-ADMIN-01 - le compte unique du gérant accède à l'espace de gestion

**Spécification :** `SPEC-ADMIN-01`
**Critères couverts :** AC-1
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Un seul compte existe, celui du gérant, avec un mot de passe conforme.

## Scénario

```gherkin
Étant donné le compte unique du gérant
Quand il saisit son e-mail et son mot de passe corrects
Alors il accède à l'espace de gestion
Et il y retrouve les tarifs, le planning, les horaires et la flotte
```

## Résultat attendu

- La session est ouverte et les quatre sections de gestion sont accessibles.
- Aucun écran de création d'un second compte n'existe nulle part.

## Ce que ce cas ne vérifie pas

- Le refus d'identifiants incorrects → `CASE-ADMIN-02`.
- La règle de complexité du mot de passe → `CASE-ADMIN-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_01_compte_unique_accede_a_lespace_de_gestion` |
| Emplacement | `tests/` |
| Doublures | aucune |
