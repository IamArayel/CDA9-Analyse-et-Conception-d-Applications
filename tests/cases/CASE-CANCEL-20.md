# CASE-CANCEL-20 - un client en cours de réservation sur un créneau annulé est arrêté sans débit

**Spécification :** `SPEC-CANCEL-03`
**Critères couverts :** AC-4, AC-5
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 10h.
- Un client A a validé son formulaire, il est sur l'écran de paiement.
- Un client B est en train de remplir le formulaire.

## Scénario

```gherkin
Étant donné un client A sur l'écran de paiement et un client B remplissant le formulaire
Quand le gérant annule le créneau
Alors le paiement du client A est interrompu et il n'est pas débité
Et la validation du client B est refusée, avec le motif d'annulation
```

## Résultat attendu

- Aucun débit pour A, aucune réservation confirmée pour A ni pour B.
- Le motif affiché est l'annulation du créneau, pas un message générique.
- Les places immobilisées par A sont libérées.

## Ce que ce cas ne vérifie pas

- Le remboursement des clients déjà payés → `CASE-CANCEL-10`.
- Le paiement abouti après expiration de l'immobilisation, qui est un autre cas → `CASE-BOOKING-13`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_20_reservation_en_cours_sur_creneau_annule_sans_debit` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
