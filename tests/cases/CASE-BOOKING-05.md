# CASE-BOOKING-05 - une sortie à 5 inscrits est annulée au contrôle des 24 heures

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-4
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- 3 réservations confirmées, totalisant 5 participants.

## Scénario

```gherkin
Étant donné une sortie du 20 juillet à 10h00 comptant 5 inscrits
Et que nous sommes le 19 juillet à 10h00
Quand le contrôle du seuil de maintien s'exécute
Alors la sortie passe à l'état « annulée »
Et chacun des 3 clients est remboursé de la totalité de ce qu'il a payé
```

## Résultat attendu

- La sortie est à l'état « annulée ».
- Trois remboursements sont demandés au prestataire, chacun égal au montant payé.
- Le créneau n'est plus proposé à la réservation.

## Ce que ce cas ne vérifie pas

- Le message envoyé aux clients, non spécifié pour cette annulation, déclaré comme trou.
- L'annulation météo, qui est manuelle → `CASE-CANCEL-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_05_seuil_non_atteint_annule_et_rembourse` |
| Emplacement | `tests/Application/CapaciteEtPlacesDisponiblesTest.php` |
| Doublures | horloge, prestataire de paiement, envoi de messages |
