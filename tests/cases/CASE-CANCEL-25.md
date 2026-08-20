# CASE-CANCEL-25 - le lien de règlement part à 7h la veille, et seulement s'il reste un solde

**Spécification :** `SPEC-CANCEL-07`
**Critères couverts :** AC-1, AC-2, AC-3, limites 1, 2 et 4
**Type :** nominal
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Une sortie dauphins du 20 juillet à 14h sur le Ti Kap.
- Marie réserve 2 places adultes le 18 juillet et verse son acompte : 30 € versés, 70 € dus.
- John réserve le 18 juillet avec un bon cadeau de 150 € qui couvre entièrement sa réservation.

## Scénario

```gherkin
Étant donné les réservations confirmées de Marie et de John
Quand la tâche d'envoi passe le 19 juillet à 6h59
Alors aucun lien de règlement n'est parti
Quand elle repasse le 19 juillet à 7h00
Alors Marie reçoit son lien de règlement par courriel
Et John n'en reçoit aucun, son bon cadeau ayant tout couvert
Et le solde de Marie devient réglable en ligne
Quand la tâche repasse le 19 juillet à 8h00
Alors Marie n'a toujours reçu qu'un seul lien
```

## Résultat attendu

- Le lien part à 7h, **quel que soit le créneau** : pour un départ à 14h, ce n'est ni 24 heures avant, ni l'heure de départ moins un délai.
- Une réservation sans solde ne reçoit rien : le message n'a pas d'objet.
- L'envoi est tracé, avec sa date, son destinataire et son canal, comme les trois autres.
- La fenêtre de `SPEC-BOOKING-12` s'ouvre exactement quand le lien part.

## Ce que ce cas ne vérifie pas

- Le contenu rédactionnel du message, jamais fourni par le client, pas plus que celui des trois autres.
- Le fait que le lien parte par courriel **seulement**, et pas par SMS : c'est une hypothèse d'équipe, vérifiée par le test mais non validée par le client.
- Le règlement lui-même → `CASE-BOOKING-40` et `CASE-BOOKING-41`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_25_lien_de_reglement_envoye_a_sept_heures_la_veille` |
| Emplacement | `tests/Application/LienDeReglementTest.php` |
| Doublures | horloge, notificateur, prestataire de paiement |
