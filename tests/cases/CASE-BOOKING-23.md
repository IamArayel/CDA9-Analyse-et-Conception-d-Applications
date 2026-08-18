# CASE-BOOKING-23 - les coordonnées sont contrôlées et le numéro normalisé

**Spécification :** `SPEC-BOOKING-01`
**Critères couverts :** AC-4, AC-7, AC-8
**Type :** erreur
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie dauphins du 20 juillet à 10h.
- Trois tentatives de saisie, l'une valide, deux invalides.

## Scénario

```gherkin
Étant donné le formulaire de réservation
Quand le client saisit une adresse « jean.dupont-arobase-exemple » sans arobase
Alors la réservation est refusée et le champ e-mail est nommé
Quand il saisit un numéro fixe au lieu d'un mobile
Alors la réservation est refusée et le champ téléphone est nommé
Quand il saisit un mobile écrit « 06 12-34.56 78 »
Alors la réservation est acceptée
Et le numéro est enregistré sans point, tiret ni espace
```

## Résultat attendu

- Chaque refus nomme le champ en cause, pas un message générique.
- Le numéro accepté est stocké normalisé : c'est ce contrôle qui rend tenable la position du client, pour qui un message non délivré relève de celui qui a mal saisi ses coordonnées.

## Ce que ce cas ne vérifie pas

- L'envoi réel d'un SMS à ce numéro → cas de `SPEC-CANCEL-05`, à écrire.
- Le consentement à recevoir des SMS, question ouverte au §11.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_23_coordonnees_controlees_et_numero_normalise` |
| Emplacement | `tests/Application/FormulaireDeReservationTest.php` |
| Doublures | aucune |
