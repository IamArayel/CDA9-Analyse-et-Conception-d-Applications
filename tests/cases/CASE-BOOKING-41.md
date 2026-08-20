# CASE-BOOKING-41 - la fenêtre de paiement du solde s'ouvre avec le lien, pas 24 heures avant

**Spécification :** `SPEC-BOOKING-12`
**Critères couverts :** AC-1, AC-2
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-20.** `CR-07/Q12` déplace le repère : la fenêtre
> s'ouvre à l'envoi du lien, à 7h du matin la veille, et non 24 heures avant le
> départ. Le cas passe du créneau de 7h à celui de **14h**, seul endroit où les
> deux règles divergent, de sept heures.

## Préconditions

- Sortie dauphins du 20 juillet à **14h**, dont les réservations ferment le
  20 juillet à midi.
- Une réservation confirmée, acompte de 30 € versé, solde de 70 € restant dû.

## Scénario

```gherkin
Étant donné une réservation dont le solde de 70 € reste dû
Quand nous sommes le 19 juillet à 6h59
Alors le règlement du solde en ligne n'est pas proposé
Quand nous sommes le 19 juillet à 7h00
Alors il est proposé
Quand nous sommes le 20 juillet à 12h00
Alors il ne l'est plus
```

## Résultat attendu

- La fenêtre s'ouvre à 7h00 la veille, à la minute où le lien part.
- **Sous l'ancienne règle, elle ne se serait ouverte qu'à 14h00 la veille** :
  le client aurait reçu à 7h un lien inactif pendant sept heures.
- Elle se ferme à l'heure de fermeture des réservations du créneau, midi le
  jour même pour un départ de 14h.

## Ce que ce cas ne vérifie pas

- L'envoi du lien lui-même, qui n'est pas encore spécifié comme message.
- Le cas d'une réservation prise à moins de 24 heures, dont le lien part
  immédiatement (`CR-07/Q02`).
- L'heure de fermeture elle-même → `CASE-BOOKING-27`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_41_solde_non_reglable_hors_fenetre` |
| Emplacement | `tests/Application/SoldeDeLaReservationTest.php` |
| Doublures | horloge, prestataire de paiement |
