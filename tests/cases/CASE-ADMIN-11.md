# CASE-ADMIN-11 - un bateau sans forfait n'est pas proposé à la privatisation

**Spécification :** `SPEC-ADMIN-05`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- « Le Petit Bleu » vient d'être créé, sans forfait de privatisation.

## Scénario

```gherkin
Étant donné un bateau créé sans forfait de privatisation
Quand un client consulte les formules disponibles sur ce bateau
Alors la privatisation ne lui est pas proposée
Quand le gérant saisit un forfait de 450 € pour ce bateau
Alors la privatisation devient proposée au tarif de 450 €
```

## Résultat attendu

- La privatisation est indisponible tant que le forfait est vide, et disponible dès qu'il est saisi.
- C'est la contradiction relevée en revue : la privatisation est tarifée par bateau, or le formulaire de création n'en demande pas.

## Ce que ce cas ne vérifie pas

- Le blocage du bateau entier par une privatisation → `CASE-BOOKING-29`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_11_bateau_sans_forfait_pas_de_privatisation` |
| Emplacement | `tests/Application/FlotteTest.php` |
| Doublures | horloge |
