# CASE-ADMIN-06 - l'export du planning produit un PDF groupé par créneau

**Spécification :** `SPEC-ADMIN-03`
**Critères couverts :** AC-1, AC-2, AC-4, AC-5
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

> **Étendu en v6, 2026-08-19.** `CR-06` ajoute un critère à la spécification
> que ce cas couvrait déjà, et le comportement décrit ici le vérifie sans
> changer.

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

- Chaque ligne indique si le solde de la réservation est réglé ou reste dû,
  seule information dont le gérant a besoin au quai et qui n'existait pas.
## Ce que ce cas ne vérifie pas

- La liste des inscrits consultée avant une annulation, qui est un écran et non un export → cas de `SPEC-CANCEL-01`, à écrire.
- Le choix de la période exportée, hypothèse d'équipe.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_06_export_planning_pdf_groupe_par_creneau` |
| Emplacement | `tests/Application/ExportDuPlanningTest.php` |
| Doublures | horloge |
