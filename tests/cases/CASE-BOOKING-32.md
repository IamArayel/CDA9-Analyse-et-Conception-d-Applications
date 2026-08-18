# CASE-BOOKING-32 - le montant affiché est celui encaissé, malgré un changement de tarif

**Spécification :** `SPEC-BOOKING-06`, `SPEC-ADMIN-02`
**Critères couverts :** `SPEC-BOOKING-06` AC-3 et AC-4, `SPEC-ADMIN-02` AC-4
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Tarif adulte dauphins à 50 €.
- Un client valide une réservation pour 2 adultes, soit 100 €, sans payer immédiatement.

## Scénario

```gherkin
Étant donné un récapitulatif affichant 100 € pour 2 adultes
Quand le gérant porte le tarif adulte dauphins à 55 €
Et que le client paie ensuite
Alors le montant encaissé est 100 €
Et il est exprimé en euros, y compris pour un client ayant choisi l'anglais
```

## Résultat attendu

- Le montant est figé à la validation du formulaire, il ne suit pas la grille.
- Un client ne peut pas être débité d'un montant différent de celui qui lui a été présenté.
- Aucune conversion de devise : le tarif reste en euros quelle que soit la langue.

## Ce que ce cas ne vérifie pas

- L'application du nouveau tarif aux réservations suivantes → `CASE-ADMIN-04`.
- La traduction du parcours → cas de `SPEC-BOOKING-11`, à écrire.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_32_montant_affiche_egal_au_montant_encaisse` |
| Emplacement | `tests/Application/MontantFigeTest.php` |
| Doublures | prestataire de paiement, horloge |
