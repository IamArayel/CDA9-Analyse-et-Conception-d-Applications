# CASE-BOOKING-15 - un bon cadeau insuffisant laisse la différence à payer par carte

**Spécification :** `SPEC-BOOKING-09`
**Critères couverts :** AC-2, AC-3
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation baleines de 170 € pour 2 adultes et 1 enfant.
- Un bon cadeau valide et non utilisé de 100 €.

## Scénario

```gherkin
Étant donné une réservation de 170 € et un bon cadeau de 100 €
Quand le bénéficiaire saisit le code au moment de payer
Alors 100 € sont déduits du montant total
Et 70 € restent à payer par carte bancaire
Quand le paiement de 70 € est confirmé
Alors la réservation est confirmée et le code est marqué utilisé
```

## Résultat attendu

- Le montant demandé au prestataire est 70 €, pas 170 €.
- Le bon est consommé en une fois, quel que soit le reliquat.
- Le type de sortie réservé n'entre pas dans la décision : le bon s'applique à n'importe quelle réservation.

## Ce que ce cas ne vérifie pas

- Le cas d'un bon supérieur au prix → `CASE-BOOKING-16`.
- Le cas d'un bon exactement égal au prix → `CASE-BOOKING-12`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_15_bon_cadeau_insuffisant_solde_paye_par_carte` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement, horloge |
