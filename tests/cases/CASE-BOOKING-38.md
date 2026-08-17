# CASE-BOOKING-38 - un paiement abouti après l'expiration est confirmé si la place est libre

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-9
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- 11 places vendues, donc 1 restante.
- Le client A a validé son formulaire pour 1 adulte à 14h00, montant 50 €.
  Son immobilisation vient à échéance à 14h15.
- Aucun autre client n'a réservé ce créneau depuis.

## Scénario

```gherkin
Étant donné une réservation immobilisant la dernière place depuis 14h00
Et que nous sommes le 18 juillet à 14h16
Et que personne d'autre n'a pris cette place
Quand le client A confirme son paiement de 50 €
Alors sa réservation passe à l'état « confirmée »
Et le créneau affiche 0 place disponible
```

## Résultat attendu

- La réservation de A est à l'état « confirmée », 1 seul débit de 50 €.
- Le créneau compte 12 participants et affiche 0 place disponible.
- L'expiration de l'immobilisation n'a pas annulé la réservation : elle a
  seulement cessé de retenir la place.

## Ce que ce cas ne vérifie pas

- La libération des places à l'expiration, vue du côté des autres clients →
  `CASE-BOOKING-04`.
- Le même paiement lorsque la place est partie entre-temps →
  `CASE-BOOKING-39`.
- Le déroulement de l'encaissement lui-même → `CASE-BOOKING-09`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_38_paiement_apres_expiration_confirme_si_place_libre` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
