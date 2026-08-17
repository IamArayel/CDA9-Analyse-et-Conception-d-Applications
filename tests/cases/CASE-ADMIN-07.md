# CASE-ADMIN-07 - une période sans réservation produit un document lisible

**Spécification :** `SPEC-ADMIN-03`
**Critères couverts :** AC-3
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Journée du 1er février, hors saison, aucune réservation.

## Scénario

```gherkin
Étant donné une journée sans aucune réservation
Quand le gérant en demande l'export
Alors un document PDF est produit
Et il indique explicitement l'absence de réservation
Et aucune erreur n'est affichée
```

## Résultat attendu

- Le document existe et s'imprime.
- L'absence de réservation est un résultat métier normal hors saison, pas une erreur : le gérant doit pouvoir imprimer une journée vide sans se demander si l'outil a échoué.

## Ce que ce cas ne vérifie pas

- Le contenu exact des colonnes, hypothèse d'équipe.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_07_export_periode_sans_reservation_document_lisible` |
| Emplacement | `tests/` |
| Doublures | horloge |
