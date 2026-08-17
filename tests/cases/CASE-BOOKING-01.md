# CASE-BOOKING-01 - une demande égale aux places restantes est acceptée

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-2
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap, 12 places.
- 9 places déjà vendues, donc 3 restantes.

## Scénario

```gherkin
Étant donné un créneau du Ti Kap avec 3 places restantes
Quand un client valide un formulaire pour 2 adultes et 1 enfant
Alors la réservation est acceptée
Et elle passe à l'état « en attente de paiement »
```

## Résultat attendu

- La réservation existe, à l'état « en attente de paiement ».
- Le créneau affiche 0 place disponible aux autres clients.

## Ce que ce cas ne vérifie pas

- Le paiement lui-même → `CASE-BOOKING-03`.
- Le calcul du montant → cas de `SPEC-BOOKING-06`, à écrire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_01_demande_egale_aux_places_restantes_acceptee` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
