# CASE-ADMIN-10 - un bateau créé apparaît côté client avec sa capacité

**Spécification :** `SPEC-ADMIN-05`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Flotte de deux bateaux.
- Le gérant crée « Le Petit Bleu », 8 places.

## Scénario

```gherkin
Étant donné une flotte de deux bateaux
Quand le gérant crée un bateau nommé « Le Petit Bleu » avec 8 places
Alors ce bateau apparaît dans les créneaux proposés côté client
Et le nombre de places affiché pour ce bateau est 8
Et il est proposé pour les sorties dauphins comme pour les sorties baleines
```

## Résultat attendu

- Le bateau est visible sans intervention technique ni redéploiement.
- Sa capacité affichée est exactement celle saisie.
- Il est habilité à tous les types de sortie : hypothèse d'équipe, faute d'information même pour les deux bateaux existants.

## Ce que ce cas ne vérifie pas

- Le forfait de privatisation, absent à la création → `CASE-ADMIN-11`.
- La règle du naturaliste unique, qui s'applique à lui comme aux autres → `CASE-BOOKING-07`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_10_bateau_cree_apparait_avec_sa_capacite` |
| Emplacement | `tests/` |
| Doublures | horloge |
