# CASE-CANCEL-24 - aucun rappel pour un créneau annulé, et un canal en échec n'emporte pas l'autre

**Spécification :** `SPEC-CANCEL-05`
**Critères couverts :** AC-4, AC-6
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau A du 20 juillet à 10h, annulé la veille avant l'horaire de rappel.
- Créneau B du 21 juillet à 10h, maintenu, avec un client dont l'adresse e-mail est invalide.

## Scénario

```gherkin
Étant donné un créneau annulé avant l'horaire de rappel
Quand cet horaire est atteint
Alors aucun message de rappel n'est envoyé pour ce créneau
Étant donné un créneau maintenu et un client à l'adresse e-mail invalide
Quand l'horaire de rappel est atteint
Alors le SMS part quand même
Et l'échec de l'e-mail est enregistré
```

## Résultat attendu

- Zéro envoi pour le créneau annulé : les clients ont déjà reçu leur message d'annulation.
- Pour le créneau maintenu, un envoi réussi et un échec enregistré, pas un silence complet.

## Ce que ce cas ne vérifie pas

- Le message d'annulation lui-même → `CASE-CANCEL-10`.
- La délivrance réelle, qui ne dépend pas de nous.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_24_aucun_rappel_si_annule_et_echec_dun_canal_isole` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
