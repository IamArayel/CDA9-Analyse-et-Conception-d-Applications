# CASE-CANCEL-06 - un créneau en alerte reste réservable, le risque étant signalé

**Spécification :** `SPEC-CANCEL-06`
**Critères couverts :** AC-6
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Sortie du 20 juillet à 14h, mise en alerte le 19 juillet.
- 4 places restantes.

## Scénario

```gherkin
Étant donné un créneau du 20 juillet à 14h00 en alerte
Et que nous sommes le 20 juillet à 11h00
Quand un client consulte ce créneau
Alors le risque d'annulation lui est signalé avant qu'il ne valide
Et il peut réserver 2 places
Quand nous sommes le 20 juillet à 12h00
Alors le créneau n'est plus réservable
```

## Résultat attendu

- La réservation prise à 11h00 est acceptée, avec l'avertissement affiché avant validation.
- À 12h00 la fermeture habituelle du créneau de 14h s'applique, indépendamment de l'alerte.
- L'alerte, elle, court jusqu'à 14h00.

## Ce que ce cas ne vérifie pas

- La confirmation d'annulation reçue par ce client → `CASE-CANCEL-07`.
- La formulation exacte de l'avertissement, non fournie par le client.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_06_creneau_en_alerte_reste_reservable_jusqua_la_fermeture` |
| Emplacement | `tests/Application/AlerteMeteoTest.php` |
| Doublures | horloge |
