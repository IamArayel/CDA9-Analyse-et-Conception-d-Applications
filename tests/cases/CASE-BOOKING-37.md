# CASE-BOOKING-37 - le parcours complet aboutit sur les trois familles d'appareils

**Spécification :** `SPEC-BOOKING-08`
**Critères couverts :** AC-1, AC-2, AC-3
**Type :** nominal
**Niveau :** bout en bout
**Statut :** manuel assumé

## Préconditions

- Sortie dauphins du 20 juillet à 10h, places disponibles.
- Trois appareils : un téléphone, une tablette, un ordinateur.
- Le téléphone est en 4G, pas en wifi.

## Scénario

```gherkin
Étant donné un client sur téléphone, puis sur tablette, puis sur ordinateur
Quand il effectue le parcours complet, consultation des places, formulaire, paiement
Alors la réservation aboutit sur les trois appareils
Et aucun écran n'impose de défilement horizontal sur une largeur de 320 pixels
Et le parcours aboutit également en connexion 4G
```

## Résultat attendu

- Trois parcours menés jusqu'à la confirmation de réservation.
- Aucun défilement horizontal, aucun bouton hors de l'écran, aucun champ inatteignable.
- Le parcours en 4G aboutit sans blocage, même si l'affichage est plus lent.

## Ce que ce cas ne vérifie pas

- Les performances chiffrées sous charge → `CASE-NFR-05`.
- Le rendu graphique et la charte, le client n'ayant qu'un logo et aucune charte définie.

## Vérification

Ce cas n'est **pas automatisé**, et c'est une décision, pas un oubli :
`docs/strategie-de-test.md` §4 en donne le motif.

| Quoi | Comment |
|---|---|
| Qui | un membre de l'équipe, seul |
| Quand | une fois avant la présentation de J10, et après toute reprise du parcours de réservation |
| Preuve | trois captures, une par appareil, sur l'écran de confirmation, jointes au journal du jour |
| Pourquoi pas automatisé | automatiser un rendu visuel sur trois familles d'appareils coûterait plus que le risque couvert, pour un site à un seul parcours |
