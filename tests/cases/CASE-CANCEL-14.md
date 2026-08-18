# CASE-CANCEL-14 - la consultation d'un créneau affiche ses inscrits sans rien déclencher

**Spécification :** `SPEC-CANCEL-01`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Créneau du 20 juillet à 10h, à venir.
- Trois réservations payées, une quatrième en attente de paiement.

## Scénario

```gherkin
Étant donné un créneau à venir portant trois réservations payées
Quand le gérant consulte ce créneau depuis l'espace de gestion
Alors la liste des trois clients s'affiche avec nom, contact et nombre de participants
Et la réservation en attente de paiement n'y figure pas
Et le créneau reste à l'état « programmée »
```

## Résultat attendu

- Trois lignes affichées, pas quatre : le client qui n'a pas payé n'est pas un inscrit.
- Aucune alerte, aucune annulation, aucun message : la consultation est sans effet de bord.

## Ce que ce cas ne vérifie pas

- La décision d'annuler qui peut suivre → `CASE-CANCEL-16`.
- L'export imprimable du planning, qui est un document et non un écran → `CASE-ADMIN-06`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_14_consultation_affiche_les_inscrits_sans_effet_de_bord` |
| Emplacement | `tests/Application/ConsultationDunCreneauTest.php` |
| Doublures | horloge |
