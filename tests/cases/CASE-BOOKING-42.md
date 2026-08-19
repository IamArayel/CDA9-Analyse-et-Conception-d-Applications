# CASE-BOOKING-42 - un solde nul ne se règle pas, un créneau annulé ne le réclame plus

**Spécification :** `SPEC-BOOKING-12`
**Critères couverts :** AC-3, AC-6, AC-7
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Une réservation dauphins de 80 € réglée par un bon cadeau de 150 €.
- Une seconde réservation de 100 €, acompte de 30 € versé, sur un créneau que
  le gérant annule ensuite.

## Scénario

```gherkin
Étant donné une réservation soldée par un bon cadeau
Alors aucun solde ne reste dû
Et aucun règlement ne lui est proposé
Étant donné une seconde réservation dont 30 € ont été versés
Quand le gérant annule le créneau
Alors le solde n'est plus dû
Et les 30 € sont remboursés
Et aucune relance n'est envoyée à ce client
```

## Résultat attendu

- La première réservation est soldée dès sa confirmation, sans acompte ni solde.
- La seconde ne doit plus rien, et son acompte lui revient.
- Aucun message ne mentionne un solde, ni avant ni après l'annulation : le
  client l'a explicitement demandé.

## Ce que ce cas ne vérifie pas

- Le message d'annulation lui-même → `CASE-CANCEL-10`.
- Le surplus perdu d'un bon cadeau → `CASE-BOOKING-16`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_42_solde_nul_et_creneau_annule` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement, envoi de messages |
