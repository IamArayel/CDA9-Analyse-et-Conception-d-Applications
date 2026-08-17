# CASE-CANCEL-18 - annuler deux fois, ou annuler après le départ, reste sans effet

**Spécification :** `SPEC-CANCEL-02`
**Critères couverts :** AC-4, AC-5
**Type :** erreur
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Créneau A du 20 juillet à 10h, déjà annulé.
- Créneau B du 18 juillet à 10h, déjà passé.

## Scénario

```gherkin
Étant donné un créneau déjà annulé
Quand le gérant l'annule de nouveau
Alors l'état reste « annulé »
Et aucun second message n'est envoyé
Et aucun second remboursement n'est demandé
Étant donné un créneau déjà passé
Quand le gérant tente de l'annuler
Alors l'action est refusée
```

## Résultat attendu

- Aucune erreur bloquante sur la double annulation : c'est un geste sans effet, pas une faute.
- Surtout, aucun doublon d'envoi ni de remboursement.
- Une sortie passée n'est plus annulable : elle a eu lieu.

## Ce que ce cas ne vérifie pas

- L'annulation entre le repère des 2 heures et le départ, qui elle est acceptée → `CASE-CANCEL-05`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_18_double_annulation_et_creneau_passe_sans_effet` |
| Emplacement | `tests/` |
| Doublures | horloge, envoi de messages, prestataire de paiement |
