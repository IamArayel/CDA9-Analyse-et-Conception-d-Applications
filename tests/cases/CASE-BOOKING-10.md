# CASE-BOOKING-10 - un paiement refusé ne confirme rien et ne décompte aucune place

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-2
**Type :** erreur
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie baleines du 20 juillet à 10h, 6 places vendues sur 12.
- Une réservation en attente de paiement de 170 €, places immobilisées.

## Scénario

```gherkin
Étant donné une réservation en attente de paiement de 170 €
Quand le prestataire refuse la transaction
Alors la réservation reste à l'état « en attente de paiement »
Et le créneau affiche toujours 6 places vendues
Et le client n'est pas débité
```

## Résultat attendu

- Aucune réservation confirmée n'est créée.
- Les places restent immobilisées jusqu'à l'expiration, elles ne sont pas décomptées pour autant.
- Le client peut retenter tant que l'immobilisation court.

## Ce que ce cas ne vérifie pas

- La libération des places à l'expiration → `CASE-BOOKING-04`.
- L'abandon du tunnel, qui produit le même état sans réponse du prestataire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_10_paiement_refuse_ne_confirme_ni_ne_decompte` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement, horloge |
