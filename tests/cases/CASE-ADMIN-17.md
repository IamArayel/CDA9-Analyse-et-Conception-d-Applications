# CASE-ADMIN-17 - un client absent au départ perd son acompte

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-8
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation de 100 €, acompte de 30 € versé, solde jamais réglé.
- Le client ne se présente pas au départ.

## Scénario

```gherkin
Étant donné une réservation dont 30 € ont été versés
Et un client absent au départ
Quand le gérant enregistre l'absence
Alors elle est traitée comme une annulation
Et l'acompte de 30 € est retenu
Et rien n'est réclamé au client
```

## Résultat attendu

- Aucune distinction entre l'absent et celui qui annule : le client l'a
  demandé explicitement.
- L'acompte est retenu en totalité.

## Ce que ce cas ne vérifie pas

- **Le taux applicable est une hypothèse d'équipe.** Le barème de `REQ-019`
  n'a aucune tranche en deçà de 24 heures, et la question ne se posait pas tant
  que tout était payé d'avance. Question 17 du §11 du cahier des charges.
- Le sort d'un client présent dont le solde n'est pas réglé → `CASE-ADMIN-18`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_17_client_absent_perd_son_acompte` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
