# CASE-CANCEL-16 - le gérant annule un créneau, avec ou sans alerte préalable

**Spécification :** `SPEC-CANCEL-02`
**Critères couverts :** AC-1
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau A du 20 juillet à 10h, mis en alerte la veille.
- Créneau B du 21 juillet à 10h, jamais mis en alerte.

## Scénario

```gherkin
Étant donné un créneau en alerte et un créneau jamais alerté
Quand le gérant annule le premier
Alors il passe à l'état « annulé »
Quand le gérant annule le second
Alors il passe aussi à l'état « annulé »
Et l'alerte préalable n'était donc pas un passage obligé
```

## Résultat attendu

- Les deux créneaux sont annulés.
- L'alerte n'est pas un préalable : la météo peut se dégrader en quelques heures, et imposer une alerte empêcherait d'annuler un départ du matin décidé la veille au soir.

## Ce que ce cas ne vérifie pas

- Le calendrier d'envoi du message qui suit → `CASE-CANCEL-05`.
- Le remboursement des clients → `CASE-CANCEL-10`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_16_annulation_avec_ou_sans_alerte_prealable` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
