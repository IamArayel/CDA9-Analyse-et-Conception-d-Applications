# CASE-BOOKING-33 - un code d'avoir se déduit du montant, quel que soit le type de sortie

**Spécification :** `SPEC-BOOKING-10`
**Critères couverts :** AC-1, AC-2, AC-4, AC-7
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

> **Étendu en v6, 2026-08-19.** `CR-06` ajoute un critère à la spécification
> que ce cas couvrait déjà, et le comportement décrit ici le vérifie sans
> changer.

## Préconditions

- Un code d'avoir valide de 130 €, émis il y a un mois après une annulation demandée par le client.
- Une réservation baleines de 170 €.

## Scénario

```gherkin
Étant donné un code d'avoir de 130 € et une réservation baleines de 170 €
Quand le client saisit le code au moment de payer
Alors 130 € sont déduits
Et 40 € restent à payer par carte bancaire
Et le code est marqué utilisé
```

## Résultat attendu

- L'avoir s'applique bien qu'il ait été émis à la suite d'une sortie dauphins : il n'est rattaché à aucun type de sortie.
- Le montant demandé au prestataire est 40 €.

- **Aucun acompte n'est prélevé** : les 40 € de différence sont dus en totalité
  au moment de réserver, un code excluant le régime d'acompte.
## Ce que ce cas ne vérifie pas

- L'émission de l'avoir par le gérant → cas de `SPEC-ADMIN-06`, à écrire.
- Le surplus perdu si l'avoir dépasse le prix, aligné sur le bon cadeau → `CASE-BOOKING-16`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_BOOKING_33_avoir_deduit_quel_que_soit_le_type_de_sortie` |
| Emplacement | `tests/Application/AvoirTest.php` |
| Doublures | prestataire de paiement, horloge |
