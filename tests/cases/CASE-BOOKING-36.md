# CASE-BOOKING-36 - le français s'applique par défaut et un changement de langue ne perd rien

**Spécification :** `SPEC-BOOKING-11`
**Critères couverts :** AC-3, AC-4
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Un client dont le navigateur est configuré en anglais.
- Un formulaire de réservation partiellement rempli.

## Scénario

```gherkin
Étant donné un client dont le navigateur est en anglais
Quand il arrive sur le site sans exprimer de choix de langue
Alors le parcours s'affiche en français
Quand il a rempli nom, prénom et e-mail puis bascule en anglais
Alors les trois champs saisis sont conservés
Et l'affichage passe en anglais
```

## Résultat attendu

- Aucune détection automatique : le français s'applique tant que le client n'a rien choisi, quelle que soit la configuration de son navigateur.
- Les données saisies survivent au changement de langue : sans cela, le client recommence son formulaire.

## Ce que ce cas ne vérifie pas

- La traduction elle-même → `CASE-BOOKING-35`.
- Une troisième langue, hors périmètre.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_36_francais_par_defaut_et_bascule_sans_perte` |
| Emplacement | `tests/Application/LangueDuParcoursTest.php` |
| Doublures | aucune |
