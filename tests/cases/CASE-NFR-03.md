# CASE-NFR-03 - seules les données du formulaire sont stockées, aucune donnée de carte

**Spécification :** `SPEC-NFR-04`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation confirmée après un paiement par carte.

## Scénario

```gherkin
Étant donné une réservation confirmée et payée par carte
Quand les données conservées pour cette réservation sont inspectées
Alors on y trouve nom, prénom, e-mail, mobile, nombre d'adultes, nombre d'enfants, créneau et type de sortie
Et aucun autre champ concernant le client
Et aucun numéro de carte, aucune date d'expiration, aucun cryptogramme
```

## Résultat attendu

- La liste des champs stockés est exactement celle du formulaire, ni plus ni moins.
- Aucune donnée bancaire nulle part, y compris dans les journaux techniques : elles ne transitent pas par l'application.

## Ce que ce cas ne vérifie pas

- La conformité du prestataire de paiement, qui ne dépend pas de nous.
- La durée de conservation → `CASE-NFR-04`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_NFR_03_donnees_minimales_et_aucune_donnee_de_carte` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement |
