# CASE-CANCEL-19 - un créneau annulé disparaît, un créneau en alerte reste avec son avertissement

**Spécification :** `SPEC-CANCEL-03`
**Critères couverts :** AC-1, AC-2, AC-3
**Type :** nominal
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Deux créneaux du 20 juillet, l'un à 10h, l'autre à 14h.
- Un client consulte la liste des créneaux au moment des deux décisions.

## Scénario

```gherkin
Étant donné un client affichant les créneaux du 20 juillet
Quand le gérant met le créneau de 14h en alerte
Alors ce créneau reste proposé, avec le risque signalé
Quand le gérant annule le créneau de 10h
Alors ce créneau n'est plus proposé à la réservation
Et l'affichage du client s'est mis à jour sans rechargement
```

## Résultat attendu

- Les deux décisions ont des effets opposés : l'alerte laisse vendre, l'annulation retire.
- Le client n'a rien rechargé : c'est le même mécanisme temps réel que le décompte des places.

## Ce que ce cas ne vérifie pas

- Les messages envoyés aux inscrits → `CASE-CANCEL-03` et `CASE-CANCEL-10`.
- La formulation exacte de l'avertissement, non fournie par le client.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_19_annulation_retire_le_creneau_alerte_le_conserve` |
| Emplacement | `tests/` |
| Doublures | horloge |
