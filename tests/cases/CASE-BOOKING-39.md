# CASE-BOOKING-39 - six acomptes suffisent à maintenir une sortie

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-10
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap.
- Six clients ont versé leur acompte, aucun n'a réglé son solde.

## Scénario

```gherkin
Étant donné une sortie comptant 6 inscrits n'ayant versé que leur acompte
Et que nous sommes le 19 juillet à 10h00
Quand le contrôle du seuil de maintien s'exécute
Alors la sortie reste à l'état « programmée »
Et aucun remboursement n'est demandé
```

## Résultat attendu

- Les six réservations comptent dans le seuil, bien qu'aucune ne soit soldée.
- Les six places sont décomptées de la capacité du créneau.

## Ce que ce cas ne vérifie pas

- Le seuil lui-même, vérifié sans base → `CASE-BOOKING-06`.
- **L'effet économique**, qui n'est pas un comportement logiciel : le gérant
  maintient une sortie dont il n'a encaissé que 30 % de six clients, soit 18 %
  du chiffre attendu. Il ne l'a pas envisagé, et la question lui est posée au
  §8 de `CR-06`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_39_six_acomptes_maintiennent_la_sortie` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
