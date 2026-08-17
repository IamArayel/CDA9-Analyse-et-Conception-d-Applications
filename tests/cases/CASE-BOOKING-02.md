# CASE-BOOKING-02 - une demande supérieure aux places restantes est refusée

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-1
**Type :** erreur
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap, 12 places.
- 9 places déjà vendues, donc 3 restantes.

## Scénario

```gherkin
Étant donné un créneau du Ti Kap avec 3 places restantes
Quand un client valide un formulaire pour 3 adultes et 1 enfant
Alors la réservation est refusée
Et le motif indiqué est le nombre de places disponibles
```

## Résultat attendu

- Aucune réservation n'est créée.
- Le créneau affiche toujours 3 places disponibles.
- Adultes et enfants comptent pour une place chacun : 4 demandées pour 3 restantes.

## Ce que ce cas ne vérifie pas

- La capacité du Grand Bleu, identique dans son principe.
- Le cas d'un groupe supérieur à la capacité totale du bateau, couvert par `SPEC-BOOKING-01`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_02_demande_superieure_aux_places_restantes_refusee` |
| Emplacement | `tests/` |
| Doublures | horloge |
