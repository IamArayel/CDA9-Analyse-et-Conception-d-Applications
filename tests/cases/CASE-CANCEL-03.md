# CASE-CANCEL-03 - le message d'alerte part la veille à 18h par SMS et par e-mail

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-3
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 7h, mise en alerte le 19 juillet à 09h00.
- Deux réservations confirmées sur ce créneau.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 7h00 mis en alerte le 19 juillet à 09h00
Quand le 19 juillet à 18h00 est atteint
Alors chacun des 2 clients reçoit un message d'alerte
Et ce message part par SMS et par e-mail
Et il annonce une décision communiquée 2 heures avant le départ
```

## Résultat attendu

- Quatre envois sont demandés, deux clients fois deux canaux.
- Chaque envoi laisse une trace portant son type, son canal et sa date.
- Aucun message n'est envoyé avant 18h00.

## Ce que ce cas ne vérifie pas

- Le contenu rédactionnel du message, non fourni par le client, déclaré comme trou.
- L'alerte posée trop tard → `CASE-CANCEL-08`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_03_message_alerte_la_veille_a_18h_sur_deux_canaux` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge, envoi de messages |
