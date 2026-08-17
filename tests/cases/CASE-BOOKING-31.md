# CASE-BOOKING-31 - le montant suit la grille du type de sortie

**Spécification :** `SPEC-BOOKING-06`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** domaine
**Statut :** à automatiser

## Préconditions

- Tarifs de référence : baleines 65 € et 40 €, dauphins 50 € et 30 €.
- Une composition identique dans les deux cas, 2 adultes et 1 enfant.

## Scénario

```gherkin
Étant donné une réservation baleines pour 2 adultes et 1 enfant
Quand le montant est calculé
Alors il est de 170 €
Étant donné la même composition en sortie dauphins
Quand le montant est calculé
Alors il est de 130 €
```

## Résultat attendu

- 2 x 65 + 1 x 40 = 170 €, et 2 x 50 + 1 x 30 = 130 €.
- La répartition adulte et enfant est déclarative : aucun âge n'est collecté, donc rien ne la vérifie.

## Ce que ce cas ne vérifie pas

- Le forfait de privatisation, qui ne se calcule pas par personne → `CASE-BOOKING-29`.
- La déduction d'un code → `CASE-BOOKING-15`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_31_montant_selon_la_grille_du_type_de_sortie` |
| Emplacement | `tests/` |
| Doublures | aucune |
