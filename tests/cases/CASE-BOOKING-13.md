# CASE-BOOKING-13 - un paiement abouti après expiration, sur une place vendue entre-temps, est remboursé

**Spécification :** `SPEC-BOOKING-07`
**Critères couverts :** AC-7
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Sortie dauphins du 20 juillet à 10h, 1 place restante.
- Un client A valide son formulaire à 14h00, immobilisation jusqu'à 14h15.
- Un client B réserve et paie la même place à 14h16.

## Scénario

```gherkin
Étant donné une immobilisation du client A expirée à 14h15
Et que le client B a payé la dernière place à 14h16
Quand le paiement du client A aboutit à 14h17
Alors sa réservation est refusée
Et son acompte lui est remboursé sans qu'il ait à le demander
Et la réservation du client B n'est pas affectée
```

## Résultat attendu

- Une seule réservation confirmée sur ce créneau, celle de B.
- Un remboursement **du seul acompte versé** est demandé pour A : le gérant ne
  rend jamais plus qu'il n'a encaissé.
- Le cas est rare mais il existe : l'immobilisation le réduit, elle ne le supprime pas.

## Ce que ce cas ne vérifie pas

- Le cas où la place est encore libre après expiration, qui aboutit à une confirmation.
- Le refus du second client avant paiement, cas nominal → `CASE-BOOKING-03`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_13_paiement_apres_expiration_place_vendue_rembourse` |
| Emplacement | `tests/Application/PaiementTest.php` |
| Doublures | prestataire de paiement, horloge |
