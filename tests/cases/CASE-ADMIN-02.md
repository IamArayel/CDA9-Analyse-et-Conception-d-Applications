# CASE-ADMIN-02 - un accès sans session ou avec de mauvais identifiants est refusé

**Spécification :** `SPEC-ADMIN-01`
**Critères couverts :** AC-3, AC-4
**Type :** erreur
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Le compte du gérant existe.
- Aucune session ouverte.

## Scénario

```gherkin
Étant donné l'écran de connexion
Quand une tentative est faite avec un e-mail inconnu
Alors l'accès est refusé
Quand une tentative est faite avec le bon e-mail et un mauvais mot de passe
Alors l'accès est refusé avec le même message que précédemment
Quand une page de l'espace de gestion est demandée sans session ouverte
Alors aucune donnée de gestion n'est affichée
```

## Résultat attendu

- Les deux refus produisent un message identique : rien ne permet de deviner si l'e-mail existe.
- L'accès direct par URL ne contourne rien, il renvoie vers la connexion.

## Ce que ce cas ne vérifie pas

- Le verrouillage après plusieurs échecs, non demandé par le client et déclaré non défini.
- La réinitialisation d'un mot de passe oublié, non prévue.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_02_acces_refuse_sans_session_ou_identifiants_errones` |
| Emplacement | `tests/Application/AccesALespaceDeGestionTest.php` |
| Doublures | aucune |
