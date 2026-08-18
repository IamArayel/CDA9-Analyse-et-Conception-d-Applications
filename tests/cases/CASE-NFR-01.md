# CASE-NFR-01 - les messages automatiques partent dans la langue choisie à la réservation

**Spécification :** `SPEC-NFR-02`
**Critères couverts :** AC-2, AC-3
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Deux réservations confirmées sur la sortie du 20 juillet à 10h.
- Le premier client a choisi l'anglais, le second n'a rien choisi.

## Scénario

```gherkin
Étant donné deux clients inscrits, l'un ayant choisi l'anglais, l'autre rien
Quand le message de rappel est envoyé
Alors le premier le reçoit en anglais
Et le second le reçoit en français
Quand le créneau est mis en alerte puis annulé
Alors les messages d'alerte et de confirmation suivent la même règle
```

## Résultat attendu

- La langue est celle enregistrée sur la réservation, pas celle du dernier écran consulté.
- Elle s'applique aux trois messages automatiques, rappel, alerte et confirmation d'annulation.
- Le français est la valeur par défaut quand aucun choix n'a été fait.

## Ce que ce cas ne vérifie pas

- Le contenu rédactionnel des messages, non fourni par le client.
- La langue des documents émis par le prestataire de paiement, hors de notre contrôle.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_NFR_01_messages_automatiques_dans_la_langue_choisie` |
| Emplacement | `tests/Application/LangueDesMessagesTest.php` |
| Doublures | horloge, envoi de messages |
