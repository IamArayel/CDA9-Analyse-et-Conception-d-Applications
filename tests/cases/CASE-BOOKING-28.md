# CASE-BOOKING-28 - une réservation validée avant midi peut être payée après

**Spécification :** `SPEC-BOOKING-04`
**Critères couverts :** AC-3
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 14h, fermeture à 12h00 le jour même.
- Un client valide son formulaire à 11h55, immobilisation jusqu'à 12h10.

## Scénario

```gherkin
Étant donné un formulaire validé le 20 juillet à 11h55 pour le créneau de 14h
Quand le client paie à 12h05
Alors la réservation est confirmée
Et le fait que 12h00 soit passé ne la refuse pas
```

## Résultat attendu

- La fermeture s'apprécie à la **validation du formulaire**, pas à l'encaissement.
- Le client dispose de ses 15 minutes même si elles franchissent l'heure de fermeture.
- Sans cette règle, un client validant à 11h59 serait refusé après avoir saisi sa carte, ce que `ADR-003` cherche précisément à éviter.

## Ce que ce cas ne vérifie pas

- Le paiement abouti après expiration de l'immobilisation → `CASE-BOOKING-13`.
- Une nouvelle validation après 12h00, qui est refusée → `CASE-BOOKING-27`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_28_validation_avant_midi_paiement_apres_accepte` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
