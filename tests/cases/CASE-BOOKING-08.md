# CASE-BOOKING-08 - les places affichées diminuent après le paiement d'un autre client

**Spécification :** `SPEC-BOOKING-03`
**Critères couverts :** AC-7
**Type :** nominal
**Niveau :** bout en bout
**Statut :** à automatiser

## Préconditions

- Sortie dauphins du 20 juillet à 10h sur le Ti Kap, 5 places restantes.
- Un client A consulte la page du créneau.
- Un client B est en cours de paiement pour 2 places.

## Scénario

```gherkin
Étant donné un client A affichant un créneau à 5 places restantes
Quand le paiement du client B pour 2 places est confirmé
Alors l'affichage du client A indique 3 places restantes
Et il n'a pas rechargé la page
```

## Résultat attendu

- Le nombre affiché passe de 5 à 3 sans action du client A.
- Le décompte intervient à la confirmation du paiement, pas avant.

## Ce que ce cas ne vérifie pas

- Le comportement d'un créneau annulé pendant la consultation → `SPEC-CANCEL-03`.
- Le délai exact de propagation, non contractuel.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_08_places_affichees_diminuent_apres_paiement` |
| Emplacement | `tests/` |
| Doublures | prestataire de paiement en mode test |
