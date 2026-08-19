# CASE-BOOKING-41 - hors de sa fenêtre, le solde n'est pas réglable en ligne

**Spécification :** `SPEC-BOOKING-12`
**Critères couverts :** AC-2
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie dauphins du 20 juillet à 7h, dont les réservations ferment le
  19 juillet à midi.
- Une réservation confirmée, acompte versé, solde de 70 € restant dû.

## Scénario

```gherkin
Étant donné une réservation dont le solde de 70 € reste dû
Quand nous sommes le 18 juillet à 9h00
Alors le règlement du solde en ligne n'est pas proposé
Quand nous sommes le 19 juillet à 7h00
Alors il est proposé
Quand nous sommes le 19 juillet à 12h00
Alors il ne l'est plus
```

## Résultat attendu

- La fenêtre s'ouvre 24 heures avant le départ et se ferme à l'heure de
  fermeture des réservations du créneau, soit de 7h00 à 12h00 la veille.
- Passé cette heure, seul le paiement au quai reste possible.

## Ce que ce cas ne vérifie pas

- **La justesse de ces bornes**, qui est une déduction d'équipe et non une
  réponse du client. Question 16 du §11 du cahier des charges.
- Le refus d'un règlement anticipé ne repose que sur la lettre de `REQ-111` :
  rien n'interdirait de l'accepter.
- L'heure de fermeture elle-même → `CASE-BOOKING-27`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_41_solde_non_reglable_hors_fenetre` |
| Emplacement | `tests/Application/SoldeDeLaReservationTest.php` |
| Doublures | horloge |
