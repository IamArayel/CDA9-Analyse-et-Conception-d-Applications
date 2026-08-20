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
- **Les frais proportionnels à une transaction**, ceux du prestataire de paiement
  au premier chef. `SPEC-NFR-03` porte sur un **montant mensuel** documenté :
  un frais prélevé à l'acte n'en est pas un, il ne s'engage pas et ne court pas
  tant que rien n'est vendu. Ces frais sont décrits en `ADR-006` §« ce qu'elle
  coûte », qui assume d'en payer deux au lieu d'un depuis le passage à l'acompte,
  et le client a répondu « budget illimité » sur le montant. Cette borne est
  posée à J9 : sans elle, le critère AC-2 rendrait le cas rouge pour un service
  dont le coût est décrit mais non mensualisable.

## Vérification

Ce cas n'est **pas automatisé**, et c'est une décision, pas un oubli :
`docs/strategie-de-test.md` §4 en donne le motif.

| Quoi | Comment |
|---|---|
| Qui | l'équipe, en relecture croisée |
| Quand | à J9 lors de la revue croisée, puis avant le rendu de J10 |
| Preuve | la liste des ADR citant un coût récurrent, confrontée aux services réellement utilisés |
| Pourquoi pas automatisé | le critère est documentaire : il vérifie qu'une décision est écrite, pas qu'un logiciel se comporte d'une certaine façon |

### Exécution du 2026-08-20, revue croisée de J9

**Verdict : passé**, une fois la borne du §« ce que ce cas ne vérifie pas » posée.

| Coût engagé | Documenté où | Montant mensuel |
|---|---|---|
| Hébergement mutualisé Hostinger | `ADR-001` §5 et son tableau de conséquences | 2,99 €/mois, soit 150 € sur 48 mois |
| Envoi de SMS | `ADR-004` §« ce qu'elle coûte » | second coût récurrent, assumé comme tel |
| Prestataire de paiement | `ADR-006` §« ce qu'elle coûte » | **hors périmètre** : frais à la transaction, non mensualisable, et rien n'est engagé à ce jour |

Confrontation faite dans l'autre sens, en partant des services réellement
utilisés et non de la liste des ADR : le seul conteneur du projet est
`mysql:9.3`, `composer.json` ne tire aucun service payant, et `src/` ne contient
aucun appel sortant. Le port `PrestataireDePaiement` n'a d'ailleurs que deux
adaptateurs, `PrestataireDeDemonstration` et `PrestataireNonConfigure` : le
prestataire est **décidé mais pas branché**, donc aucun de ses frais n'est
engagé aujourd'hui. AC-1 et AC-2 sont tenus.

Point relevé pendant la revue : `ADR-006` **double les frais de transaction**
depuis le passage à l'acompte, et ne les chiffre pas. Ce n'est pas un défaut de
ce cas, mais une question pour le client, ajoutée à celles du §8 de `CR-07`.
Prochaine exécution, comme la table le prévoit, avant le rendu de J10.
