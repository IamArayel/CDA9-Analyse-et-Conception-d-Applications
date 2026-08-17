# CASE-BOOKING-35 - une sortie annulée au seuil n'est pas rétablie par de nouvelles inscriptions

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- Le contrôle du seuil s'est exécuté le 19 juillet à 10h00 avec 5 inscrits :
  la sortie est à l'état « annulée » et les 3 clients ont été remboursés.
- Deux participants supplémentaires se présentent après ce contrôle.

## Scénario

```gherkin
Étant donné une sortie du 20 juillet à 10h00 à l'état « annulée » depuis le contrôle du seuil
Et que nous sommes le 19 juillet à 15h00
Quand 2 participants supplémentaires sont enregistrés sur ce créneau
Alors la sortie reste à l'état « annulée »
Et aucun nouveau remboursement n'est demandé
```

## Résultat attendu

- La sortie est toujours à l'état « annulée », alors que le total atteindrait 7.
- Le contrôle du seuil n'est pas rejoué : il ne s'exécute qu'une fois, 24 heures
  avant le départ.
- 0 nouveau remboursement demandé au prestataire, 0 € mouvementé.
- Franchir 6 après coup est sans effet : ni 7, ni 12 inscrits ne rétablissent
  la sortie.

## Ce que ce cas ne vérifie pas

- L'annulation elle-même, à 5 inscrits → `CASE-BOOKING-05`.
- Le maintien à exactement 6 inscrits au contrôle → `CASE-BOOKING-06`.
- **Si la nouvelle réservation est elle-même acceptée sur un créneau annulé.**
  La spécification ne le dit pas ; ce cas n'observe que l'état de la sortie.
  Question à poser au client, ligne proposée pour `docs/traceability-trous.md`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_35_sortie_annulee_non_retablie_par_de_nouvelles_inscriptions` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
