# CASE-BOOKING-14 - l'achat d'un bon cadeau délivre un code unique valable un an

**Spécification :** `SPEC-BOOKING-09`
**Critères couverts :** AC-1, AC-7
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Aucun bon cadeau existant.
- Un acheteur choisit un montant de 150 €.

## Scénario

```gherkin
Étant donné un acheteur qui commande un bon cadeau de 150 €
Et que nous sommes le 20 juillet 2026
Quand son paiement de 150 € est confirmé
Alors un code unique lui est délivré
Et ce code porte un montant de 150 € et une expiration au 20 juillet 2027
Et aucun écran de l'achat ne lui a demandé un type de sortie
```

## Résultat attendu

- Le code existe, il est unique, il vaut 150 €.
- Sa date d'expiration est à un an jour pour jour de l'achat.
- Aucun rattachement à un type de sortie ni à une catégorie adulte ou enfant : c'est la règle inversée en v4.

## Ce que ce cas ne vérifie pas

- L'usage du code au paiement → `CASE-BOOKING-15` et `CASE-BOOKING-16`.
- Les bornes du montant à l'achat, question ouverte au §11 du cahier des charges.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_14_achat_bon_cadeau_delivre_un_code_unique_dun_an` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement, horloge |
