# CASE-CANCEL-10 - chaque client d'un créneau annulé est prévenu par écrit et remboursé de ce qu'il a versé

**Spécification :** `SPEC-CANCEL-04`
**Critères couverts :** AC-1, AC-3, AC-4
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Créneau du 20 juillet à 10h, annulé par le gérant.
- Trois réservations confirmées, d'un montant de 100 €, 160 € et 260 €, dont
  seuls les acomptes de 30 €, 48 € et 78 € ont été versés.

## Scénario

```gherkin
Étant donné un créneau annulé portant trois réservations payées
Quand l'annulation est enregistrée
Alors chacun des trois clients reçoit un message écrit annonçant l'annulation
Et trois remboursements de 30 €, 48 € et 78 € sont demandés
Et le gérant n'a passé aucun appel téléphonique
```

## Résultat attendu

- Trois remboursements, chacun égal à **l'acompte versé**, sans retenue.
- Six envois, trois clients sur deux canaux.
- Aucune action téléphonique n'est nécessaire pour déclencher l'un ou l'autre.

## Ce que ce cas ne vérifie pas

- Le calendrier d'envoi du message, 2 heures avant le départ → `CASE-CANCEL-05`.
- L'annulation faute de 6 inscrits → `CASE-BOOKING-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_10_annulation_previent_par_ecrit_et_rembourse_en_totalite` |
| Emplacement | `tests/Application/AnnulationEtRemboursementTest.php` |
| Doublures | envoi de messages, prestataire de paiement, horloge |
