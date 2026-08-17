# CASE-ADMIN-04 - un tarif modifié ne change pas les réservations déjà payées

**Spécification :** `SPEC-ADMIN-02`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Tarif adulte dauphins à 50 €.
- Une réservation payée 100 € pour 2 adultes.

## Scénario

```gherkin
Étant donné une réservation dauphins déjà payée 100 € pour 2 adultes
Quand le gérant porte le tarif adulte dauphins à 55 €
Alors la réservation déjà payée reste à 100 €
Et une nouvelle réservation pour 2 adultes est calculée à 110 €
```

## Résultat attendu

- Le montant de la réservation payée est inchangé, à l'euro près.
- Le nouveau tarif s'applique à partir de la création de la réservation suivante.
- Le montant est recopié sur la réservation, il n'est pas relu dans la grille.

## Ce que ce cas ne vérifie pas

- Le récapitulatif présenté mais non encore payé → `CASE-BOOKING-32`.
- Le forfait de privatisation, porté par le bateau → `CASE-ADMIN-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_04_tarif_modifie_nimpacte_pas_les_reservations_payees` |
| Emplacement | `tests/` |
| Doublures | horloge |
