# Architecture — <projet>

**Version :** v1 — J5
**Décisions associées :** `adr/ADR-001-stack.md`, `adr/ADR-002-persistance.md`

Décrit comment le système est organisé et pourquoi. Chaque choix se rattache à une
exigence (`REQ`) ou à une contrainte du client.

---

## 1. Vue d'ensemble

Un schéma, puis quelques phrases. Le schéma peut vivre en PlantUML dans
`uml/components.puml`.

## 2. Couches

| Couche | Responsabilité | Ce qu'elle a le droit d'appeler | Ce qui n'a rien à y faire |
|---|---|---|---|
| UI | … | Application | règle métier, requête SQL |
| Application | orchestration des cas d'usage | Domaine, Infrastructure | règle métier |
| Domaine | les règles métier | rien | framework, base de données |
| Infrastructure | persistance, paiement, envoi d'emails | services externes | règle métier |

La colonne de droite est celle qui sert en review : c'est elle qui permet de dire
si une portion de code générée est à sa place.

## 3. Où vivent les règles métier

Lister explicitement, pour les règles les plus sensibles, l'endroit du code qui en
est responsable. C'est ce qu'on vous demandera à J10.

| Règle | Spécification | Où elle est implémentée |
|---|---|---|
| retenue d'annulation selon le délai | SPEC-CANCEL-0x | … |
| capacité d'une sortie jamais dépassée | SPEC-BOOKING-0x | … |

## 4. Arborescence applicative

```text
src/
└── …
```

Expliquer le principe de rangement en une phrase, pas fichier par fichier.

## 5. Modèle de données

Renvoyer au MCD/MLD. Ici, seulement ce qui a une conséquence architecturale :
transactions, unicité, contraintes de concurrence.

> La concurrence sur la dernière place disponible (`SPEC-BOOKING-0x`) est traitée
> par … et garantie par …

## 6. Services externes

| Service | Usage | Ce qui se passe s'il est indisponible |
|---|---|---|
| paiement | … | … |
| envoi d'emails | … | … |

La colonne de droite n'est pas facultative : le client annule des sorties pour
cause de météo et doit joindre des gens déjà en route.

## 7. Sécurité et contrôle d'accès

| Rôle | Ce qu'il peut faire | Ce qu'il ne peut pas faire |
|---|---|---|
| visiteur | … | … |
| client | … | … |
| accueil | … | … |
| gérant | … | … |

## 8. Déploiement

Où, comment, à quel coût mensuel, et qui appuie sur le bouton. Rattacher à la
contrainte budgétaire du client.

## 9. Ce que cette architecture ne fait pas

Les limites assumées, et à partir de quel moment elles deviendraient gênantes.

- …
