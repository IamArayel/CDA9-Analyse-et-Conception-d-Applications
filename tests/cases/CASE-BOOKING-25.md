# CASE-BOOKING-25 - un jour de fermeture ne propose aucun créneau

**Spécification :** `SPEC-BOOKING-02`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Jours de fermeture par défaut : 25 décembre et 1er janvier.
- Aucun jour de fermeture supplémentaire.

## Scénario

```gherkin
Étant donné les jours de fermeture par défaut
Quand le client consulte le 25 décembre
Alors aucun créneau ne lui est proposé
Quand il consulte le 1er janvier
Alors aucun créneau ne lui est proposé
Quand il consulte le 26 décembre
Alors les trois créneaux sont proposés, en sortie dauphins
```

## Résultat attendu

- Aucun des trois créneaux n'existe sur un jour fermé, pas même en affichage grisé.
- Le 26 décembre est un jour ouvert ordinaire : la fermeture ne déborde pas.

## Ce que ce cas ne vérifie pas

- L'ajout d'un jour de fermeture par le gérant → cas de `SPEC-ADMIN-04`, à écrire.
- Le sort d'une réservation déjà payée sur une date qui devient fermée, traité manuellement par le gérant.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_25_jour_de_fermeture_aucun_creneau` |
| Emplacement | `tests/` |
| Doublures | horloge |
