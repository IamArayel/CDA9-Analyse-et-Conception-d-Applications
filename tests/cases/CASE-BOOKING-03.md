# CASE-BOOKING-03 - le second client sur la dernière place est refusé avant de payer

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-3, AC-8
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- 11 places vendues, donc 1 restante.
- Deux clients consultent le créneau au même instant.

## Scénario

```gherkin
Étant donné un créneau avec 1 place restante
Et que nous sommes le 18 juillet à 14h00
Quand le premier client valide son formulaire pour 1 adulte
Alors ses places sont immobilisées jusqu'à 14h15
Quand le second client valide son formulaire pour 1 adulte à 14h01
Alors sa réservation est refusée
Et il n'a pas atteint l'écran de paiement
```

## Résultat attendu

- Une seule réservation existe, celle du premier client.
- Le second n'a jamais été débité, et n'a pas saisi de carte.
- L'immobilisation du premier porte une échéance à 14h15.

## Ce que ce cas ne vérifie pas

- La libération des places à l'expiration → `CASE-BOOKING-04`.
- Le paiement abouti après expiration → cas de `SPEC-BOOKING-07`, à écrire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_03_derniere_place_second_client_refuse_avant_paiement` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
