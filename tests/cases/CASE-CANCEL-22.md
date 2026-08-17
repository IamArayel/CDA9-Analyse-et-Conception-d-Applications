# CASE-CANCEL-22 - l'horaire de rappel modifié s'applique aux envois à venir

**Spécification :** `SPEC-CANCEL-05`
**Critères couverts :** AC-3
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie du 21 juillet à 10h, une réservation confirmée.
- Le gérant porte le délai de rappel de 24 à 48 heures le 18 juillet.

## Scénario

```gherkin
Étant donné une réservation confirmée pour une sortie le 21 juillet à 10h00
Quand le gérant porte le délai de rappel à 48 heures
Alors le message part le 19 juillet à 10h00
Et un rappel déjà parti pour une autre sortie n'est pas rejoué
```

## Résultat attendu

- Le délai est lu au moment de l'envoi, pas figé à la confirmation de la réservation.
- Aucun doublon sur les envois déjà effectués.

## Ce que ce cas ne vérifie pas

- Les horaires de l'alerte et de la confirmation, réglés séparément → `CASE-CANCEL-09`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_22_horaire_de_rappel_modifie_applique_aux_envois_a_venir` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
