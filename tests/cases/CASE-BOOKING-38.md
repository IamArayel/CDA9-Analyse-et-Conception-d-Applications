# CASE-BOOKING-38 - l'acompte n'est pas arrondi à l'euro

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-9
**Type :** limite
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Une réservation baleines pour 1 adulte, soit 65 €.

## Scénario

```gherkin
Étant donné une réservation baleines de 65 € pour 1 adulte
Quand l'acompte de 30 % est calculé
Alors il vaut 19,50 €
Et non 19 € ni 20 €
```

## Résultat attendu

- L'acompte vaut exactement 19,50 €, arrondi au centime.
- Arrondir à l'euro ferait perdre ou gagner jusqu'à 50 centimes par réservation,
  ce que ni le client ni le passager n'accepteraient sur un relevé.

## Ce que ce cas ne vérifie pas

- Le troisième décimal, que la grille tarifaire actuelle ne peut pas produire :
  tous les tarifs sont des euros entiers, et 30 % d'un entier n'a qu'une
  décimale. Le cas deviendrait utile si le gérant saisissait un tarif au
  centime, ce que `SPEC-ADMIN-02` autorise.
- Le taux de 50 % d'une privatisation → `CASE-BOOKING-29`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_38_acompte_arrondi_au_centime` |
| Emplacement | `tests/Domaine/AcompteTest.php` |
| Doublures | aucune |
