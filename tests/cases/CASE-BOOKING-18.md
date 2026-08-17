# CASE-BOOKING-18 - un bon cadeau est accepté le jour anniversaire et refusé le lendemain

**Spécification :** `SPEC-BOOKING-09`
**Critères couverts :** AC-6
**Type :** limite
**Niveau :** domaine
**Statut :** à automatiser

## Préconditions

- Un bon cadeau de 100 € acheté le 20 juillet 2026, jamais utilisé.
- Une réservation de 130 €.

## Scénario

```gherkin
Étant donné un bon cadeau acheté le 20 juillet 2026
Quand son code est saisi le 20 juillet 2027 à 23h00
Alors il est accepté
Quand son code est saisi le 21 juillet 2027 à 00h01
Alors il est refusé
```

## Résultat attendu

- La validité court jusqu'à la fin du jour anniversaire, bornes incluses.
- C'est une hypothèse d'équipe : le client a dit « un an » sans préciser si le jour anniversaire compte.

## Ce que ce cas ne vérifie pas

- Le code déjà utilisé → `CASE-BOOKING-17`.
- L'information du bénéficiaire avant expiration, écartée faute de demande client.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_18_bon_cadeau_expire_le_lendemain_de_lanniversaire` |
| Emplacement | `tests/` |
| Doublures | horloge |
