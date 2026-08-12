## SPEC-<DOM>-01 — <titre court, en langage métier>

**Exigence :** REQ-0xx
**Statut :** brouillon | revue IA faite | validée
**Version :** v1

### Règle

Une phrase, à l'indicatif, qui dit ce qui doit être vrai. Pas de « le système
pourrait », pas de « idéalement ».

> À moins de 48 heures du départ, une annulation à l'initiative du client
> entraîne une retenue de 50 % du montant total de la réservation.

### Portée

Ce que cette spécification couvre, et surtout **ce qu'elle ne couvre pas**. Nommer
explicitement les cas voisins traités ailleurs, avec leur ID.

- Ne couvre pas l'annulation à l'initiative du prestataire → `SPEC-<DOM>-0x`
- Ne couvre pas la réduction du nombre de participants → `SPEC-<DOM>-0x`

### Scénarios nominaux

```gherkin
Étant donné une réservation payée de 260 € prévue le 12 juillet à 10h00
Et que nous sommes le 11 juillet à 09h00
Quand le client annule sa réservation
Alors 130 € restent acquis au prestataire
Et 130 € sont remboursés
Et la réservation passe à l'état « annulée »
```

### Cas limites

Un cas limite par ligne, avec le comportement attendu. C'est la partie qui
distingue une spécification d'une intention.

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | annulation exactement 48h00 avant le départ | … |
| 2 | annulation après l'heure de départ | … |
| 3 | réservation déjà annulée | … |
| 4 | réservation d'un montant nul (offerte) | … |

### Ce qui n'est pas défini

Assumé et daté. Une zone grise déclarée vaut mieux qu'une zone grise ignorée.

- <question restée sans réponse du client, et hypothèse retenue en attendant>

### Critères d'acceptation

Chacun doit être vérifiable sans interprétation, et donne lieu à au moins un cas
de test.

- [ ] AC-1 — …
- [ ] AC-2 — …

### Revue IA

Consigne utilisée :

> Analyse cette spécification. Recherche les ambiguïtés, contradictions,
> comportements non définis, cas limites oubliés et exigences impossibles à
> tester. Ne réécris pas la spécification.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| … | acceptée / refusée | … |

Les refus se reportent aussi dans `docs/journal.md`.
