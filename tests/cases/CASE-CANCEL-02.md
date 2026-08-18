# CASE-CANCEL-02 - aucune alerte ne se déclenche sans action du gérant

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-2
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Journée du 20 juillet, trois créneaux, des réservations confirmées.
- Le gérant ne se connecte pas.

## Scénario

```gherkin
Étant donné des créneaux à venir et aucune action du gérant
Quand la veille à 18h00 est atteinte
Alors aucune sortie n'est passée en alerte
Et aucun message d'alerte n'est envoyé
```

## Résultat attendu

- Toutes les sorties restent à l'état « programmée ».
- Aucun envoi n'est demandé, quel que soit le canal.
- L'application n'interroge aucun service météo.

## Ce que ce cas ne vérifie pas

- Le message de rappel avant la sortie, qui lui est automatique → cas de `SPEC-CANCEL-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_02_aucune_alerte_sans_action_du_gerant` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge, envoi de messages |
