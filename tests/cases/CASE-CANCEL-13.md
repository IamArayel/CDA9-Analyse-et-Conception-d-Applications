# CASE-CANCEL-13 - la date et le canal de chaque message envoyé sont enregistrés

**Spécification :** `SPEC-CANCEL-04`
**Critères couverts :** AC-6
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Créneau du 20 juillet à 10h, annulé.
- Deux réservations confirmées.
- L'adresse e-mail de l'un des deux clients est invalide.

## Scénario

```gherkin
Étant donné un créneau annulé portant deux réservations
Quand les messages d'annulation sont envoyés
Alors quatre envois sont enregistrés, deux clients sur deux canaux
Et chacun porte son type, son canal et sa date
Et l'envoi e-mail en échec est enregistré comme tel
```

## Résultat attendu

- Quatre traces d'envoi, pas deux : le canal fait partie de la trace.
- L'échec d'un canal n'empêche pas l'autre de partir.
- Le gérant peut répondre à un client affirmant n'avoir rien reçu, ce qu'il ne pourrait pas faire sans cette trace.

## Ce que ce cas ne vérifie pas

- La délivrance réelle du message, qui ne dépend pas de nous et que le client a placée hors de notre responsabilité.
- Le contenu du message, non fourni.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_13_trace_des_envois_type_canal_et_date` |
| Emplacement | `tests/Application/AnnulationEtRemboursementTest.php` |
| Doublures | envoi de messages |
