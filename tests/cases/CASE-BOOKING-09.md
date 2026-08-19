# CASE-BOOKING-09 - un paiement confirmé décompte les places et confirme la réservation

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-1, AC-3, AC-5
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Sortie baleines du 20 juillet à 10h sur le Ti Kap, 12 places, 6 vendues.
- Une réservation en attente de paiement pour 2 adultes et 1 enfant, soit 170 €,
  dont **51 € d'acompte** à verser.

## Scénario

```gherkin
Étant donné une réservation en attente de paiement de 170 €
Quand le client règle l'acompte de 51 € par carte bancaire
Et que le prestataire confirme la transaction
Alors la réservation passe à l'état « confirmée »
Et le créneau passe de 6 à 3 places disponibles
```

## Résultat attendu

- La réservation est à l'état « confirmée ».
- Le montant demandé au prestataire est **51 €**, soit 30 % de 170 €.
- Un solde de 119 € reste dû, dont le règlement relève de `SPEC-BOOKING-12`.
- Aucune donnée de carte n'existe dans les données de l'application, quel que soit l'endroit où l'on regarde.

## Ce que ce cas ne vérifie pas

- Le calcul du montant lui-même → cas de `SPEC-BOOKING-06`, à écrire.
- L'application d'un code de réduction → `CASE-BOOKING-13`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_09_paiement_confirme_decompte_les_places` |
| Emplacement | `tests/Application/PaiementTest.php` |
| Doublures | prestataire de paiement, horloge |
