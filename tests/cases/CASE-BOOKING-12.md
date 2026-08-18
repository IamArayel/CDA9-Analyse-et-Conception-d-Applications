# CASE-BOOKING-12 - un montant dû nul confirme la réservation sans paiement carte

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-6
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Une réservation dauphins de 100 € pour 2 adultes.
- Un bon cadeau valide de 100 €, non utilisé.

## Scénario

```gherkin
Étant donné une réservation de 100 € et un bon cadeau de 100 €
Quand le client saisit le code du bon
Alors le montant restant dû est de 0 €
Quand il valide
Alors la réservation est confirmée sans passer par le paiement carte
Et aucun appel n'est fait au prestataire de paiement
```

## Résultat attendu

- La réservation est confirmée, les places décomptées.
- Le prestataire de paiement n'est jamais sollicité.
- Le bon cadeau est marqué utilisé.

## Ce que ce cas ne vérifie pas

- Le surplus perdu quand le bon dépasse le prix → `CASE-BOOKING-15`.
- Le solde à payer quand le bon est insuffisant → `CASE-BOOKING-14`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_12_montant_du_nul_confirme_sans_paiement_carte` |
| Emplacement | `tests/Application/PaiementTest.php` |
| Doublures | prestataire de paiement, horloge |
