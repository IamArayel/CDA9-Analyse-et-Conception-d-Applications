# CASE-BOOKING-35 - le parcours complet s'affiche en anglais quand l'anglais est choisi

**Spécification :** `SPEC-BOOKING-11`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** bout en bout
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h, places disponibles.
- Un client choisit l'anglais dès la page d'accueil.

## Scénario

```gherkin
Étant donné un client qui choisit la langue anglaise
Quand il consulte les places disponibles, remplit le formulaire et atteint le paiement
Alors chaque écran du parcours s'affiche en anglais
Et aucun libellé, aucun message d'erreur, aucun bouton ne reste en français
```

## Résultat attendu

- Les trois étapes du parcours sont en anglais, du premier écran au paiement.
- Les messages de validation du formulaire sont traduits eux aussi : c'est là que les oublis se logent.
- Le montant reste exprimé en euros.

## Ce que ce cas ne vérifie pas

- La langue des messages automatiques envoyés après la réservation → `CASE-NFR-01`.
- La traduction du reste du site hors parcours → `CASE-NFR-01`.
- La langue de l'espace de gestion, qui reste en français.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_35_parcours_complet_en_anglais` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement en mode test |
