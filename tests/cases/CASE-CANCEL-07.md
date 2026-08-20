# CASE-CANCEL-07 - un client inscrit après l'alerte reçoit la confirmation d'annulation

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-7
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Sortie du 20 juillet à 14h, mise en alerte le 19 juillet, message parti à 18h00.
- Un client réserve et paie le 20 juillet à 11h00, après l'envoi de l'alerte.

## Scénario

```gherkin
Étant donné un client ayant réservé le 20 juillet à 11h00 sur un créneau déjà en alerte
Quand le gérant annule ce créneau le 20 juillet à 11h30
Alors ce client reçoit le message de confirmation d'annulation à 12h00
Et il est remboursé de la totalité de ce qu'il a versé
```

## Résultat attendu

- Le client reçoit la confirmation bien qu'il n'ait jamais reçu l'alerte de la veille.
- Le destinataire est déterminé au moment de l'annulation, pas au moment de l'alerte.

## Ce que ce cas ne vérifie pas

- Le signalement du risque avant sa réservation → `CASE-CANCEL-06`.
- Le droit au remboursement d'un client qui renonce de lui-même, couvert par `SPEC-ADMIN-06`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_07_client_inscrit_apres_alerte_recoit_la_confirmation` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge, envoi de messages, prestataire de paiement |
