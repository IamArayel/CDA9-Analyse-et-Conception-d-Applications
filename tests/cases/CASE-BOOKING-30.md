# CASE-BOOKING-30 - une privatisation est refusée sur un bateau portant déjà des places vendues

**Spécification :** `SPEC-BOOKING-05`
**Critères couverts :** AC-4
**Type :** erreur
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 10h.
- Le Ti Kap porte déjà 2 places vendues sur ce créneau.

## Scénario

```gherkin
Étant donné le Ti Kap portant 2 places vendues le 20 juillet à 10h00
Quand un client demande la privatisation de ce bateau au même créneau
Alors la demande est refusée
Et Le Grand Bleu, libre, reste privatisable
```

## Résultat attendu

- Aucune privatisation n'est créée sur le Ti Kap.
- Les 2 réservations existantes ne sont ni annulées ni déplacées : le gérant ne réattribue rien automatiquement.

## Ce que ce cas ne vérifie pas

- La répartition des passagers entre bateaux, hors périmètre.
- Le cas inverse, une place demandée après privatisation → `CASE-BOOKING-29`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_30_privatisation_refusee_si_places_deja_vendues` |
| Emplacement | `tests/` |
| Doublures | horloge |
