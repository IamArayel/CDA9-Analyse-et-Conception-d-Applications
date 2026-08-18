# CASE-ADMIN-13 - l'enregistrement d'un avoir produit un code valable un an

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Une réservation payée 170 €, pour une sortie dans 5 jours.
- Le client a appelé le gérant pour annuler ; ils conviennent d'un avoir de 170 €.

## Scénario

```gherkin
Étant donné une réservation payée 170 € et une annulation demandée par le client
Et que nous sommes le 20 juillet 2026
Quand le gérant enregistre l'issue « avoir » avec un montant de 170 €
Alors un code d'avoir unique est produit
Et il porte un montant de 170 € et une expiration au 20 juillet 2027
```

## Résultat attendu

- Le code existe et vaut le montant saisi par le gérant, pas un montant calculé.
- C'est la seule origine d'un avoir depuis la correction du 2026-08-14.

## Ce que ce cas ne vérifie pas

- L'usage du code au paiement → `CASE-BOOKING-33`.
- Le barème dégressif appliqué au montant, laissé à l'appréciation du gérant.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_13_enregistrement_avoir_produit_un_code_dun_an` |
| Emplacement | `tests/Application/IssueDannulationClientTest.php` |
| Doublures | horloge |
