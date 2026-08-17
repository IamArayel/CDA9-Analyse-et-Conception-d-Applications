# CASE-BOOKING-27 - les créneaux ferment à midi, la veille ou le jour même

**Spécification :** `SPEC-BOOKING-04`
**Critères couverts :** AC-1, AC-2, AC-4
**Type :** limite
**Niveau :** domaine
**Statut :** à automatiser

## Préconditions

- Sorties du 20 juillet, créneaux de 7h, 10h et 14h.
- Aucune contrainte de capacité.

## Scénario

```gherkin
Étant donné les créneaux du 20 juillet
Quand nous sommes le 19 juillet à 11h59
Alors les créneaux de 7h et 10h sont encore réservables
Quand nous sommes le 19 juillet à 12h00
Alors ils ne le sont plus et n'apparaissent plus dans les créneaux proposés
Quand nous sommes le 20 juillet à 11h59
Alors le créneau de 14h est encore réservable
Quand nous sommes le 20 juillet à 12h00
Alors il ne l'est plus
```

## Résultat attendu

- La fermeture est effective **à partir de** 12h00, pas après : 11h59 accepte, 12h00 refuse.
- Un créneau fermé disparaît de la liste, il n'est pas affiché comme complet.
- L'heure de référence est l'heure locale de l'exploitation.

## Ce que ce cas ne vérifie pas

- La réservation validée avant midi et payée après → `CASE-BOOKING-28`.
- L'annulation d'un créneau, qui produit une indisponibilité d'une autre nature → `SPEC-CANCEL-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_27_fermeture_des_creneaux_a_midi` |
| Emplacement | `tests/` |
| Doublures | horloge |
