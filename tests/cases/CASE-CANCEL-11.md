# CASE-CANCEL-11 - aucun choix entre report, avoir et remboursement n'est proposé

**Spécification :** `SPEC-CANCEL-04`
**Critères couverts :** AC-2
**Type :** limite
**Niveau :** application
**Statut :** automatisé

## Préconditions

- Créneau du 20 juillet à 10h, annulé par le gérant.
- Une réservation confirmée payée 260 €.

## Scénario

```gherkin
Étant donné un créneau annulé par le gérant
Quand le gérant consulte la fiche du client concerné
Alors aucun écran ne lui propose d'enregistrer un report, un avoir ou un remboursement
Et le remboursement intégral est la seule issue
```

## Résultat attendu

- Le triptyque n'apparaît nulle part dans le parcours d'annulation météo.
- Il n'existe que pour une annulation demandée par le client, spécifiée dans le domaine ADMIN.
- C'est la correction du 2026-08-14 : la lecture antérieure venait d'une transcription erronée de `CR-02/Q04`.

## Ce que ce cas ne vérifie pas

- L'enregistrement d'une issue après une annulation client → cas de `SPEC-ADMIN-06`, à écrire.
- Le sort d'une réservation payée par bon cadeau, déclaré non défini.

## Test automatisé

| Attendu | Valeur |
|---|---|
| Nom du test | `test_CASE_CANCEL_11_aucun_choix_propose_apres_annulation_gerant` |
| Emplacement | `tests/Application/AnnulationEtRemboursementTest.php` |
| Doublures | envoi de messages, prestataire de paiement |
