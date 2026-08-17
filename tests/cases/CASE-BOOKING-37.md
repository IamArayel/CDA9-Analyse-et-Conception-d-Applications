# CASE-BOOKING-37 - une privatisation à moins de 6 participants est maintenue

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-5
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 14h sur le Ti Kap.
- Le créneau est privatisé, payé 600 €, pour 4 participants déclarés.
- Aucune autre réservation sur ce créneau : la privatisation bloque le bateau
  entier.

## Scénario

```gherkin
Étant donné une sortie privatisée du 20 juillet à 14h00 comptant 4 participants
Et que nous sommes le 19 juillet à 14h00
Quand le contrôle du seuil de maintien s'exécute
Alors la sortie reste à l'état « programmée »
Et aucun remboursement n'est demandé
```

## Résultat attendu

- La sortie est maintenue avec 4 participants : le seuil de 6 ne s'applique pas
  à une privatisation, le bateau étant payé en entier.
- 0 remboursement demandé au prestataire, les 600 € restent acquis.
- Aucun appel au prestataire de paiement.

## Ce que ce cas ne vérifie pas

- Le même effectif en réservations standard, qui annule la sortie →
  `CASE-BOOKING-05`.
- Le maintien à exactement 6 inscrits hors privatisation → `CASE-BOOKING-06`.
- Le forfait de privatisation et le blocage du bateau → `CASE-BOOKING-29`.
- **Le seuil de 6 appliqué à une privatisation est une hypothèse d'équipe**,
  jamais soumise au client. Ce cas fige l'hypothèse, il ne la démontre pas.
  Ligne proposée pour `docs/traceability-trous.md`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_37_privatisation_non_soumise_au_seuil_de_maintien` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
