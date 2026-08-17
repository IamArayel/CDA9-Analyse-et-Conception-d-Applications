# CASE-CANCEL-15 - un créneau vide reste annulable, un créneau en alerte affiche sa date

**Spécification :** `SPEC-CANCEL-01`
**Critères couverts :** AC-3, AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau du 1er février à 10h, hors saison, aucun inscrit.
- Créneau du 20 juillet à 7h, mis en alerte le 19 juillet à 09h00.

## Scénario

```gherkin
Étant donné un créneau sans aucun inscrit
Quand le gérant le consulte
Alors la liste affichée est vide
Et le créneau reste annulable
Étant donné un créneau mis en alerte le 19 juillet à 09h00
Quand le gérant le consulte
Alors la date d'envoi de l'alerte lui est indiquée
```

## Résultat attendu

- Une liste vide est un résultat normal hors saison, pas une erreur.
- La date d'alerte est visible : c'est ce qui évite au gérant de poser une seconde alerte sur un créneau déjà alerté.

## Ce que ce cas ne vérifie pas

- La seconde alerte, sans effet → `CASE-CANCEL-01`.
- L'annulation d'un créneau sans inscrit, qui n'envoie aucun message → `CASE-CANCEL-16`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_15_creneau_vide_annulable_et_date_dalerte_affichee` |
| Emplacement | `tests/` |
| Doublures | horloge |
