# CASE-CANCEL-23 - une réservation tardive déclenche le rappel immédiatement

**Spécification :** `SPEC-CANCEL-05`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie du 20 juillet à 7h.
- Horaire de rappel par défaut, soit le 19 juillet à 07h00, déjà passé.
- Un client réserve et paie le 19 juillet à 11h00.

## Scénario

```gherkin
Étant donné une sortie du 20 juillet à 7h00 dont l'horaire de rappel est déjà passé
Quand un client confirme sa réservation le 19 juillet à 11h00
Alors le message de rappel lui est envoyé immédiatement
Et non pas jamais
```

## Résultat attendu

- Le client reçoit son rappel, bien qu'il ait réservé après l'horaire programmé.
- Sans cette règle, tout client réservant la veille au matin pour un départ à 7h n'aurait jamais reçu de message, ce qui est fréquent puisque les réservations restent ouvertes jusqu'à midi la veille.

## Ce que ce cas ne vérifie pas

- L'absence de rappel sur un créneau annulé → `CASE-CANCEL-24`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_23_reservation_tardive_declenche_le_rappel_immediatement` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
