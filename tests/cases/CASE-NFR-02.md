# CASE-NFR-02 - aucun contenu ne reste sans traduction dans l'une des deux langues

**Spécification :** `SPEC-NFR-02`
**Critères couverts :** AC-1
**Type :** limite
**Niveau :** application
**Statut :** à automatiser

## Préconditions

- Les fichiers de traduction française et anglaise du site.
- Les gabarits des trois messages automatiques.

## Scénario

```gherkin
Étant donné les catalogues de traduction des deux langues livrées
Quand ils sont comparés clé par clé
Alors aucune clé présente dans l'un n'est absente de l'autre
Et aucune valeur n'est vide
Et les gabarits des trois messages automatiques existent dans les deux langues
```

## Résultat attendu

- Les deux catalogues portent exactement les mêmes clés.
- Un contenu ajouté après la livraison sans sa traduction fait échouer ce test : c'est le garde-fou dans la durée, plus qu'un contrôle ponctuel.

## Ce que ce cas ne vérifie pas

- La qualité de la traduction, qui relève d'une relecture humaine.
- Les libellés saisis par le gérant, comme un nom de bateau, conservés tels quels dans les deux langues.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_NFR_02_catalogues_de_traduction_complets` |
| Emplacement | `tests/` |
| Doublures | aucune |
