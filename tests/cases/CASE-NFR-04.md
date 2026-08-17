# CASE-NFR-04 - les données sont purgées au terme du délai, sauf celles d'un bon cadeau vivant

**Spécification :** `SPEC-NFR-04`
**Critères couverts :** AC-3, AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation honorée le 20 juillet 2026.
- Un bon cadeau acheté le 20 juillet 2026, non consommé, valable jusqu'au 20 juillet 2027.

## Scénario

```gherkin
Étant donné une réservation dont la sortie a eu lieu le 20 juillet 2026
Quand nous sommes le 21 octobre 2026, soit trois mois plus tard
Alors ses données personnelles sont supprimées ou anonymisées
Étant donné un bon cadeau acheté le 20 juillet 2026 et non consommé
Quand nous sommes à la même date
Alors les données nécessaires à ce bon subsistent
Et elles ne disparaissent qu'après le 20 juillet 2027
```

## Résultat attendu

- La purge s'applique à trois mois de la sortie, valeur retenue par l'équipe faute de réponse client.
- Le bon cadeau échappe à cette purge tant qu'il est vivant : sans cette exception, un bon valable un an deviendrait inutilisable au bout de trois mois.
- C'est la contradiction relevée en revue du modèle de données.

## Ce que ce cas ne vérifie pas

- Le point de départ exact du délai, question 4 du §11 restée sans réponse.
- La demande de suppression émanant d'un client, traitée manuellement par le gérant.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_NFR_04_purge_a_trois_mois_sauf_bon_cadeau_vivant` |
| Emplacement | `tests/` |
| Doublures | horloge |
