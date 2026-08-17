# CASE-BOOKING-36 - une sortie baleines et une sortie dauphins au même créneau sont acceptées

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-6
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 20 juillet à 10h, en saison baleines.
- Aucune sortie n'est encore programmée sur ce créneau.
- Ti Kap, 12 places, et Le Grand Bleu, 24 places, tous deux libres.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 10h00 sans aucune sortie programmée
Quand une sortie baleines est programmée sur le Ti Kap
Et qu'une sortie dauphins est programmée sur Le Grand Bleu au même créneau
Alors les deux sorties sont acceptées
Et le créneau compte 2 sorties et 36 places offertes
```

## Résultat attendu

- 2 sorties existent sur le créneau du 20 juillet à 10h00.
- 12 + 24 = 36 places sont offertes à la vente sur ce créneau.
- La contrainte du naturaliste unique n'est pas déclenchée : elle porte sur une
  seconde sortie **baleines**, pas sur la présence de deux bateaux en mer.

## Ce que ce cas ne vérifie pas

- Le refus d'une seconde sortie baleines sur le même créneau → `CASE-BOOKING-07`.
- La saison des sorties baleines et ses bornes → `CASE-BOOKING-24`.
- La répartition des passagers entre bateaux, hors périmètre.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_36_baleines_et_dauphins_acceptees_sur_le_meme_creneau` |
| Emplacement | `tests/` |
| Doublures | horloge |
