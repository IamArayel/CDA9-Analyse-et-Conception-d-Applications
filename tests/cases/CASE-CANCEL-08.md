# CASE-CANCEL-08 - une alerte posée après l'heure programmée part immédiatement

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-8
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 7h.
- Le gérant met le créneau en alerte le 19 juillet à 21h00, soit après 18h00.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 7h00
Quand le gérant le met en alerte le 19 juillet à 21h00
Alors le message d'alerte part immédiatement
Et il ne reste pas en attente de l'horaire programmé
```

## Résultat attendu

- L'envoi est demandé à 21h00, pas repoussé au lendemain.
- Le message reste identique à celui d'une alerte posée à l'heure.

## Ce que ce cas ne vérifie pas

- L'annulation décidée après le repère des 2 heures, qui suit la même logique d'envoi immédiat.
- L'alerte posée à l'heure → `CASE-CANCEL-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_08_alerte_posee_apres_lheure_part_immediatement` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge, envoi de messages |
