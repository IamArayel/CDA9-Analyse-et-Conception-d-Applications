# CASE-CANCEL-12 - une réservation non payée au moment de l'annulation ne donne lieu à aucun remboursement

**Spécification :** `SPEC-CANCEL-04`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 10h.
- Deux réservations confirmées et payées, une troisième en attente de paiement, places immobilisées.

## Scénario

```gherkin
Étant donné un créneau portant deux réservations payées et une en attente de paiement
Quand le gérant annule le créneau
Alors deux remboursements sont demandés, pas trois
Et l'immobilisation de la troisième est libérée
Et son client reçoit le message d'annulation
```

## Résultat attendu

- Exactement deux appels de remboursement.
- Aucun montant nul n'est envoyé au prestataire.
- Le client non payé est prévenu comme les autres : il est inscrit, même s'il n'a rien versé.

## Ce que ce cas ne vérifie pas

- L'expiration normale d'une immobilisation → `CASE-BOOKING-04`.
- Le paiement qui aboutirait après l'annulation, couvert par `SPEC-CANCEL-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_12_reservation_non_payee_aucun_remboursement` |
| Emplacement | `tests/` |
| Doublures | envoi de messages, prestataire de paiement, horloge |
