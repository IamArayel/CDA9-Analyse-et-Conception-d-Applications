# Analyse d'impact - CR-002

**Demande du client :** révision du fonctionnement du bon cadeau et de la durée
de validité de l'avoir, formulée oralement lors d'un point de suivi.
**Reçue le :** 2026-08-13, entretien oral (compte rendu à formaliser en CR-04).
**Rédigée par :** l'équipe, avec revue critique de l'IA.

---

> **Interdiction de modifier le code avant que cette analyse soit complète.**
>
> La modification descend la chaîne dans cet ordre : cahier des charges → specs →
> UML → modèle de données → cas de test → tests → code.

Ce CR-002 porte sur une demande courte mais **régressive** : elle retire une
exigence `Must` déjà formalisée en v3 (`REQ-045`), issue d'une déclaration
spontanée du client lors du troisième entretien. C'est le premier changement
du projet qui *simplifie* le périmètre au lieu de l'étendre. Aucun code ni
test automatisé n'existe encore (`src/`, `tests/cases/` sont vides) :
l'analyse porte sur le cahier des charges, les spécifications et l'UML.

---

## 1. Ce que le client demande, reformulé

Le client revient sur le fonctionnement du bon cadeau qu'il avait décrit au
troisième entretien. Le bon cadeau n'est plus rattaché à un type de sortie :
l'acheteur choisit librement un montant, sans désigner ni sortie baleines,
ni sortie dauphins, ni privatisation, et sans distinguer un tarif adulte
d'un tarif enfant. À l'usage, le code se déduit du montant total de la
réservation ; si ce montant est supérieur au bon, le bénéficiaire paie le
solde par carte, et s'il est inférieur, le surplus est perdu. Ces deux
dernières règles étaient déjà acquises et ne changent pas.

Le client tranche par ailleurs deux points restés en suspens : l'avoir est
lui aussi valable un an, comme le bon cadeau, et une réservation porte
toujours sur une seule sortie, sans regroupement de plusieurs sorties en une
commande unique.

## 2. Questions posées au client

| # | Question | Réponse |
|---|---|---|
| 1 | Le montant d'un bon cadeau est-il libre, ou aligné sur un tarif ? | Montant libre choisi à l'achat. Tranche la question 9 du §11 du cahier des charges |
| 2 | Le bon reste-t-il rattaché à un type de sortie ? | Non. Aucune restriction de type de sortie ni de catégorie de tarif |
| 3 | Sur quoi le bon s'impute-t-il ? | Sur le montant total de la réservation |
| 4 | L'avoir a-t-il une durée de validité ? | Oui, un an, comme le bon cadeau |
| 5 | Le mot « panier » implique-t-il plusieurs sorties en une commande ? | Non. Une réservation porte sur une seule sortie |

Le client n'a **pas** été interrogé sur la conséquence la plus lourde de sa
demande : la quasi-disparition de la différence entre un bon cadeau et un
avoir (voir §8). Le point est reporté au prochain entretien.

## 3. Impact - cahier des charges

| Exigence | Impact | Action |
|---|---|---|
| REQ-045 | **modifiée (inversée)** | La spécificité de type de sortie disparaît. L'exigence est réécrite en sens inverse : montant libre, aucun rattachement à un type de sortie ni à une catégorie de tarif. L'identifiant est conservé pour que l'historique reste lisible et que la matrice ne perde pas la trace du changement |
| REQ-047 | modifiée | « prix de la sortie réservée » remplacé par « montant total de la réservation », le client ayant parlé du total du panier. Précision, pas changement de règle |
| REQ-048 | modifiée | Même reformulation que REQ-047 |
| REQ-051 *(nouvelle)* | ajoutée | Un avoir est valable 1 an à compter de sa date d'émission |
| REQ-043, REQ-044, REQ-046, REQ-049 | inchangées | La vente sur la plateforme, la validité d'un an, la saisie du code et l'usage unique ne sont pas touchés |
| REQ-050 | inchangée | Le mécanisme du code d'avoir ne change pas ; seule sa durée de validité s'ajoute, en REQ-051 |
| §11 question 8 | modifiée | L'hypothèse retenue citait « bon cadeau spécifique à un type de sortie » comme critère distinctif : ce critère n'existe plus |
| §11 question 9 | **répondue** | Montant libre. La question sort de la liste des points ouverts |
| §13 glossaire | modifié | Les définitions de « Bon cadeau » et « Avoir » citent des règles qui changent |

Le reste des exigences est **inchangé** : la demande ne touche ni aux
créneaux, ni à la capacité, ni à la tarification des sorties, ni à
l'annulation.

## 4. Impact - spécifications

| Spécification | Impact | Ce qui change exactement |
|---|---|---|
| SPEC-BOOKING-09 | **modifiée en profondeur** | La règle perd le rattachement à un type de sortie. Disparaissent : le cas limite 7 (code saisi pour un autre type de sortie), le cas limite 12 (sortie devenue plus chère), le critère AC-7, et l'hypothèse de travail sur le prix d'achat. Apparaît : le montant libre saisi à l'achat, et l'imputation sur le montant total |
| SPEC-BOOKING-10 | modifiée | Ajout de la validité d'un an et de son point de départ. Le cas limite 5 (avoir utilisé sur un autre type de sortie) et le critère AC-4 perdent leur intérêt distinctif, l'absence de contrainte de type n'étant plus une spécificité de l'avoir. Le paragraphe des trois critères distinctifs passe à un seul |
| SPEC-BOOKING-06 | inchangée | Le calcul du montant d'une réservation ne bouge pas ; le bon s'applique après |
| SPEC-BOOKING-07 | inchangée | Le montant dû nul après application d'un bon reste traité par le cas limite 4 et AC-6 |
| SPEC-CANCEL-04 | inchangée | Le choix report/avoir/remboursement ne change pas de mécanique. Son cas limite 5 (réservation payée par bon cadeau) reste ouvert et devient même plus simple à traiter, le bon n'ayant plus de type |

