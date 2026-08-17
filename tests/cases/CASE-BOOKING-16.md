# CASE-BOOKING-16 - le surplus d'un bon cadeau supérieur au prix est perdu

**Spécification :** `SPEC-BOOKING-09`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation dauphins de 60 € pour 1 adulte et 1 enfant.
- Un bon cadeau valide et non utilisé de 150 €.

## Scénario

```gherkin
Étant donné une réservation de 60 € et un bon cadeau de 150 €
Quand le bénéficiaire saisit le code au moment de payer
Alors le montant restant dû est de 0 €
Et la réservation est confirmée
Et aucun avoir n'est produit pour les 90 € non consommés
Et le code ne peut plus servir
```

## Résultat attendu

- Le bon est marqué utilisé, sans reliquat.
- Aucun code, aucun avoir, aucun remboursement n'est créé pour le surplus.
- La règle est défavorable au bénéficiaire, et c'est celle que le client a posée deux fois.

## Ce que ce cas ne vérifie pas

- Le fractionnement d'un bon sur plusieurs réservations, explicitement écarté.
- Le même surplus pour un code d'avoir → cas de `SPEC-BOOKING-10`, à écrire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_16_surplus_du_bon_cadeau_est_perdu` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement, horloge |
