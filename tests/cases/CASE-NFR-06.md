# CASE-NFR-06 - aucun coût récurrent n'est engagé sans être documenté

**Spécification :** `SPEC-NFR-03`
**Critères couverts :** AC-1, AC-2
**Type :** nominal
**Niveau :** domaine
**Statut :** manuel assumé

## Préconditions

- `ADR-001` documente l'hébergement mutualisé et son coût mensuel.
- Une plateforme d'envoi de SMS est retenue depuis J6, elle introduit un second coût récurrent.

## Scénario

```gherkin
Étant donné les décisions d'architecture du projet
Quand on recense les coûts récurrents engagés pour le client
Alors chacun est documenté dans une décision d'architecture, avec son montant mensuel
Et aucun abonnement, aucun service payant n'existe en dehors de cette liste
```

## Résultat attendu

- Deux coûts récurrents à ce jour, l'hébergement et l'envoi de SMS, chacun rattaché à un ADR.
- La liste est exhaustive : un service ajouté sans ADR fait échouer cette vérification.
- Le client a répondu « budget illimité » pour l'exercice, ce qui lève la contrainte de montant mais pas celle de la traçabilité.

## Ce que ce cas ne vérifie pas

- Le montant lui-même, qui n'est plus un critère depuis la réponse du client sur le budget.
- Le budget total du projet, hors de notre main.

## Vérification

Ce cas n'est **pas automatisé**, et c'est une décision, pas un oubli :
`docs/strategie-de-test.md` §4 en donne le motif.

| Quoi | Comment |
|---|---|
| Qui | l'équipe, en relecture croisée |
| Quand | à J9 lors de la revue croisée, puis avant le rendu de J10 |
| Preuve | la liste des ADR citant un coût récurrent, confrontée aux services réellement utilisés |
| Pourquoi pas automatisé | le critère est documentaire : il vérifie qu'une décision est écrite, pas qu'un logiciel se comporte d'une certaine façon |
