# CASE-ADMIN-14 - les issues report et remboursement ne produisent aucun code

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-3, AC-5
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Deux réservations payées, chacune annulée par son client.
- Une troisième réservation déjà annulée.

## Scénario

```gherkin
Étant donné une annulation demandée par le client
Quand le gérant enregistre l'issue « report »
Alors aucun code n'est produit
Quand il enregistre l'issue « remboursement » sur une autre réservation
Alors aucun code n'est produit
Quand il tente d'enregistrer une seconde issue sur une réservation déjà annulée
Alors l'enregistrement est refusé
```

## Résultat attendu

- Aucun code d'avoir n'existe pour les deux premières issues.
- Une réservation ne porte qu'une issue : on ne rejoue pas une annulation.

## Ce que ce cas ne vérifie pas

- Le report vers un créneau complet, refusé au titre de la capacité.
- L'avoir, seule issue qui produit un code → `CASE-ADMIN-13`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_14_report_et_remboursement_ne_produisent_aucun_code` |
| Emplacement | `tests/Application/IssueDannulationClientTest.php` |
| Doublures | horloge |
