# CASE-ADMIN-05 - un tarif négatif ou nul est refusé

**Spécification :** `SPEC-ADMIN-02`
**Critères couverts :** AC-3
**Type :** erreur
**Niveau :** domaine
**Statut :** automatisé

## Préconditions

- Grille tarifaire en place.

## Scénario

```gherkin
Étant donné la saisie d'un tarif
Quand le gérant saisit un tarif adulte de -10 €
Alors la saisie est refusée
Quand il saisit un tarif adulte de 0 €
Alors la saisie est refusée
Et la grille reste inchangée
```

## Résultat attendu

- Aucune valeur négative ou nulle n'entre dans la grille.
- Le refus du 0 € est une décision d'équipe : le client n'a jamais prévu de sortie gratuite.

## Ce que ce cas ne vérifie pas

- Le forfait de privatisation d'un bateau sans tarif saisi, qui est légitimement vide → `CASE-ADMIN-11`.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_ADMIN_05_tarif_negatif_ou_nul_refuse` |
| Emplacement | `tests/Domaine/ValiditeDunTarifTest.php` |
| Doublures | aucune |