## 5. Impact - conception

| Artefact | Impact | Ce qui change |
|---|---|---|
| `uml/domain.puml` | modifié | `BonCadeau` perd l'attribut `typeSortie` et n'a aucune association vers `Tarif`. `Avoir` gagne `dateEmission` et `dateExpiration`. Aucune classe créée ni supprimée |
| `uml/use-cases.puml` | inchangé | UC10 et UC11 restent valides : acheter un bon cadeau, utiliser un bon ou un avoir au paiement |
| `uml/sequences/…` | à créer | Le futur diagramme « Réserver une sortie » intègre une branche « avec code » unique, au lieu de deux branches distinctes |
| MCD / MLD | non commencé | Intrant direct : les tables `bon_cadeau` et `code_avoir` ont désormais des colonnes identiques à l'origine près. Voir §8 |

**État nouveau ou donnée nouvelle ?** Aucune donnée nouvelle. Le changement
**retire** deux données de `BonCadeau` et en ajoute deux à `Avoir`. C'est le
premier CR du projet qui allège le modèle.

## 6. Impact - tests

Aucun cas de test n'existe à ce jour. Ce CR modifie le périmètre des
`CASE-BOOKING-*` à écrire :

| Cas de test | Impact |
|---|---|
| achat d'un bon cadeau à montant libre | remplace le cas « achat d'un bon pour un type de sortie donné » |
| usage d'un bon sur une réservation de n'importe quel type de sortie | remplace le cas « code refusé pour un autre type de sortie » |
| usage d'un avoir après un an | **nouveau** : l'expiration de l'avoir n'existait pas |

## 7. Impact - code

Sans objet : `src/` ne contient qu'un `.gitkeep`. À anticiper au découpage :
le moteur d'application d'un code au montant dû n'a plus besoin de connaître
le type de sortie de la réservation, ce qui supprime un couplage entre le
composant de paiement et le catalogue des sorties.

## 8. Effets de bord identifiés

Ce que la demande touche sans que le client l'ait envisagé.

- **`BonCadeau` et `Avoir` deviennent des jumeaux.** Avant ce CR, ils
  différaient sur trois points : le type de sortie, l'expiration, l'origine.
  Le client vient de supprimer les deux premiers. Il ne reste que l'origine
  (vendu au client contre accordé par le gérant) et le point de départ de
  l'expiration (date d'achat contre date d'émission). Les deux classes ont
  désormais les mêmes attributs et le même comportement à l'usage. La
  question 8 du §11 cesse d'être une question de vocabulaire pour devenir
  une décision de conception : une classe `Code` avec un attribut d'origine
  est défendable. **Non tranché dans ce CR** : les deux dispositifs restent
  séparés, conformément à l'hypothèse d'équipe en vigueur, et la question est
  reposée au client.
- **La règle de non-cumul perd sa justification d'origine.** Interdire de
  cumuler un bon cadeau et un avoir se comprenait quand les deux dispositifs
  avaient des règles différentes. Maintenant que ce sont deux montants
  libres imputés sur un total, le non-cumul devient une décision commerciale
  arbitraire, pas une contrainte de modèle. Elle est maintenue faute de règle
  client contraire, mais elle n'est plus étayée.
- **Le montant libre ouvre une question de bornes.** Le client n'a fixé ni
  minimum, ni maximum, ni pas d'arrondi. Un bon de 3 € ou de 5 000 € est
  aujourd'hui conforme à l'exigence telle qu'écrite. Hypothèse d'équipe
  retenue : montant entier, borné entre 10 € et le forfait de privatisation
  le plus élevé (1 100 €), à confirmer.
- **L'avoir expirant à un an est défavorable au client final.** L'avoir
  compense une sortie annulée par l'entreprise. Lui imposer une péremption
  avait été explicitement **refusé** en revue IA de `SPEC-BOOKING-10` en v3,
  au motif qu'aucune règle client ne la fondait. Le client la demande
  aujourd'hui : la décision est désormais sourcée, la revue IA de cette
  spécification est mise à jour en conséquence.

## 9. Ce que nous ne ferons pas dans le temps restant

- Aucune fusion de `BonCadeau` et `Avoir` en une classe unique tant que le
  client n'a pas répondu à la question 8 du §11.
- Aucun contrôle de bornes sur le montant d'un bon cadeau au-delà de
  l'hypothèse d'équipe ci-dessus.
- Aucune reprise des bons cadeaux déjà vendus sous l'ancienne règle : la
  question ne se pose pas, rien n'est en production.

## 10. Ordre d'exécution retenu

| # | Étape | Qui |
|---|---|---|
| 1 | Mettre à jour `docs/cahier-des-charges.md` (v4) : REQ-045 inversée, REQ-047/048 précisées, REQ-051 ajoutée, §11, §12 et §13 mis à jour | équipe |
| 2 | Mettre à jour `specs/booking.md` : `SPEC-BOOKING-09` et `SPEC-BOOKING-10` | équipe |
| 3 | Mettre à jour `docs/uml/domain.puml` : `BonCadeau` allégé, `Avoir` daté | équipe |
| 4 | Régénérer `docs/traceability.md` via `./tools/traceability.sh` | équipe |
| 5 | Formaliser le compte rendu de l'entretien oral du 2026-08-13 en `CR-04`, et y reposer la question de la fusion bon cadeau / avoir | équipe |
