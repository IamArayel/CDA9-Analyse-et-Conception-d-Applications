# CASE-BOOKING-19 - un bon cadeau et un code d'avoir ne se cumulent pas

**Spécification :** `SPEC-BOOKING-09`, `SPEC-BOOKING-10`
**Critères couverts :** `SPEC-BOOKING-09` AC-8, `SPEC-BOOKING-10` AC-5
**Type :** erreur
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Une réservation de 170 €.
- Un bon cadeau valide de 100 € et un code d'avoir valide de 50 €.

## Scénario

```gherkin
Étant donné une réservation de 170 €
Quand le client saisit le code du bon cadeau de 100 €
Alors 100 € sont déduits
Quand il saisit ensuite le code d'avoir de 50 €
Alors le second code est refusé
Et le montant restant dû reste de 70 €
```

## Résultat attendu

- Un seul dispositif est appliqué, le premier saisi.
- Le second code n'est pas consommé : il reste utilisable sur une autre réservation.
- Le non-cumul est porté par une contrainte de la base, pas seulement par le code applicatif.

## Ce que ce cas ne vérifie pas

- L'ordre inverse, avoir puis bon cadeau, qui doit produire le même refus.
- L'origine d'un avoir → cas de `SPEC-ADMIN-06`, à écrire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_19_non_cumul_bon_cadeau_et_avoir` |
| Emplacement | `tests/Application/BonCadeauTest.php` |
| Doublures | horloge |
