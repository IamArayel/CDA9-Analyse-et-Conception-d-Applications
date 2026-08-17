# CASE-ADMIN-08 - les deux jours de fermeture sont présents par défaut et modifiables

**Spécification :** `SPEC-ADMIN-04`
**Critères couverts :** AC-1, AC-2, AC-3
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Première ouverture de la section des horaires.
- Aucune réservation sur les dates concernées.

## Scénario

```gherkin
Étant donné la section des horaires à sa première ouverture
Alors le 25 décembre et le 1er janvier y figurent comme jours de fermeture
Quand le gérant ajoute le 15 août aux jours de fermeture
Alors aucun créneau n'est proposé le 15 août
Quand il retire le 25 décembre de la liste
Alors les trois créneaux sont de nouveau proposés le 25 décembre
```

## Résultat attendu

- Les deux dates sont présentes sans que personne les ait saisies.
- L'ajout et le retrait prennent effet le jour même de l'enregistrement, sans intervention technique.

## Ce que ce cas ne vérifie pas

- L'effet sur les créneaux vu côté client → `CASE-BOOKING-25`.
- La récurrence annuelle d'une date ajoutée, hypothèse d'équipe.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_08_jours_de_fermeture_par_defaut_et_modifiables` |
| Emplacement | `tests/` |
| Doublures | horloge |
