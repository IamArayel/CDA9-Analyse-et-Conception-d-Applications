# CASE-ADMIN-06 - l'export du planning produit un PDF groupé par créneau

**Spécification :** `SPEC-ADMIN-03`
**Critères couverts :** AC-1, AC-2, AC-4
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Journée du 20 juillet.
- Trois réservations payées, réparties sur les créneaux de 7h et 10h.
- Une réservation en attente de paiement sur le créneau de 14h.

## Scénario

```gherkin
Étant donné la journée du 20 juillet
Quand le gérant demande l'export du planning de cette journée
Alors un document PDF imprimable est produit
Et les réservations y sont regroupées par créneau, dans l'ordre chronologique
Et la réservation en attente de paiement n'y figure pas
```

## Résultat attendu

- Le document contient les trois réservations payées, sous les créneaux de 7h puis de 10h.
- La réservation non payée est absente : le planning liste ce qui embarque, pas ce qui est en cours d'achat.

## Ce que ce cas ne vérifie pas

- La liste des inscrits consultée avant une annulation, qui est un écran et non un export → cas de `SPEC-CANCEL-01`, à écrire.
- Le choix de la période exportée, hypothèse d'équipe.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_06_export_planning_pdf_groupe_par_creneau` |
| Emplacement | `tests/Application/ExportDuPlanningTest.php` |
| Doublures | horloge |
