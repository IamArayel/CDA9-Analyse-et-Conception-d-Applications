# CASE-BOOKING-07 - une seconde sortie baleines sur le même créneau est refusée

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-6
**Type :** erreur
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Créneau du 20 juillet à 10h, en saison baleines.
- Une sortie baleines est déjà programmée sur le Ti Kap.
- Le Grand Bleu est libre sur ce créneau.

## Scénario

```gherkin
Étant donné une sortie baleines déjà programmée sur le Ti Kap le 20 juillet à 10h00
Quand un client demande une sortie baleines sur Le Grand Bleu au même créneau
Alors la demande est refusée
Et le motif est l'indisponibilité du naturaliste
```

## Résultat attendu

- Une seule sortie baleines existe sur ce créneau.
- Une sortie dauphins sur Le Grand Bleu au même créneau resterait acceptée.

## Ce que ce cas ne vérifie pas

- La saison des sorties baleines → cas de `SPEC-BOOKING-02`, à écrire.
- La répartition des passagers entre bateaux, hors périmètre.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_07_seconde_sortie_baleines_refusee_sur_le_creneau` |
| Emplacement | `tests/Application/CapaciteEtPlacesDisponiblesTest.php` |
| Doublures | horloge |
