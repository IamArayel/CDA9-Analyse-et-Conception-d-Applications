# CASE-BOOKING-34 - un code d'avoir déjà utilisé ou expiré est refusé

**Spécification :** `SPEC-BOOKING-10`
**Critères couverts :** AC-3, AC-6
**Type :** erreur
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Un code d'avoir déjà consommé.
- Un second code d'avoir émis le 20 juillet 2026, non utilisé.

## Scénario

```gherkin
Étant donné un code d'avoir déjà utilisé
Quand il est saisi sur une nouvelle réservation
Alors il est refusé
Étant donné un code d'avoir émis le 20 juillet 2026
Quand il est saisi le 21 juillet 2027
Alors il est refusé car sa validité d'un an est dépassée
```

## Résultat attendu

- Aucune déduction dans les deux cas.
- La validité d'un an de l'avoir vient de `CR-04/Q04` : elle infirme l'hypothèse d'équipe initiale, qui ne prévoyait aucune expiration.

## Ce que ce cas ne vérifie pas

- Le non-cumul avec un bon cadeau → `CASE-BOOKING-19`.
- L'information du client avant expiration, écartée faute de demande.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_34_avoir_utilise_ou_expire_refuse` |
| Emplacement | `tests/Domaine/ValiditeDunAvoirTest.php` |
| Doublures | horloge |
