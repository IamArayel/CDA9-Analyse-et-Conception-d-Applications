# CASE-ADMIN-18 - le gérant pointe un solde encaissé au quai, sans qu'aucune transaction ne parte

**Spécification :** `SPEC-ADMIN-07`
**Critères couverts :** AC-1, AC-4, AC-6
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 7h, deux réservations confirmées de 100 € chacune.
- La première a été soldée en ligne, la seconde n'a versé que son acompte.

## Scénario

```gherkin
Étant donné un planning d'embarquement portant deux réservations
Alors la première y figure comme soldée
Et la seconde comme restant à encaisser
Quand le gérant encaisse 70 € au quai et pointe la seconde
Alors elle figure comme soldée
Et aucune transaction n'est demandée au prestataire de paiement
Quand il pointe la première, déjà soldée
Alors rien ne se passe, et aucune erreur ne lui est présentée
```

## Résultat attendu

- Le planning distingue les deux lignes avant le pointage, et plus après.
- **Zéro appel au prestataire** : l'outil enregistre un fait, il ne le provoque
  pas.
- Pointer une réservation déjà soldée est sans effet : le gérant ne peut pas
  savoir de tête qui a payé en ligne.

## Ce que ce cas ne vérifie pas

- Le montant réellement encaissé, que l'outil ne connaît pas et ne vérifie pas.
- La réversibilité du pointage → `CASE-ADMIN-19`.
- La mise en page du planning → `CASE-ADMIN-06`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_18_pointage_du_solde_sans_transaction` |
| Emplacement | `tests/Application/PointageDuSoldeTest.php` |
| Doublures | horloge, prestataire de paiement |
