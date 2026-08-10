# Diagrammes UML

Les diagrammes vivent en **PlantUML** dans ce dossier : c'est du texte, donc
versionnable, diffable et relisible par l'agent en revue critique. Une capture
d'écran d'un outil de dessin ne l'est pas.

---

## Fichiers attendus

| Fichier | Diagramme | Quand |
|---|---|---|
| `use-cases.puml` | cas d'utilisation | J4 |
| `domain.puml` | classes du domaine | J4 |
| `sequences/<cas>.puml` | séquence, un fichier par cas d'usage sensible | J4 |
| `components.puml` | composants *(facultatif)* | J5 |

Trois diagrammes de séquence suffisent : les cas d'usage où plusieurs acteurs ou
services interviennent. Faire une séquence par écran est du remplissage.

## Rendu

```bash
# avec plantuml installé
plantuml -tsvg docs/uml/**/*.puml
```

Les images générées ne sont pas commitées : seules les sources le sont.

## Conventions

- Les noms de classes, d'acteurs et de cas d'usage reprennent **le vocabulaire du
  client**. Si le client dit « sortie », la classe s'appelle `Sortie`, pas
  `TripSession`.
- Chaque cas d'usage cite la spécification qu'il réalise, en note :

```plantuml
note right of (Annuler une réservation)
  SPEC-CANCEL-03
end note
```

- Le diagramme de classes du domaine ne contient **ni** contrôleur, **ni**
  repository, **ni** classe technique. Il contient les concepts métier et leurs
  règles.

## Revue critique

Après votre v1, consigne à donner à l'agent :

> Compare ce diagramme aux spécifications jointes. Signale les incohérences, les
> responsabilités mal placées, les règles métier absentes du modèle et les
> relations qui ne sont justifiées par aucune spécification. Ne produis pas de
> diagramme corrigé.

Vous arbitrez, puis vous produisez la v2. Les remarques refusées se reportent dans
`docs/journal.md`.
