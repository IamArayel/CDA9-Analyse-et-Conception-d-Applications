# Compte rendu d'entretien n° 1

**Date :** …
**Durée :** …
**Interlocuteur :** le commanditaire
**Présents pour l'équipe :** …

Rédigé le jour même. C'est la première trace du projet et la source du cahier des
charges.

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë — c'est
précisément l'ambiguïté qu'il faudra lever.

> « 2 bateaux : bateaux fond de verre pour voir le fond. Ti Cap 12 places, Le Grand
> Bleu 24 places. »
>
> « Au moins 2 personnes sur la résa. Min 6 personnes sinon sortie annulée. Un
> bateau est réservé avec un minimum de 2 personnes, mais si pas 6 personnes, pas
> de départ. »
>
> « Bateau entier bloqué sur créneaux (réservation privée). Privatisation = pas de
> tarif préférentiel. Forfait = 4 ou 12 personnes (la même chose). Suppléments
> pour champagne gérés par téléphone, uniquement par tél car personnalisable. »
>
> « 3 départs par jour : 7h, 10h, 14h, sortie de ~3h. Saison baleines du 15 juin
> au 31 octobre, sinon sortie "cétacés dauphins" sur les mêmes créneaux. »
>
> « Une sortie dure 2h30 (baleines), 2h (dauphins). 30 min à 1h de préparation du
> bateau entre deux sorties. Privatisation = une demi-journée, souvent l'après-midi
> pour le coucher de soleil. »
>
> « Révision du prix chaque année, à définir en backoffice par l'armateur. »
>
> « Pour la réservation en ligne d'un groupe, on affiche les places disponibles
> pour chaque bateau, le client choisit son bateau, mais la modification reste à
> la discrétion de l'armateur. »
>
> « Réservation en ligne possible jusqu'à 12h avant le départ si départ l'après-midi ;
> si départ le lendemain, jusqu'à 12h la veille. »
>
> « Paiement de la totalité à la réservation, sur le site. Pas de prestataire
> actuellement, paiement uniquement en carte avec le TPE de la banque. En ligne
> par CB uniquement — pas d'espèces, pas de virement, pas de chèque. »
>
> « Annulation possible jusqu'au départ, remboursement sur CB. Plus de 7 jours
> avant : remboursement total. Entre 7 jours et 48h : 25% de commission gardée.
> Entre 48h et 24h : 50% de commission gardée. Est-ce qu'un client peut reporter
> sa résa même 24h avant ? »

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la trace
de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-01/Q07` désigne la question 7 de ce
compte rendu. La numérotation est définitive — on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | Composition de la flotte : combien de bateaux, quelles caractéristiques (nom, type, modèle) ? | 2 bateaux à fond de verre : Ti Cap (12 places) et Le Grand Bleu (24 places). |
| Q02 | Capacités : capacité max par bateau, jauge minimale pour maintenir un départ ? | Capacité max = 12 et 24 places selon le bateau. Chaque réservation ≥ 2 personnes. Sortie annulée si moins de 6 personnes au total sur le créneau. |
| Q03 | Certains bateaux sont-ils réservés à des types de sorties particulières (privatisation, VIP…) ? | Un bateau entier peut être bloqué sur un créneau pour une réservation privée. |
| Q04 | Types de prestations : différentes formules proposées ? | Privatisation sans tarif préférentiel. Forfait 4 ou 12 personnes (la même chose). Supplément champagne géré uniquement par téléphone. |
| Q05 | Planning quotidien : créneaux fixes ? varient selon saison/jour ? | 3 départs/jour : 7h, 10h, 14h, toute l'année. Sortie baleines du 15 juin au 31 octobre, sinon sortie dauphins, mêmes créneaux. |
| Q06 | Délai d'escale/nettoyage nécessaire entre deux sorties pour un même bateau ? | Sortie de 2h30 (baleines) ou 2h (dauphins), + 30 min à 1h de préparation. Privatisation = une demi-journée. |
| Q07 | Grille tarifaire : tarifs adulte/enfant/bébé/groupe/privatisation ? | Révisée chaque année, définie en backoffice par l'armateur — montants non communiqués. |
| Q08 | Contraintes de groupe : taille max en ligne ? gestion des modifications après réservation ? | Le client choisit son bateau selon les places affichées disponibles. Modification à la discrétion de l'armateur — sans réponse sur les détails. |
| Q09 | Délai de réservation : jusqu'à combien de temps avant le départ ? | Jusqu'à 12h avant le départ (après-midi), ou 12h la veille si départ le lendemain. |
| Q10 | Mode de paiement : totalité à la commande ou acompte ? | Totalité au moment de la réservation, sur le site. |
| Q11 | Solutions de paiement : contrat monétique/banque ou prestataire (Stripe, PayPal) ? | Aucun prestataire actuel. Paiement carte via le TPE de la banque de l'armateur. |
| Q12 | Paiement full en ligne ou une partie en espèces ? | Full carte bancaire en ligne — pas d'espèces, virement ni chèque. |
| Q13 | Politique d'annulation client : conditions de remboursement ? | Remboursement sur CB. >7j : 100%. 7j-48h : 75% (25% retenus). 48h-24h : 50% (50% retenus). Moins de 24h : sans réponse. |
| Q14 | Un client peut-il reporter sa réservation même à moins de 24h du départ ? | Sans réponse. |

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage : s'il
répond « non, pas tout à fait », la compréhension n'est pas acquise.

L'entreprise exploite deux bateaux à fond de verre de capacités différentes (12
et 24 places), sur trois créneaux fixes identiques toute l'année (7h, 10h, 14h).
La nature de la sortie change selon la saison (baleines en saison, dauphins hors
saison) mais pas les horaires. Une réservation individuelle doit compter au moins
2 personnes, mais la sortie n'est confirmée que si le cumul des réservations sur
le créneau atteint 6 personnes ; en dessous, elle est annulée. Un bateau peut
aussi être entièrement privatisé sur un créneau, sans réduction de tarif. Les
tarifs eux-mêmes ne sont pas fixes dans le système : ils sont saisis et révisés
chaque année par l'armateur via un backoffice. Le paiement en ligne se fait
uniquement par carte bancaire, en totalité, au moment de la réservation, via le
terminal de la banque de l'armateur (aucun prestataire tiers). L'annulation suit
un barème dégressif jusqu'à 48h avant le départ, mais ce qui se passe en dessous
de 24h — remboursement, report, ou rien — n'est pas encore défini.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
|---|---|---|
| L'armateur (commanditaire) | Fixe et révise les tarifs, décide des modifications de réservation | Réponses sur la grille tarifaire (Q07) et les contraintes de groupe (Q08) |
| Client réservant en ligne | Réserve et paie en totalité par carte bancaire | Brief initial + réponses sur le paiement (Q10-Q12) |
| Client demandant un supplément personnalisé (champagne) | Contacte par téléphone, hors circuit en ligne | Réponse sur les types de prestations (Q04) |
| Banque de l'armateur (TPE) | Fournit le terminal de paiement carte utilisé pour le site | Réponse sur les solutions de paiement (Q11) |

## 5. Règles métier découvertes

| # | Règle | Formulation exacte du client | Sûre ? |
|---|---|---|---|
| 1 | Une réservation doit compter au moins 2 personnes | « Au moins 2 personnes sur la résa » | oui |
| 2 | Une sortie est annulée si le total des passagers sur le créneau est inférieur à 6 | « min 6 personnes sinon sortie annulée » | à confirmer (lien avec la règle 1, cf. §6) |
| 3 | Un bateau peut être bloqué entièrement pour une réservation privée sur un créneau | « Bateau entier bloqué sur créneaux (réservation privée) » | oui |
| 4 | La privatisation ne bénéficie d'aucun tarif préférentiel | « Privatisation = pas de tarif préférentiel » | oui |
| 5 | Le supplément champagne est géré uniquement par téléphone | « suppléments pour champagne géré par téléphone (uniquement par tel car personnalisable) » | oui |
| 6 | 3 départs fixes par jour, toute l'année : 7h, 10h, 14h | « 3 départs / jours → 7h, 10H, 14H » | oui |
| 7 | La sortie proposée dépend de la saison : baleines du 15/06 au 31/10, dauphins le reste de l'année, mêmes créneaux | « Baleine du 15 juin au 31 octobre. Sinon sortie "Cétacé dauphins" (même créneaux) » | oui |
| 8 | Le paiement s'effectue en totalité au moment de la réservation en ligne | « Paiement totalité à la réservation sur le site » | oui |
| 9 | Seul le paiement par carte bancaire est accepté en ligne | « en ligne CB pas espèce pas virement pas chèque full carte bleu site » | oui |
| 10 | Barème de remboursement : 100% si annulation à plus de 7j, 75% entre 7j et 48h, 50% entre 48h et 24h | « 7j = remboursement full / 7j à 48h = 25% de commission gardée / 48h à 24H = 50% de commission gardée » | à confirmer (palier < 24h non précisé) |
| 11 | La réservation en ligne doit être faite au moins 12h avant le départ | « jusqu'à 12H avant le départ » | à confirmer (formulation ambiguë, cf. §6) |
| 12 | Les tarifs sont définis et révisés chaque année par l'armateur via un backoffice | « Révision chaque année du prix → définir tarif backoffice par l'armateur » | oui |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « … » | (a) … (b) … | non |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
|---|---|---|

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.

| Priorité | Question | Pourquoi elle compte |
|---|---|---|
| 1 | … | … |

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que l'entretien n'a
pas touchés. C'est là que se cachent les découvertes tardives et coûteuses.

- …
