# CASE-CANCEL-17 - rien n'annule un créneau à la place du gérant

**Spécification :** `SPEC-CANCEL-02`
**Critères couverts :** AC-2, AC-3
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 7h, en alerte depuis le 19 juillet.
- 8 inscrits, donc au-dessus du seuil de maintien.
- Le gérant ne prend aucune décision.

## Scénario

```gherkin
Étant donné un créneau en alerte comptant 8 inscrits
Quand le 20 juillet à 07h00 est atteint sans action du gérant
Alors le créneau n'a jamais été annulé
Et la sortie a lieu
```

## Résultat attendu

- Aucun passage à l'état « annulé », quelles que soient les prévisions : aucune règle météo n'est automatisée.
- Une alerte laissée sans suite vaut maintien, elle n'annule rien par expiration.

## Ce que ce cas ne vérifie pas

- L'annulation automatique au seuil de 6 inscrits, seul cas automatisé de l'outil → `CASE-BOOKING-05`.
- L'absence de message quand la sortie est maintenue → `CASE-CANCEL-04`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_17_aucune_annulation_sans_decision_du_gerant` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
