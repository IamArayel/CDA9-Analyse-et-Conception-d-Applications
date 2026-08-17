# CASE-BOOKING-21 - une réservation sans participant ou sans adulte est refusée

**Spécification :** `SPEC-BOOKING-01`
**Critères couverts :** AC-2, AC-3
**Type :** erreur
**Niveau :** domaine
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h, places disponibles.
- Coordonnées valides dans les deux tentatives.

## Scénario

```gherkin
Étant donné un formulaire de réservation
Quand le client déclare 0 adulte et 0 enfant
Alors la réservation est refusée
Quand le client déclare 0 adulte et 2 enfants
Alors la réservation est refusée
Et le motif est qu'un adulte au moins est requis dès qu'un enfant est déclaré
```

## Résultat attendu

- Aucune réservation n'est créée dans les deux cas.
- Le second refus repose sur une hypothèse d'équipe : le client n'a jamais évoqué de mineur non accompagné, mais toutes les règles écrites l'autorisaient.

## Ce que ce cas ne vérifie pas

- La limite d'âge de 4 ans, qui n'est pas contrôlable faute de champ d'âge → `CASE-BOOKING-22`.
- La capacité du créneau → `CASE-BOOKING-02`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_21_reservation_sans_participant_ou_sans_adulte_refusee` |
| Emplacement | `tests/` |
| Doublures | aucune |
