# CASE-CANCEL-04 - une sortie maintenue ne déclenche aucun second message

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie du 20 juillet à 7h, en alerte depuis le 19 juillet.
- Le message d'alerte est parti le 19 juillet à 18h00.
- Le gérant ne prend aucune décision.

## Scénario

```gherkin
Étant donné un créneau en alerte dont l'alerte est partie
Quand le 20 juillet à 05h00 est atteint
Et que le gérant n'a pas annulé le créneau
Alors aucun message de confirmation n'est envoyé
Quand le 20 juillet à 07h00 est atteint
Alors la sortie a lieu
```

## Résultat attendu

- Un seul message a été envoyé à chaque client, celui de l'alerte.
- La sortie reste à l'état « en alerte » jusqu'au départ, puis a lieu.
- Le silence vaut maintien : c'est la règle voulue par le client.

## Ce que ce cas ne vérifie pas

- L'annulation et son message → `CASE-CANCEL-05`.
- Le message de rappel, qui part indépendamment.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_04_sortie_maintenue_aucun_second_message` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
