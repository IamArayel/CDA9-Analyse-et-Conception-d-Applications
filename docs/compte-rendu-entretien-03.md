# Compte rendu d'entretien n° 3

**Date :** 2026-08-12
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …
**Source brute :** Notion, « J3 - Entretien n°3 avec le client »

> Ce troisième rendez-vous reprend les dernières questions restées ouvertes
> après [`compte-rendu-entretien-02.md`](./compte-rendu-entretien-02.md)
> (jours de fermeture, langues, contenu du message de rappel, taille
> minimale d'une réservation, mécanique de l'avoir, évolution de la flotte),
> et se termine par une contrainte nouvelle apportée spontanément par le
> client : la vente de bons cadeaux.

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë — c'est
précisément l'ambiguïté qu'il faudra lever.

Contrairement au premier rendez-vous, celui-ci n'a pas commencé par une
déclaration liminaire : comme au deuxième rendez-vous, il s'agit d'un
passage en revue des questions restées ouvertes ([CdC v2, §11](./cahier-des-charges.md#11-questions-restées-ouvertes)).
En fin d'entretien, le client a introduit de lui-même un sujet non prévu à
la trame :

> « J'ai décidé de vendre des bons cadeaux (code utilisable à n'importe
> quel moment sur le site), à Noël la personne réserve plus tard avec son
> bon cadeau, réservation uniquement sur la plateforme, usage unique. »

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la trace
de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-03/Q07` désigne la question 7 de ce
compte rendu. La numérotation est définitive — on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | **Exploitation (résout CdC v2, §11 question 3) —** Existe-t-il des jours où l'entreprise est fermée ? Si oui, souhaitez-vous gérer les horaires sur l'interface admin ? | Fermée le 25 décembre et le 1ᵉʳ janvier. Le client souhaite en plus une section dédiée, dans l'espace de gestion, pour modifier les horaires d'ouverture et de fermeture. |
| Q02 | **Langues (résout CdC v2, §11 question 5) —** Quelles langues sur le site ? | Anglais et français. |
| Q03 | **Communication (résout CdC v2, §11 question 2) —** Quel est le contenu exact du message à envoyer aux clients un jour avant la sortie ? Message général qu'il serait possible d'automatiser via l'application, ou message personnalisé géré par l'entreprise ? | Un message type, avec un horaire d'envoi personnalisable. Le client préfère que l'automatisation se fasse directement via le site. |
| Q04 | **Réservation —** Est-ce qu'une seule personne peut réserver un créneau, ou faut-il être au minimum 2 ? (exemple donné : le bateau de 12 personnes a déjà 7 places prises, un client réserve une place seule — est-ce que cela fonctionne ?) | Une personne seule peut réserver une seule place, ou en réserver plusieurs. |
| Q05 | **Avoirs —** Comment gérez-vous les avoirs aujourd'hui ? | Un avoir prend la forme d'un code de « réduction » à renseigner au moment du paiement. Le client souhaite qu'une option de coupon de réduction (code unique) soit proposée sur le site. |
| Q06 | **Flotte —** Si vous disposez un jour d'un nouveau bateau, souhaitez-vous pouvoir le créer vous-même depuis votre espace admin, pour qu'il apparaisse sur l'interface ? | Oui. |
| Q07 | **Contrainte formulée spontanément par le client, en fin d'entretien, hors trame prévue —** *(aucune question posée : le client introduit le sujet de lui-même)* | Le client a décidé de vendre des bons cadeaux : code utilisable à n'importe quel moment sur le site (ex. offert à Noël, la personne réserve plus tard avec son bon cadeau) ; réservation uniquement sur la plateforme ; usage unique ; validité d'un an à compter de l'achat ; code à renseigner au moment de la réservation ; bon spécifique à un type de sortie (baleines, dauphins ou privatisation) ; si le prix de la sortie est supérieur au bon, le client final paie la différence ; si le bon est supérieur au prix, le surplus est perdu, sans remboursement. |

Une question posée et **restée sans réponse** figure quand même ici, avec
« sans réponse » : c'est une trace, et elle sert au §8. Ici, toutes les
questions ont reçu une réponse.

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage : s'il
répond « non, pas tout à fait », la compréhension n'est pas acquise.

Trois questions restées ouvertes depuis le deuxième rendez-vous sont
désormais tranchées. L'entreprise ferme deux jours fixes dans l'année (25
décembre, 1ᵉʳ janvier), et le gérant veut piloter lui-même ces dates ainsi
que les horaires d'ouverture depuis son espace de gestion — ce qui élargit
légèrement le périmètre du back-office, jusqu'ici limité aux tarifs et à
l'export du planning (`CR-02/Q03`). Le site sera bilingue français/anglais,
ce qui infirme l'hypothèse par défaut retenue jusqu'ici (français
uniquement, `REQ-102`). Le message de rappel à J-1 suit un texte type dont
l'horaire d'envoi est réglable par le gérant, avec une automatisation
pilotée par le site plutôt qu'un envoi manuel.

Une clarification corrige une exigence déjà formalisée : `REQ-001`
retenait « à partir de 2 personnes » comme seuil minimal de réservation, à
partir de la réponse de `CR-01/Q02` (« une réservation peut être prise à
partir de 2 personnes »). Reposée avec un exemple concret (une place
isolée restant sur un bateau presque complet), la réponse du client est
sans ambiguïté : une personne seule peut réserver une place unique. La
lecture initiale de `CR-01/Q02` était donc trop stricte ; le seuil de 6
personnes (`REQ-002`) reste, lui, inchangé — c'est un seuil de maintien de
la sortie entière, pas de taille minimale d'une réservation individuelle.

Le mécanisme de l'avoir, jusque-là seulement décrit comme un « choix »
enregistré par le gérant après une annulation météo (`REQ-023`), est
maintenant précisé techniquement : c'est un code de réduction unique,
saisi par le client au moment de payer une réservation future — un
mécanisme que le client veut voir apparaître comme une option de coupon
sur le site. L'équipe retient ce code d'avoir comme un dispositif distinct
du bon cadeau (§6, ambiguïté 3) : sa valeur est décidée au cas par cas par
le gérant, sans montant fixe ni durée de validité imposée par une règle
client, contrairement au bon cadeau.

Sur la flotte, le client confirme vouloir, à terme, ajouter un bateau
depuis son espace de gestion pour qu'il apparaisse immédiatement côté
client — ce qui nuance `REQ-030`/`REQ-033`, qui excluaient jusqu'ici toute
gestion de la flotte depuis le back-office. Il ne s'agit pas d'un besoin
immédiat (aucun nouveau bateau n'existe à ce jour), mais d'une capacité
que le client veut anticiper. L'équipe retient un formulaire de création
limité aux deux informations déjà connues pour un bateau (nom, capacité —
§6, ambiguïté 2), sans champ supplémentaire pour restreindre les types de
sorties compatibles, faute d'avoir cette information pour les deux bateaux
existants eux-mêmes.

Enfin, une contrainte nouvelle et non anticipée apparaît en fin
d'entretien : la vente de bons cadeaux. Le client en a une idée précise et
déjà chiffrée dans ses règles (validité 1 an, spécificité par type de
sortie, usage unique, paiement de la différence ou perte du surplus), mais
c'est un sujet qui n'existait dans aucun cahier des charges antérieur —
voir l'analyse d'impact dédiée,
[`impact-CR-001.md`](./impact-CR-001.md). L'équipe retient la lecture la
plus stricte de « réservation uniquement sur la plateforme » (§6,
ambiguïté 1) : ni l'achat ni l'usage d'un bon cadeau ne passent par le
téléphone, contrairement au reste des réservations où le gérant garde la
main — cohérent avec le fait que le bon cadeau est un moyen de paiement en
ligne assimilable à un code de réduction, non une réservation classique
négociée avec le gérant.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
|---|---|---|
| Gérant / armateur Ti Baleine | Définit les jours et horaires de fermeture, personnalise l'horaire du message de rappel, pourra créer un nouveau bateau depuis l'espace de gestion | Q01, Q03, Q06 |
| Client / passager | Peut réserver seul, pour une place unique ; peut acheter un bon cadeau ou utiliser un code d'avoir au paiement | Q04, Q05, Q07 |
| Bénéficiaire d'un bon cadeau | Reçoit un bon cadeau (ex. à Noël) et réserve plus tard, seul, avec le code ; peut être une personne distincte de l'acheteur du bon | Q07 |

## 5. Règles métier découvertes

| # | Règle | Formulation exacte du client | Sûre ? |
|---|---|---|---|
| 1 | L'entreprise est fermée le 25 décembre et le 1ᵉʳ janvier ; ces dates et les horaires d'ouverture sont modifiables depuis le dashboard | « Fermée le 25 déc et 1er Janvier + section pour modifier les horaires d'ouverture et fermeture sur dashboard » | oui |
| 2 | Le site est disponible en français et en anglais | « Anglais Français » | oui |
| 3 | Le message de rappel suit un texte type, avec un horaire d'envoi personnalisable ; l'automatisation se fait via le site | « message type + horaire personnalisable, automatisation via le site serait le mieux » | oui |
| 4 | Une réservation peut être prise pour une seule personne (une place) ou pour plusieurs — corrige la lecture retenue de `CR-01/Q02` | « 1 personne peut réserver une seule place ou pour plusieurs » | oui |
| 5 | Un avoir est délivré sous forme de code de réduction unique, saisi au moment du paiement | « Un avoir est sous forme de code de "réduction" à renseigner lors du paiement » | oui |
| 6 | Le gérant pourra créer un nouveau bateau depuis l'espace de gestion pour qu'il apparaisse sur l'interface, si la flotte évolue | « Oui » (réponse directe à la question posée) | oui, mais capacité anticipée, sans besoin immédiat (aucun nouveau bateau à ce jour) |
| 7 | Un bon cadeau est utilisable à tout moment, uniquement via une réservation sur la plateforme, à usage unique, valable 1 an à compter de l'achat, spécifique à un type de sortie (baleines, dauphins ou privatisation) | « bon cadeau (code utilisable à n'importe quel moment sur le site) […] réservation uniquement sur la plateforme, usage unique […] durée de validité est de 1 an à partir de l'achat […] Le bon cadeau est spécifique (pour sortie baleine, sortie dauphin, ou privatisation) » | oui |
| 8 | Si le prix de la sortie dépasse le montant du bon cadeau, le client final paie la différence ; si le bon dépasse le prix, le surplus est perdu, sans remboursement | « Le client final a la possibilité de l'utiliser et de payer la différence si le prix final est supérieur au bon. Si le bon est supérieur au prix de la sortie, pas de remboursement de la différence, le surplus est perdu » | oui |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8. Les
trois ambiguïtés ci-dessous ont été tranchées par une **hypothèse
d'équipe**, faute d'avoir pu reposer la question directement au client
pendant cet entretien — à confirmer explicitement au prochain passage
(§8), et donc marquées `déduit` dans le cahier des charges plutôt que
sourcées `CR-03/Qnn`.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « réservation uniquement sur la plateforme » à propos du bon cadeau (Q07) | (a) le bon cadeau lui-même n'est vendu que sur le site, mais son usage suit les mêmes règles que toute réservation, y compris téléphonique si le gérant l'accepte au cas par cas (b) l'achat *et* l'usage du bon cadeau passent exclusivement par la plateforme, sans aucune exception téléphonique, contrairement au reste des réservations où le gérant garde la main | oui — **hypothèse d'équipe : lecture (b) retenue**, à confirmer avec le client |
| 2 | « qui apparaisse sur l'interface » à propos d'un nouveau bateau créé depuis l'espace de gestion (Q06) | (a) le gérant saisit uniquement nom et capacité, comme les deux bateaux actuels (b) le gérant doit aussi pouvoir définir le type de sorties compatibles (ex. un bateau non habilité aux sorties baleines) | oui — **hypothèse d'équipe : lecture (a) retenue**, à confirmer avec le client |
| 3 | « code de réduction » pour l'avoir (Q05) vs. « code » pour le bon cadeau (Q07) | (a) il s'agit de deux mécanismes distincts, avec deux types de codes différents (un avoir a une valeur libre décidée par le gérant au cas par cas, un bon cadeau a un montant fixé à l'achat et une spécificité de sortie) (b) le client envisage un mécanisme de code unique commun aux deux usages | oui — **hypothèse d'équipe : lecture (a) retenue**, à confirmer avec le client |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
|---|---|---|
| 1 | Fermeture fixe deux jours par an (25 décembre, 1ᵉʳ janvier), modifiable depuis le back-office | métier |
| 2 | Site à livrer en deux langues (français, anglais), alors que l'hypothèse par défaut retenue jusqu'ici était le français seul | technique |
| 3 | Vente de bons cadeaux à intégrer, avec gestion de codes uniques, d'une durée de validité et d'une spécificité par type de sortie — sujet non anticipé dans la conception initiale (UML, modèle de données) | technique |

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.
Les trois premières confirment (ou infirment) les hypothèses d'équipe
retenues au §6 ; elles restent prioritaires même si le travail de
conception avance sur ces hypothèses en attendant.

| Priorité | Question | Pourquoi elle compte |
|---|---|---|
| 1 | Confirmez-vous qu'un bon cadeau ne peut être ni acheté ni utilisé par téléphone, y compris pour le gérant lui-même, même à titre exceptionnel ? | Confirme l'hypothèse d'équipe retenue en §6, ambiguïté 1 ; conditionne si une saisie manuelle du gérant doit exister en complément du parcours client |
| 2 | Lors de la création d'un nouveau bateau depuis l'espace de gestion, confirmez-vous qu'il suffit de saisir un nom et une capacité, sans indiquer les types de sorties compatibles ? | Confirme l'hypothèse d'équipe retenue en §6, ambiguïté 2 ; impacte le modèle de données de `Bateau` et le formulaire de création |
| 3 | Confirmez-vous que l'avoir (valeur libre décidée par vous au cas par cas) et le bon cadeau (montant fixé à l'achat, spécifique à un type de sortie) sont bien deux dispositifs séparés, et non un mécanisme de code unique ? | Confirme l'hypothèse d'équipe retenue en §6, ambiguïté 3 ; conditionne un modèle de données unique ou deux modèles distincts |
| 4 | Le prix d'achat d'un bon cadeau correspond-il toujours au tarif standard d'une sortie au moment de l'achat, ou le client peut-il choisir un montant libre ? | Nécessaire pour spécifier le formulaire d'achat d'un bon cadeau |

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que l'entretien n'a
pas touchés. C'est là que se cachent les découvertes tardives et coûteuses.

- La formalisation de la règle de modification de groupe après réservation
  (CR-01, ambiguïté §6 n°2, « à la discrétion de l'armateur ») **reste
  ouverte** : non reposée lors de ce troisième rendez-vous non plus.
- Le budget d'hébergement et le budget total du projet ([CdC v2, §11,
  question 4]) n'ont toujours pas été abordés.
- Le format de transmission de la facture ([CdC v2, §11, question 6]) et la
  durée de conservation des données clients ([CdC v2, §11, question 7])
  n'ont pas été reposés.
- Le prix d'achat d'un bon cadeau (montant libre ou aligné sur le tarif
  d'une sortie) n'a pas été précisé — voir §8, question 4.
