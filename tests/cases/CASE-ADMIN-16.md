# CASE-ADMIN-16 - la retenue est plafonnée à l'acompte, et rend la différence

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-6, AC-7
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Deux réservations dauphins de 100 € chacune, acompte de 30 € versé.
- La première est annulée par son client 5 jours avant le départ, la seconde
  36 heures avant.

## Scénario

```gherkin
Étant donné une réservation de 100 € dont 30 € ont été versés
Quand le client annule 5 jours avant le départ
Alors la commission de 25 % vaut 25 €
Et 5 € lui sont remboursés
Étant donné une seconde réservation identique
Quand ce client annule 36 heures avant le départ
Alors la commission de 50 % vaut 50 €, plafonnée à 30 €
Et rien ne lui est remboursé
Et rien ne lui est réclamé
```

## Résultat attendu

- Le plafond joue **dans les deux sens** : il rend 5 € dans un cas, il retient
  tout dans l'autre.
- Le gérant perd 20 € sur la seconde annulation, et l'accepte : c'est
  l'arbitrage commercial de `CR-06`.

## Ce que ce cas ne vérifie pas

- **La restitution des 5 € est une hypothèse d'équipe.** Le client n'a raisonné
  que sur la tranche 48h-24h, celle où le plafond joue en sa faveur. Question 19
  du §11 du cahier des charges.
- Le barème dégressif lui-même, appliqué à la main par le gérant.
- Le renoncement après une alerte météo, qui rend tout → `CASE-ADMIN-15`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_16_retenue_plafonnee_a_lacompte` |
| Emplacement | `tests/` |
| Doublures | horloge, prestataire de paiement |
