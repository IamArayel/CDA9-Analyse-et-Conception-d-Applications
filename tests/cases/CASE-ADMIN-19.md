# CASE-ADMIN-19 - un pointage annulé laisse les deux gestes dans la trace

**Spécification :** `SPEC-ADMIN-07`
**Critères couverts :** AC-2, AC-3, AC-5
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Une réservation confirmée dont le solde de 70 € reste dû.
- Une seconde réservation, annulée.

## Scénario

```gherkin
Étant donné une réservation dont le solde reste dû
Quand le gérant la pointe comme soldée à 6h50
Et qu'il annule ce pointage à 6h52
Alors la réservation redevient non soldée
Et la trace porte les deux gestes, horodatés
Quand il la pointe de nouveau à 6h55
Alors la trace en porte trois
Quand il tente de pointer la réservation annulée
Alors le pointage est refusé
```

## Résultat attendu

- Trois écritures conservées, et non un drapeau écrasé deux fois.
- Chaque écriture porte son heure et l'auteur du geste.
- Une réservation annulée n'est pas pointable : il n'y a plus rien à encaisser.

## Ce que ce cas ne vérifie pas

- Le pointage nominal → `CASE-ADMIN-18`.
- La durée de conservation de la trace, qui suit la réservation par hypothèse
  d'équipe, `SPEC-NFR-04` ne visant que les données personnelles.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_19_pointage_reversible_et_trace` |
| Emplacement | `tests/Application/PointageDuSoldeTest.php` |
| Doublures | horloge |
