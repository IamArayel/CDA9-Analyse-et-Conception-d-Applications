# CASE-ADMIN-16 - le barème rend tout, puis les trois quarts, puis rien

**Spécification :** `SPEC-ADMIN-06`
**Critères couverts :** AC-6, AC-7
**Type :** limite
**Niveau :** application
**Statut :** automatisé

> **Repris en v6, 2026-08-20.** `CR-07/Q11` rend le barème calculable et
> **non uniforme** : au-delà de 48 heures la commission s'applique à ce que le
> client a versé, en deçà elle s'applique au prix total puis se plafonne. Le
> client a confirmé les deux formules séparément, après avoir vu l'écart
> chiffré.

## Préconditions

- Trois réservations dauphins de 100 € chacune sur la sortie du 20 juillet à 7h.
- Chacune a versé son acompte de 30 €, aucune n'a réglé son solde.

## Scénario

```gherkin
Étant donné trois réservations de 100 € dont 30 € ont été versés
Quand la première est annulée le 10 juillet, à plus de 7 jours du départ
Alors son client récupère 30 €
Quand la deuxième est annulée le 16 juillet, entre 7 jours et 48 heures
Alors son client récupère 22,50 €
Quand la troisième est annulée le 19 juillet à 19h, à moins de 48 heures
Alors son client ne récupère rien
Et rien ne lui est réclamé
```

## Résultat attendu

- 30 €, puis 22,50 €, puis 0 €. **Les deux premiers montants sont des
  pourcentages du versé, le troisième un plafonnement d'une commission
  calculée sur le prix total.**
- Le gérant perd 20 € sur la troisième annulation, et l'accepte : c'est
  l'arbitrage commercial de `CR-06`.

## Ce que ce cas ne vérifie pas

- **Le client soldé**, qui récupère 50 % du prix total : il ne peut avoir
  soldé que dans la fenêtre du lien, donc à moins de 48 heures du départ.
- Le client absent au départ → `CASE-ADMIN-17`.
- Le renoncement après une alerte météo, qui rend tout → `CASE-ADMIN-15`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_16_retenue_plafonnee_a_lacompte` |
| Emplacement | `tests/Application/IssueDannulationClientTest.php` |
| Doublures | horloge, prestataire de paiement |
