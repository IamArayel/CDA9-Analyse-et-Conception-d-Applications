# CASE-CANCEL-01 - une alerte couvre les deux bateaux d'un créneau et lui seul

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-1, AC-10
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Journée du 20 juillet, trois créneaux à 7h, 10h et 14h.
- Le créneau de 10h engage le Ti Kap et Le Grand Bleu.
- Des réservations confirmées existent sur les trois créneaux.

## Scénario

```gherkin
Étant donné la journée du 20 juillet avec ses trois créneaux
Quand le gérant met le créneau de 10h en alerte météo
Alors la sortie du Ti Kap de 10h est en alerte
Et la sortie du Grand Bleu de 10h est en alerte
Et les créneaux de 7h et de 14h restent programmés
```

## Résultat attendu

- Les deux sorties du créneau de 10h portent l'état « en alerte » et la même date de mise en alerte.
- Aucune des sorties des autres créneaux n'a changé d'état.

## Ce que ce cas ne vérifie pas

- Les messages envoyés aux clients → `CASE-CANCEL-03`.
- L'annulation qui peut suivre → `CASE-CANCEL-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_01_alerte_couvre_les_deux_bateaux_du_creneau` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages |
