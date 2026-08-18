# CASE-BOOKING-29 - une privatisation bloque le bateau au forfait, l'autre reste réservable

**Spécification :** `SPEC-BOOKING-05`
**Critères couverts :** AC-1, AC-2, AC-3, AC-5
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Créneau du 20 juillet à 10h, Ti Kap et Le Grand Bleu libres.
- Forfait Ti Kap : 600 €.

## Scénario

```gherkin
Étant donné le Ti Kap libre sur le créneau du 20 juillet à 10h00
Quand un client privatise ce bateau pour 4 personnes
Et que son paiement est confirmé
Alors le montant facturé est 600 €
Et aucune place individuelle n'est proposée sur le Ti Kap à ce créneau
Et Le Grand Bleu reste réservable au même créneau
```

## Résultat attendu

- Le montant est le forfait du bateau, 600 €, indépendant des 4 participants.
- Les 12 places du Ti Kap sont bloquées, pas seulement 4.
- Une demande de place individuelle sur le Ti Kap à ce créneau est refusée.

## Ce que ce cas ne vérifie pas

- La privatisation d'une sortie baleines, qui consomme le naturaliste du créneau → `CASE-BOOKING-07`.
- Le seuil de 6 inscrits, qui ne s'applique pas à une privatisation → `CASE-BOOKING-06`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_29_privatisation_bloque_le_bateau_au_forfait` |
| Emplacement | `tests/Application/PrivatisationTest.php` |
| Doublures | prestataire de paiement, horloge |
