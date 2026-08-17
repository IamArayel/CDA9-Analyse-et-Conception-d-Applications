# CASE-BOOKING-17 - un bon cadeau déjà utilisé est refusé

**Spécification :** `SPEC-BOOKING-09`
**Critères couverts :** AC-5
**Type :** erreur
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Un bon cadeau de 100 €, déjà consommé sur une réservation antérieure.
- Une nouvelle réservation de 130 € au nom du même bénéficiaire.

## Scénario

```gherkin
Étant donné un bon cadeau déjà utilisé
Quand son code est saisi sur une nouvelle réservation
Alors le code est refusé
Et le montant restant dû reste de 130 €
```

## Résultat attendu

- Aucune déduction n'est appliquée.
- Le message de refus ne distingue pas un code déjà utilisé d'un code inexistant, pour ne pas permettre de sonder les codes.

## Ce que ce cas ne vérifie pas

- Le code expiré → `CASE-BOOKING-18`.
- Le cumul avec un avoir → `CASE-BOOKING-19`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_17_bon_cadeau_deja_utilise_refuse` |
| Emplacement | `tests/` |
| Doublures | horloge |
