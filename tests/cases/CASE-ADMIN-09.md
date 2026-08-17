# CASE-ADMIN-09 - fermer une date déjà réservée n'annule ni ne rembourse rien

**Spécification :** `SPEC-ADMIN-04`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Deux réservations payées sur le 15 août.
- Le gérant ajoute le 15 août aux jours de fermeture.

## Scénario

```gherkin
Étant donné deux réservations payées sur le 15 août
Quand le gérant ajoute le 15 août aux jours de fermeture
Alors l'ajout est accepté
Et les deux réservations concernées lui sont listées
Et aucune n'est annulée ni remboursée automatiquement
```

## Résultat attendu

- Les deux réservations existent toujours, à l'état confirmée.
- Aucun appel au prestataire de paiement.
- Le gérant est averti, à lui de traiter ces clients : c'est l'effet de bord relevé dans l'analyse d'impact, que le client n'avait pas envisagé.

## Ce que ce cas ne vérifie pas

- L'annulation d'un créneau pour raison météo, qui elle rembourse → `CASE-CANCEL-10`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_09_fermeture_date_reservee_naffecte_pas_les_reservations` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
