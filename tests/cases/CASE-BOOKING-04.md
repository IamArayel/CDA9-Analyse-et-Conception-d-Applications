# CASE-BOOKING-04 - les places immobilisées redeviennent disponibles au bout de 15 minutes

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-9
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- 11 places vendues, donc 1 restante.
- Un client a validé son formulaire à 14h00 sans payer.

## Scénario

```gherkin
Étant donné une réservation immobilisant la dernière place depuis 14h00
Quand nous sommes le 18 juillet à 14h14
Alors le créneau affiche 0 place disponible
Quand nous sommes le 18 juillet à 14h16
Alors le créneau affiche 1 place disponible
Et un autre client peut la réserver
```

## Résultat attendu

- À 14h14 la place est indisponible, à 14h16 elle est disponible.
- La disponibilité change sans qu'aucune tâche planifiée n'ait été exécutée : l'expiration est évaluée à la lecture.

## Ce que ce cas ne vérifie pas

- Le nettoyage périodique des réservations échues, qui est un entretien et non une condition de correction.
- Le sort de la réservation expirée elle-même → cas de `SPEC-BOOKING-07`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_04_immobilisation_expiree_libere_les_places` |
| Emplacement | `tests/` |
| Doublures | horloge |
