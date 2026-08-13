# Compte rendu d'entretien n° 4

**Date :** 2026-08-13
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …
**Source brute :** échange oral, sans trame écrite préparée ; compte rendu
rédigé après coup à partir des notes rapportées par l'équipe.

> ⚠️ **Statut particulier de ce compte rendu.** Contrairement aux trois
> précédents, il ne s'appuie sur aucune source brute écrite. La colonne
> « formulation exacte du client » du [§5](#5-règles-métier-découvertes)
> reproduit les propos **tels que rapportés par l'équipe**, et non une
> transcription. Les identifiants `CR-04/Qnn` sont utilisables dès à présent
> par le cahier des charges, mais ce document doit être relu par la personne
> qui a mené l'échange avant d'être considéré comme définitif.

> Cet échange revient sur un sujet déjà formalisé : le bon cadeau, introduit
> spontanément par le client au troisième rendez-vous
> ([`compte-rendu-entretien-03.md`](./compte-rendu-entretien-03.md), Q07). Il
> en **retire** deux règles au lieu d'en ajouter. Il tranche par ailleurs
> deux points laissés en hypothèse d'équipe. Voir l'analyse d'impact dédiée,
> [`impact-CR-002.md`](./impact-CR-002.md).

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë — c'est
précisément l'ambiguïté qu'il faudra lever.

Le client revient de lui-même sur le fonctionnement du bon cadeau qu'il
avait décrit en fin de troisième entretien :

> « Pour le bon cadeau, lors de l'achat, le montant est libre et non défini
> par un type de sortie et non dépendant d'un adulte ou d'un enfant. Il
> s'applique sur le montant total du panier. Si le bon est insuffisant pour
> l'intégralité du panier, le client paye le reste ; si le bon est supérieur
> au montant du panier, le surplus est perdu. »

Deux points supplémentaires sont tranchés au cours du même échange :
l'avoir est valable un an comme le bon cadeau, et une réservation porte sur
une seule sortie, sans regroupement de plusieurs sorties en une commande.

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la trace
de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-04/Q01` désigne la question 1 de ce
compte rendu. La numérotation est définitive — on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | **Bons cadeaux (résout CdC v3, §11 question 9) —** Le prix d'achat d'un bon cadeau correspond-il au tarif standard d'une sortie au moment de l'achat, ou l'acheteur choisit-il un montant libre ? | Montant libre, choisi par l'acheteur au moment de l'achat. |
| Q02 | **Bons cadeaux (revient sur `CR-03/Q07`) —** Le bon reste-t-il rattaché à un type de sortie déterminé à l'achat, et distingue-t-il un tarif adulte d'un tarif enfant ? | Non, ni l'un ni l'autre. Le bon n'est « pas défini par un type de sortie », ni « dépendant d'un adulte ou d'un enfant ». |
| Q03 | **Bons cadeaux —** Sur quoi le montant du bon s'impute-t-il exactement ? | Sur le montant total du panier. Si le bon est insuffisant, le client paie le reste ; s'il est supérieur, le surplus est perdu. Ces deux dernières règles étaient déjà acquises (`CR-03/Q07`) et ne changent pas. |
| Q04 | **Avoirs (revient sur `CR-03/Q05`) —** Un avoir a-t-il une durée de validité, comme le bon cadeau ? | Oui, un an, comme le bon cadeau. |
| Q05 | **Réservation —** Le mot « panier » désigne-t-il une commande pouvant regrouper plusieurs sorties, ou une réservation portant sur une seule sortie ? | Une réservation porte sur une seule sortie. Pas de panier regroupant plusieurs réservations. |

Une question posée et **restée sans réponse** figure quand même ici, avec
« sans réponse » : c'est une trace, et elle sert au §8. Ici, toutes les
questions ont reçu une réponse.

**Point à confirmer avant validation du compte rendu :** les réponses `Q04`
et `Q05` ont été rapportées à l'équipe sous la forme d'arbitrages (« on
tranche »). Si l'une des deux relève d'une décision d'équipe et non d'une
réponse du client, l'exigence correspondante doit être marquée `déduit` au
cahier des charges plutôt que sourcée `CR-04/Qnn`. Voir §6, ambiguïté 2.

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage : s'il
répond « non, pas tout à fait », la compréhension n'est pas acquise.

Le bon cadeau devient un simple porte-monnaie. À l'achat, l'acheteur ne
choisit plus qu'une chose, un montant. Les deux critères que le client avait
lui-même énoncés au troisième entretien, la spécificité par type de sortie
et, par extension, toute distinction adulte/enfant, disparaissent. À l'usage,
le code se déduit du montant total de la réservation, quel que soit le type
de sortie réservé et quelle que soit la composition du groupe. Le paiement du
solde par carte et la perte du surplus, eux, ne bougent pas.

C'est le premier changement du projet qui **retire** du périmètre. Il ferme
une question ouverte depuis le troisième entretien (le prix d'achat d'un bon,
`CdC v3, §11` question 9) et il invalide une exigence `Must` déjà
formalisée, `REQ-045`. L'équipe a choisi d'**inverser** cette exigence plutôt
que de la supprimer, en conservant son identifiant, pour que l'historique du
changement reste lisible dans le cahier des charges comme dans la matrice de
traçabilité.

La durée de validité de l'avoir est désormais fixée à un an. Ce point était
resté sans règle client au troisième entretien, et l'équipe avait
explicitement retenu l'hypothèse inverse (aucune expiration), au motif qu'un
avoir compense une sortie annulée par l'entreprise et qu'une péremption non
demandée serait défavorable au client. Le client la demande : l'hypothèse
tombe, et la spécification est reprise en conséquence.

Enfin, le mot « panier » employé par le client ne recouvre pas une commande
regroupant plusieurs sorties : une réservation reste attachée à une seule
sortie, ce qui confirme le modèle existant et évite d'introduire une classe
de commande au-dessus de `Réservation`.

**Conséquence non abordée avec le client.** En retirant au bon cadeau sa
spécificité de sortie et en donnant à l'avoir une expiration d'un an, ces
réponses rendent les deux dispositifs presque identiques : même nature de
code, montant libre, validité d'un an, usage unique, imputation sur le
montant total, surplus perdu. Il ne reste qu'une seule différence, leur
origine, le bon cadeau étant vendu et l'avoir accordé par le gérant. La
question 3 du §8 de `CR-03`, posée à l'époque comme une question de
vocabulaire, devient une décision de conception. Elle est reposée au §8.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
|---|---|---|
| Client / acheteur d'un bon cadeau | Choisit librement le montant du bon qu'il achète, sans désigner de sortie ni de catégorie de tarif | Q01, Q02 |
| Bénéficiaire d'un bon cadeau | Utilise le code sur n'importe quelle réservation, et paie le solde par carte si le montant total dépasse le bon | Q02, Q03 |
| Gérant / armateur Ti Baleine | Accorde un avoir après une annulation météo ; cet avoir est désormais périssable au bout d'un an | Q04 |

Aucune partie prenante nouvelle n'apparaît : cet échange précise des rôles
déjà identifiés en `CR-03`.

## 5. Règles métier découvertes

Rappel du statut de ce compte rendu : la colonne de formulation reproduit les
propos **tels que rapportés par l'équipe**, et non une transcription.

| # | Règle | Formulation rapportée du client | Sûre ? |
|---|---|---|---|
| 1 | Le montant d'un bon cadeau est libre, choisi par l'acheteur à l'achat | « lors de l'achat, le montant est libre » | oui |
| 2 | Un bon cadeau n'est rattaché ni à un type de sortie, ni à une catégorie de tarif adulte ou enfant — **annule la règle 7 de `CR-03`** | « non défini par un type de sortie et non dépendant d'un adulte ou d'un enfant » | oui |
| 3 | Un bon cadeau s'impute sur le montant total de la réservation | « Il s'applique sur le montant total du panier » | oui |
| 4 | Si le bon est insuffisant, le client paie le reste ; s'il est supérieur, le surplus est perdu — **confirme la règle 8 de `CR-03`** | « Si le bon est insuffisant pour l'intégralité du panier, le client paye le reste ; si le bon est supérieur au montant du panier, le surplus est perdu » | oui |
| 5 | Un avoir est valable un an, comme un bon cadeau — **infirme l'hypothèse d'équipe d'absence d'expiration** | « l'avoir n'est valide qu'un (1) an, comme le bon cadeau » | oui, sous réserve du §6 ambiguïté 2 |
| 6 | Une réservation porte sur une seule sortie ; il n'existe pas de commande regroupant plusieurs réservations | « une réservation = une sortie, pas de "panier" avec plusieurs réservations » | oui, sous réserve du §6 ambiguïté 2 |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « le montant est libre » (Q01) | (a) libre au sens strict, sans borne : un bon de 3 € comme un bon de 5 000 € est conforme (b) libre dans une plage raisonnable, avec un minimum et un maximum que le client n'a pas jugé utile d'énoncer | non — **hypothèse d'équipe : montant entier entre 10 € et 1 100 €**, borne haute alignée sur le forfait de privatisation le plus élevé (`REQ-014`), à confirmer ; ouverte en question 10 du §11 du cahier des charges |
| 2 | Origine des réponses `Q04` et `Q05` | (a) réponses du client, sourçables `CR-04/Q04` et `CR-04/Q05` (b) arbitrages de l'équipe pris en séance, qui devraient être marqués `déduit` au cahier des charges | non — lecture (a) retenue en attendant relecture par la personne ayant mené l'échange ; conditionne la source de `REQ-051` |
| 3 | « panier » (Q03, Q05) | (a) synonyme de réservation, employé par analogie avec le commerce en ligne (b) notion distincte, qui supposerait une classe de commande | oui — levée par la réponse à `Q05` : lecture (a), aucune classe ajoutée |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
|---|---|---|
| 1 | Retrait d'une exigence `Must` déjà formalisée et déjà descendue jusqu'à l'UML (`REQ-045`) : le changement impose de reprendre le cahier des charges, les spécifications, le diagramme de domaine et la matrice de traçabilité | méthode |
| 2 | Un avoir devient périssable, alors qu'il compense une annulation décidée par l'entreprise : règle défavorable au client final, assumée par le commanditaire | métier |
| 3 | Bon cadeau et avoir ne se distinguent plus que par leur origine, ce qui rend deux classes distinctes difficiles à justifier dans le modèle de données à venir | technique |

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.

| Priorité | Question | Pourquoi elle compte |
|---|---|---|
| 1 | Maintenant que le bon cadeau porte un montant libre, sans type de sortie, et que l'avoir expire lui aussi au bout d'un an, souhaitez-vous conserver deux dispositifs distincts, ou un mécanisme de code unique dont l'origine (acheté ou accordé) serait la seule différence ? | Reprend la question 3 du §8 de `CR-03`, devenue une décision de conception : elle détermine une ou deux classes dans le modèle de données, et donc une ou deux tables |
| 2 | Le montant d'un bon cadeau doit-il être borné (minimum, maximum) et arrondi à l'euro ? | Le formulaire d'achat n'est pas spécifiable sans borne ; voir §6, ambiguïté 1 |
| 3 | Lorsqu'une réservation payée avec un bon cadeau est annulée pour raison météo, que reçoit le client ? | Cas jamais envisagé par le client, resté en hypothèse d'équipe depuis `CR-03` (un avoir de montant équivalent) ; le point devient plus simple à trancher maintenant que les deux dispositifs ont les mêmes règles |
| 4 | Le client final doit-il être averti de l'expiration prochaine de son avoir ? | Nouvelle règle d'expiration sans règle d'information associée ; hypothèse d'équipe retenue : aucun rappel, la date figure sur le message qui communique le code |

Les questions 1 et 2 du §8 de `CR-03` (usage téléphonique exceptionnel d'un
bon cadeau, champs du formulaire de création d'un bateau) **n'ont pas été
reposées** lors de cet échange et restent ouvertes.

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que l'entretien n'a
pas touchés. C'est là que se cachent les découvertes tardives et coûteuses.

- La conséquence principale de la demande, la quasi-disparition de la
  différence entre bon cadeau et avoir, **n'a pas été signalée au client**
  pendant l'échange. C'est la question 1 du §8.
- Les bornes du montant d'un bon cadeau n'ont pas été demandées, alors que
  c'est la réponse du client lui-même qui ouvre le sujet.
- Le budget d'hébergement et le budget total du projet (`CdC`, §11
  question 2) n'ont toujours pas été abordés, pour le quatrième entretien
  consécutif.
- Le format de transmission de la facture (`CdC`, §11 question 3), la durée
  de conservation des données clients (question 4) et les modalités de
  connexion à l'espace de gestion (question 5) n'ont pas été reposés.
- La règle de modification d'un groupe après réservation (`CR-01`, §6
  ambiguïté 2, « à la discrétion de l'armateur ») **reste ouverte** : non
  reposée lors de ce quatrième échange non plus.
