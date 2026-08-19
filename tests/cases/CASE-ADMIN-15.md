# CASE-ADMIN-15 - un client qui renonce après une alerte récupère tout ce qu'il a versé

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-4
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-19.** `CR-06` remplace le paiement intégral par un
> acompte. Le comportement vérifié ne change pas ; les montants, si.

## Préconditions

- Créneau du 20 juillet à 7h, mis en alerte météo le 19 juillet.
- Une réservation de 260 €, dont 78 € d'acompte versés, à moins de 48 heures du
  départ.

## Scénario

```gherkin
Étant donné un créneau en alerte météo et une réservation dont 78 € ont été versés
Quand le client appelle le 19 juillet pour renoncer
Et que le gérant enregistre l'issue « remboursement »
Alors le montant proposé est 78 €, sans retenue
Et il reste acquis même si la sortie a finalement lieu
```

## Résultat attendu

- Aucune retenue n'est appliquée, alors que le barème prévoirait 50 % à moins de 48 heures.
- L'alerte l'emporte sur le barème : le risque vient du gérant, pas du client.
- Le remboursement n'est pas repris si la sortie part finalement.

## Ce que ce cas ne vérifie pas

- Le barème dégressif hors alerte, appliqué à la main par le gérant.
- Le remboursement après une annulation décidée par le gérant → `CASE-CANCEL-10`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_15_client_renoncant_apres_alerte_rembourse_en_totalite` |
| Emplacement | `tests/Application/IssueDannulationClientTest.php` |
| Doublures | horloge, prestataire de paiement |
