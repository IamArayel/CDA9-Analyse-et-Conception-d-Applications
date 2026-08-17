# CASE-CANCEL-09 - les horaires d'alerte modifiés s'appliquent aux envois à venir

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-9
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Paramètres par défaut : alerte à 18h00, confirmation 2 heures avant.
- Sortie du 21 juillet à 10h, mise en alerte le 20 juillet à 09h00.

## Scénario

```gherkin
Étant donné un créneau du 21 juillet à 10h00 mis en alerte
Quand le gérant porte l'heure d'envoi de l'alerte à 17h00 et le délai de confirmation à 3 heures
Alors le message d'alerte part le 20 juillet à 17h00
Et si le créneau est annulé, la confirmation part le 21 juillet à 07h00
```

## Résultat attendu

- Les deux valeurs sont lues au moment de l'envoi, pas figées à la mise en alerte.
- Un envoi déjà parti n'est pas rejoué.

## Ce que ce cas ne vérifie pas

- L'horaire du message de rappel, réglé séparément → `SPEC-CANCEL-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_09_horaires_modifies_appliques_aux_envois_a_venir` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
