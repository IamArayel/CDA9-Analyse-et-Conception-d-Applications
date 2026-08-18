# CASE-CANCEL-05 - une annulation déclenche la confirmation 2 heures avant le départ

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-5
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 7h, en alerte, message d'alerte parti la veille à 18h00.
- Deux réservations confirmées, payées 100 € et 160 €.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 7h00 en alerte
Quand le gérant annule ce créneau le 20 juillet à 04h30
Alors la sortie passe à l'état « annulée »
Et le message de confirmation part à 05h00, soit 2 heures avant le départ
Et chaque client est remboursé de la totalité de ce qu'il a payé
```

## Résultat attendu

- Le message part à 05h00, ni à 04h30 ni au départ.
- Deux remboursements sont demandés, de 100 € et de 160 €, sans retenue.
- Aucun choix entre report, avoir et remboursement n'est proposé.

## Ce que ce cas ne vérifie pas

- Une annulation décidée après 05h00 → `CASE-CANCEL-08` pour l'envoi immédiat.
- Le remboursement d'une réservation réglée par bon cadeau, déclaré comme non défini.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_05_annulation_confirmee_deux_heures_avant_le_depart` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge, envoi de messages, prestataire de paiement |
