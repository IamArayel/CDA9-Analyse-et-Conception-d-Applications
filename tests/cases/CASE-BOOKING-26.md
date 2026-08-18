# CASE-BOOKING-26 - une sortie baleines hors saison est refusée à l'enregistrement

**Spécification :** `SPEC-BOOKING-02`
**Critères couverts :** AC-5
**Type :** erreur
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Date du 1er décembre, hors saison baleines.
- La demande ne passe pas par l'écran de sélection, elle est soumise directement.

## Scénario

```gherkin
Étant donné une date au 1er décembre, hors saison baleines
Quand une réservation de sortie baleines est soumise pour cette date
Alors elle est refusée
Et le motif est que les sorties baleines ne sont proposées que du 15 juin au 31 octobre
```

## Résultat attendu

- Le refus vient de l'enregistrement, pas seulement de l'affichage : masquer une option ne suffit pas à la rendre impossible.
- Une sortie dauphins à la même date resterait acceptée.

## Ce que ce cas ne vérifie pas

- L'affichage des types de sortie selon la saison → `CASE-BOOKING-24`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_26_sortie_baleines_hors_saison_refusee` |
| Emplacement | `tests/Application/CalendrierDesSortiesTest.php` |
| Doublures | horloge |
