# CASE-BOOKING-24 - la saison des baleines s'ouvre et se ferme aux bornes incluses

**Spécification :** `SPEC-BOOKING-02`
**Critères couverts :** AC-1, AC-2, AC-3
**Type :** limite
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Aucun jour de fermeture sur les dates testées.
- Les trois créneaux sont ouverts.

## Scénario

```gherkin
Étant donné le calendrier des sorties
Quand le client consulte le 14 juin
Alors seule la sortie dauphins est proposée, sur les trois créneaux
Quand il consulte le 15 juin
Alors dauphins et baleines sont proposées
Quand il consulte le 31 octobre
Alors dauphins et baleines sont proposées
Quand il consulte le 1er novembre
Alors seule la sortie dauphins est proposée
```

## Résultat attendu

- Les deux bornes, 15 juin et 31 octobre, sont incluses.
- Les trois créneaux de 7h, 10h et 14h sont proposés tous les jours d'ouverture, quelle que soit la saison.

## Ce que ce cas ne vérifie pas

- L'affectation d'un bateau à la sortie, et la règle du naturaliste unique → `CASE-BOOKING-07`.
- Les jours de fermeture → `CASE-BOOKING-25`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_24_saison_baleines_bornes_incluses` |
| Emplacement | `tests/Domaine/OffreDeCreneauxTest.php` |
| Doublures | horloge |
