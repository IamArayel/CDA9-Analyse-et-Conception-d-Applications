# CASE-BOOKING-22 - l'interdiction aux moins de 4 ans est affichée, pas contrôlée

**Spécification :** `SPEC-BOOKING-01`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** bout en bout
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h.
- Un client réserve pour 2 adultes et 1 enfant de 3 ans, ce que rien ne permet de savoir.

## Scénario

```gherkin
Étant donné le formulaire de réservation
Quand le client consulte les conditions d'accès avant de valider
Alors un avertissement indique que l'accès est interdit aux enfants de moins de 4 ans
Quand il valide pour 2 adultes et 1 enfant
Alors la réservation est acceptée
```

## Résultat attendu

- L'avertissement est visible avant la validation, pas après.
- L'application ne peut pas refuser : elle ne collecte aucun âge, et le client a exclu tout champ supplémentaire.
- La règle relève de l'information donnée au client, pas d'un contrôle de saisie.

## Ce que ce cas ne vérifie pas

- Le tarif enfant, qui s'applique au nombre déclaré sans vérification d'âge → `CASE-BOOKING-27`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_22_avertissement_moins_de_4_ans_affiche_sans_controle` |
| Emplacement | `tests/` |
| Doublures | aucune |
