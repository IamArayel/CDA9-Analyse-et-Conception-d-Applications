# CASE-CANCEL-21 - le message de rappel part 24 heures avant, sur les deux canaux

**Spécification :** `SPEC-CANCEL-05`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 10h, deux réservations confirmées.
- Horaire d'envoi laissé à sa valeur par défaut.
- Le gérant a saisi la prévision météo du jour.

## Scénario

```gherkin
Étant donné deux réservations confirmées pour une sortie le 20 juillet à 10h00
Et un horaire d'envoi laissé par défaut
Quand le 19 juillet à 10h00 est atteint
Alors chaque client reçoit le message type par SMS et par e-mail
Et le message contient les conditions météo prévues et les affaires à prévoir
Et le gérant n'a effectué aucune action
```

## Résultat attendu

- La valeur par défaut est bien 24 heures avant le départ.
- Quatre envois, deux clients sur deux canaux.
- La prévision météo vient de la saisie du gérant : l'application n'interroge aucun service externe.

## Ce que ce cas ne vérifie pas

- Le contenu rédactionnel du message, non fourni par le client.
- Le message d'alerte météo, qui part indépendamment → `CASE-CANCEL-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_21_rappel_24h_avant_sur_deux_canaux` |
| Emplacement | `tests/Application/MessageDeRappelTest.php` |
| Doublures | horloge, envoi de messages |
