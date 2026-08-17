# CASE-NFR-05 - l'application tient trente parcours simultanés sans dégradation perceptible

**Spécification :** `SPEC-NFR-01`
**Critères couverts :** AC-1, AC-2, AC-3
**Type :** limite
**Niveau :** bout en bout
**Statut :** manuel assumé

## Préconditions

- Environnement identique à la production, hébergement mutualisé.
- Jeu de données de référence, créneaux ouverts en saison.
- Trente parcours de réservation lancés en parallèle.

## Scénario

```gherkin
Étant donné trente parcours de réservation simultanés
Quand ils se déroulent de la consultation au paiement
Alors chaque écran répond en moins de 2 secondes
Et aucune réservation confirmée n'est perdue
Quand un export de planning est lancé pendant ce pic
Alors aucun parcours n'est interrompu
```

## Résultat attendu

- Trente parcours menés en parallèle, aucune réservation confirmée manquante à l'arrivée.
- Le seuil de 2 secondes et le chiffre de 30 sont des **hypothèses d'équipe**, pas un engagement client : le client n'a fourni aucune volumétrie.
- La borne haute réelle est physique, 36 places par créneau et 108 par jour.

## Ce que ce cas ne vérifie pas

- Un engagement de disponibilité, jamais négocié et impossible à tenir sur un hébergement mutualisé.
- Le comportement au-delà de trente parcours, hors des hypothèses retenues.

## Vérification

Ce cas n'est **pas automatisé**, et c'est une décision, pas un oubli :
`docs/strategie-de-test.md` §4 en donne le motif.

| Quoi | Comment |
|---|---|
| Qui | un membre de l'équipe, avec un outil de tir de charge simple |
| Quand | une fois, avant la mise en production, et à refaire si l'hébergement change |
| Preuve | temps de réponse relevés et nombre de réservations confirmées, consignés au journal |
| Pourquoi pas automatisé | une mesure ponctuelle suffit à une hypothèse d'équipe ; un test de charge permanent surveillerait un engagement qui n'existe pas |
