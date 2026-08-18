# CASE-BOOKING-06 - une sortie à exactement 6 inscrits est maintenue

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Sortie dauphins du 20 juillet à 10h.
- 2 réservations confirmées, totalisant 6 participants.

## Scénario

```gherkin
Étant donné une sortie du 20 juillet à 10h00 comptant 6 inscrits
Et que nous sommes le 19 juillet à 10h00
Quand le contrôle du seuil de maintien s'exécute
Alors la sortie reste à l'état « programmée »
Et aucun remboursement n'est demandé
```

## Résultat attendu

- La sortie est maintenue : le seuil est « à partir de 6 », donc 6 suffit.
- Aucun appel au prestataire de paiement.

## Ce que ce cas ne vérifie pas

- Le cas à 5 inscrits → `CASE-BOOKING-05`.
- Le cas d'une privatisation, qui n'est pas soumise au seuil.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_06_seuil_exactement_atteint_maintient_la_sortie` |
| Emplacement | `tests/Domaine/SeuilDeMaintienTest.php` |
| Doublures | horloge |
