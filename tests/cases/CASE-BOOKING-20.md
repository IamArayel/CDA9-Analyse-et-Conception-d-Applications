# CASE-BOOKING-20 - une réservation pour une seule personne est acceptée

**Spécification :** `SPEC-BOOKING-01`
**Critères couverts :** AC-1, AC-6
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h, 5 places libres.
- Un client seul, coordonnées valides.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 10h00 avec 5 places libres
Quand le client renseigne nom, prénom, e-mail, mobile, 1 adulte et 0 enfant
Et qu'il valide le formulaire
Alors la réservation est acceptée
Et elle passe à l'état « en attente de paiement »
Et aucun écran ne lui a demandé l'âge d'un enfant
```

## Résultat attendu

- La réservation existe pour 1 participant : aucun minimum de personnes n'est imposé.
- C'est la règle corrigée en v3, la v1 et la v2 imposaient à tort 2 personnes.
- Aucun champ d'âge individuel n'apparaît nulle part dans le parcours.

## Ce que ce cas ne vérifie pas

- Le seuil de 6 inscrits, qui porte sur la sortie et non sur la réservation → `CASE-BOOKING-06`.
- Le calcul du montant → `CASE-BOOKING-27`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_20_reservation_une_seule_personne_acceptee` |
| Emplacement | `tests/` |
| Doublures | horloge |
