# CASE-BOOKING-40 - le solde se règle en ligne dans sa fenêtre, en une seule transaction

**Spécification :** `SPEC-BOOKING-12`
**Critères couverts :** AC-1, AC-4, AC-5
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 7h sur le Ti Kap.
- Une réservation de 100 € pour 2 adultes, acompte de 30 € versé.

## Scénario

```gherkin
Étant donné une réservation confirmée dont 30 € ont été versés
Et que nous sommes le 19 juillet à 9h00
Quand le client règle son solde en ligne
Alors 70 € sont demandés au prestataire
Et cette demande est distincte de celle de l'acompte
Quand il soumet une seconde fois
Alors aucun second débit n'est demandé
```

## Résultat attendu

- Le montant demandé est 70 €, et non 100 €.
- Deux transactions au total sur cette réservation, l'acompte et le solde,
  conformément à `ADR-006`.
- La réservation est soldée.

## Ce que ce cas ne vérifie pas

- L'acompte lui-même → `CASE-BOOKING-09`.
- Le règlement au quai → `CASE-ADMIN-18`.
- Les bornes de la fenêtre → `CASE-BOOKING-41`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_40_solde_regle_en_ligne_en_une_transaction` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
