# CASE-BOOKING-11 - une double soumission du paiement ne produit qu'un seul débit

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Une réservation en attente de paiement de 130 €, dont **39 € d'acompte**.
- Le client valide deux fois, par double clic ou par retour arrière du navigateur.

## Scénario

```gherkin
Étant donné une réservation en attente de paiement de 130 €
Quand le client soumet son paiement deux fois de suite
Alors un seul encaissement de 39 € est demandé au prestataire
Et une seule réservation confirmée existe
Et les places ne sont décomptées qu'une fois
```

## Résultat attendu

- Un seul appel d'encaissement de l'acompte, pas deux.
- Une seule réservation confirmée pour ce client sur ce créneau.
- Le créneau ne perd que le nombre de places de la réservation, pas le double.

## Ce que ce cas ne vérifie pas

- Le comportement en cas de refus → `CASE-BOOKING-10`.
- Deux clients distincts sur la dernière place → `CASE-BOOKING-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_11_double_soumission_un_seul_debit` |
| Emplacement | `tests/Application/PaiementTest.php` |
| Doublures | prestataire de paiement, horloge |
