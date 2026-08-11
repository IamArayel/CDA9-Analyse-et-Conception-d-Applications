# Compte rendu d'entretien n° 2 — synthèse des points 5 à 8

**Date :** …
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …

> Ce document reprend, au format du gabarit `compte-rendu-entretien-01.md`, les
> réponses obtenues lors du **second** rendez-vous : les points 5 à 8 de la
> trame « Questions au client » (météo/annulations armateur, communication,
> exploitation quotidienne, intégrations techniques), envoyés par e-mail après
> le premier rendez-vous (voir
> [`compte-rendu-entretien-01-points-1-4.md`](./compte-rendu-entretien-01-points-1-4.md)),
> ainsi que trois questions complémentaires posées après coup sur les points
> 1 à 4 (privatisation le matin, tranche d'âge « enfant », fonctions du
> back-office).

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë — c'est
précisément l'ambiguïté qu'il faudra lever.

Contrairement au premier rendez-vous, celui-ci n'a pas commencé par une
déclaration liminaire : il s'agit d'un passage en revue des questions restées
ouvertes. Deux formulations résument bien sa posture :

> « Il doit être maître de l'annulation. »
>
> « Répartition [du] nombre[s] de personnes sur bateau : réponse floue. »

La seconde n'est pas une citation du client mais une note prise pendant
l'entretien, qui documente elle-même que la réponse obtenue n'est pas
exploitable en l'état — voir Q10 et l'ambiguïté 1 au §6.

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la trace
de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-02/Q07` désigne la question 7 de ce
compte rendu. La numérotation est définitive — on n'insère pas, on ajoute à la
suite.

| ID | Question posée | Réponse |
| --- | --- | --- |
| Q01 | **Gestion des durées (complément CR-01/Q06) —** La privatisation est-elle possible aussi le matin, ou uniquement l'après-midi ? | Oui : possible de privatiser le matin (brunch) et aussi au sunset. |
| Q02 | **Grille tarifaire (complément CR-01/Q07) —** Quelle est la tranche d'âge d'un « enfant » ? | Sortie interdite avant 4 ans ; tarif enfant de 4 à 11 ans ; tarif adulte à partir de 12 ans. *(il n'existe donc pas de tarif « bébé » : l'accès est tout simplement interdit en dessous de 4 ans — répond à CR-01/Q07)* |
| Q03 | **Back-office (complément CR-01/Q08) —** Quelles fonctions/actions sont nécessaires (export PDF, annulation, modification des créneaux, tarifs, noms/nombre des bateaux, message d'accueil) ? Qui y a accès, et qui a le droit de modifier quelles informations ? | Un onglet de modification des tarifs **seulement** ; export du planning des réservations en PDF, imprimable ; pas de modification possible des messages de la page d'accueil ni d'autre contenu. Accès réservé à un unique compte ADMIN. *(voir tension avec US12/US13 au §8)* |
| Q04 | **Météo —** Qui prend la décision d'annuler un créneau, et comment s'effectue le choix entre report de date et remboursement intégral (annulation par téléphone uniquement, SMS ou WhatsApp) ? | La décision vient du gérant : il appelle le client, qui choisit entre report, avoir ou remboursement. L'annulation passe par téléphone (appel ou SMS) ; prévoir aussi un e-mail sur la plateforme, pour pouvoir annuler par mail au besoin. *(canal e-mail formulé de façon imprécise — voir ambiguïté 4)* |
| Q05 | **Météo —** Souhaitez-vous déclencher l'annulation d'un créneau en un clic et informer automatiquement tous les passagers impactés ? | Le gérant veut un visuel sur l'annulation à valider : il tient à rester maître de la décision, pas d'automatisation intégrale. *(en tension avec US09 — voir §8)* |
| Q06 | **Météo —** Comment le client doit-il pouvoir choisir un nouveau créneau en cas d'annulation météo (lien dédié, avoir, contact direct) ? | Proposition d'un nouveau créneau en fonction des disponibilités et de la météo, **par téléphone**. *(en tension avec US11 — voir §8)* |
| Q07 | **Communication —** Souhaitez-vous conserver WhatsApp en parallèle des e-mails/SMS pour les confirmations, rappels et alertes météo ? | WhatsApp est conservé, mais en plus de la nouvelle application (pas en remplacement). |
| Q08 | **Communication —** À quel moment envoyer un rappel de sortie aux clients ? | Message 24h avant la sortie, avec les conditions météo et la liste des affaires à prévoir. |
| Q09 | **Communication —** Comment les clients sont-ils avertis d'une annulation ou d'une modification ? | Par appel téléphonique. *(à recouper avec Q04/Q05 — voir §8)* |
| Q10 | **Exploitation —** Qui va manipuler l'outil au quotidien ? Plusieurs types de compte ? Qui répartit les passagers entre les bateaux, et comment ? | Uniquement le gérant utilise l'outil. Un seul naturaliste est disponible, donc un seul bateau est dédié aux sorties baleines à la fois ; les deux bateaux peuvent en revanche être utilisés pour les sorties dauphins. **La règle de répartition des passagers entre bateaux reste floue** — réponse explicitement signalée comme imprécise par le client lui-même. |
| Q11 | **Exploitation —** Avez-vous besoin d'une vue « Planning du jour » imprimable ou consultable sur mobile ? | Un planning papier suffit largement, avec un planning matinée et un planning après-midi séparés. *(portée exacte du besoin numérique à clarifier — voir §8)* |
| Q12 | **Exploitation —** Devez-vous pouvoir ajouter manuellement des réservations prises sur place ou au téléphone ? | Toutes les réservations passent par la plateforme. *(formulation à deux lectures — voir ambiguïté 2)* |
| Q13 | **Intégrations —** Avez-vous déjà un site web sur lequel intégrer le module de réservation ? | Aucun site actuellement, seulement une page Facebook — dont la mise à jour reste à vérifier. |
| Q14 | **Intégrations —** Utilisez-vous un logiciel comptable ou de caisse particulier ? | Aucun logiciel : comptabilité « à l'ancienne », tenue de caisse manuelle. |
| Q15 | **Divers —** Le client a-t-il une charte graphique définie ? | Il dispose d'un logo, mais pas de charte graphique définie. |
| Q16 | **Divers (résout CR-01, ambiguïté §6 n°1) —** À quel moment le seuil de jauge minimale (6 personnes) est-il vérifié ? | Contrôle 24h avant le départ ; si moins de 6 personnes, la sortie est annulée et les clients sont remboursés. *(voir l'incohérence avec CR-01/Q09 — fermeture des réservations à midi, et non à J-12h — au §6)* |
| Q17 | **Divers (résout CR-01, ambiguïté §6 n°4) —** Un client peut-il reporter sa réservation à moins de 24h du départ ? | Oui, sous réserve de disponibilité. |
| Q18 | **Divers —** Faut-il demander des informations particulières aux clients lors de la réservation ? | Non, aucune information particulière à demander. |
| Q19 | **Paiement (complément CR-01/Q11) —** Entre Stripe, PayPal et l'offre de paiement en ligne de votre banque, le Crédit Agricole, lequel souhaitez-vous retenir comme prestataire de paiement ? | Le moins cher des trois sera retenu ; pas de préférence de principe entre Stripe, PayPal et le Crédit Agricole. |

Une question posée et **restée sans réponse** figure quand même ici, avec
« sans réponse » : c'est une trace, et elle sert au §8. Ici, toutes les
questions ont reçu une réponse, mais certaines restent imprécises ou
partielles (Q10, Q12) — voir §6.

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage : s'il
répond « non, pas tout à fait », la compréhension n'est pas acquise.

Sur les aléas météo, le gérant ne veut pas d'un système entièrement
automatisé : il décide seul, appelle le client concerné, et propose report,
avoir ou remboursement. Le système doit donc l'aider à visualiser et valider
une annulation, pas la déclencher tout seul — ce qui nuance fortement les user
stories rédigées avant cet entretien (US09, US10, US11), écrites en supposant
un parcours plus automatisé et plus autonome pour le client.

Côté exploitation, l'outil sera manipulé par une seule personne (le gérant),
avec un accès administrateur unique ; le planning papier suffit à son usage
actuel, et toutes les réservations doivent transiter par la plateforme — sans
que l'on sache encore si cela exclut les réservations téléphoniques ou si
cela signifie qu'elles y seront simplement ressaisies.

Une contrainte structurante apparaît pour la première fois : un seul
naturaliste est disponible, donc une seule sortie baleines peut avoir lieu à
la fois, alors que les deux bateaux peuvent être mobilisés en parallèle pour
les sorties dauphins. Cette règle doit être modélisée au même titre que les
capacités des bateaux.

Enfin, deux ambiguïtés ouvertes au premier rendez-vous sont levées : le
contrôle de jauge a lieu à J-24h (et non à une date indéterminée), et un
client peut reporter sa réservation même dans les dernières 24h, sous réserve
de disponibilité. Ces deux réponses, mises côte à côte avec ce qui était déjà
consigné, font toutefois apparaître une nouvelle incohérence de calendrier —
voir §6.

Sur le paiement, le client a précisé le critère resté ouvert au premier
rendez-vous (CR-01/Q11) : il ne fixe pas par avance un prestataire de
paiement en ligne, mais retiendra celui qui revient le moins cher, en
comparant Stripe, PayPal et l'offre de paiement en ligne de sa banque, le
Crédit Agricole.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
| --- | --- | --- |
| Gérant / armateur Ti Baleine | Seul utilisateur de l'outil ; décide seul des annulations météo, valide les reports, gère les tarifs en back-office | Q04, Q05, Q10 |
| Naturaliste | Présence obligatoire à bord pour les sorties baleines ; un seul disponible, ce qui limite à un bateau à la fois sur ce type de sortie | Q10 |
| Client / passager | Réserve et paie en ligne ; peut être recontacté par téléphone en cas d'annulation météo ; peut reporter sa réservation même à J-24h | Q04, Q06, Q17 |

## 5. Règles métier découvertes

| # | Règle | Formulation exacte du client | Sûre ? |
| --- | --- | --- | --- |
| 1 | La privatisation peut être réservée le matin (brunch) ou l'après-midi (sunset) | « possible de privatiser le matin ( brunch) et aussi sunset » | oui — lève CR-01, ambiguïté §6 n°3 |
| 2 | Barème d'âge des tarifs : accès interdit avant 4 ans, tarif enfant de 4 à 11 ans, tarif adulte à partir de 12 ans | « avant 4 ans sortie interdite, 4 à 11 ans = tarif enfant, 12+ ans = tarif adulte » | oui — répond à CR-01/Q07 (tarif bébé sans objet) |
| 3 | Le back-office ne permet que la modification des tarifs, l'export du planning en PDF et l'impression ; ni la page d'accueil ni le contenu ne sont éditables ; accès limité à un compte ADMIN unique | « onglet modification de tarifs seulement […] pas de modifications de messages pour la page d'accueil ou autres […] Uniquement un compte ADMIN » | oui, mais **restreint fortement le périmètre supposé par US12/US13** — voir §8 |
| 4 | Une seule sortie baleines peut avoir lieu à la fois (un seul naturaliste disponible) ; les deux bateaux sont mobilisables pour les sorties dauphins | « 1 seul Naturaliste donc qu'un seul bateau dédié aux baleines […] dauphins les 2 bateaux » | oui — règle structurante, non modélisée jusqu'ici |
| 5 | Le contrôle de la jauge minimale (6 personnes) a lieu à J-24h ; en dessous, annulation et remboursement | « 24H avant contrôle du nombre de personnes, si - de 6 personnes = pas de sortie (annulation et remboursement) » | oui — lève CR-01, ambiguïté §6 n°1 ; voir cependant l'incohérence au §6 |
| 6 | Un client peut reporter sa réservation même à moins de 24h du départ, sous réserve de disponibilité | « Le client peut reporter sa résa même 24H avant, sous réserve de disponibilité » | oui — lève CR-01, ambiguïté §6 n°4 |
| 7 | Le gérant valide chaque annulation météo lui-même ; pas de déclenchement automatique en un clic | « il doit être maitre de l'annulation » | oui, mais **contredit la formulation initiale de US09** — voir §8 |
| 8 | Le prestataire de paiement en ligne est choisi selon le critère du coût le plus bas, en comparant Stripe, PayPal et l'offre en ligne du Crédit Agricole | « le moins cher entre Stripe, Paypal ou mon Crédit Agricole » | oui — répond à CR-01/Q11 |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
| --- | --- | --- | --- |
| 1 | « Répartition nombres de personnes sur bateau : réponse flou » (Q10) | (a) aucune règle n'existe encore, le gérant décide au cas par cas (b) une règle proche de l'exemple proposé pendant l'entretien (prioriser le 12 places, basculer sur le 24, cumuler les deux) est pressentie mais non validée | non |
| 2 | « toutes les réservations se passent sur la plateforme » (Q12) | (a) plus aucune réservation téléphonique ne sera acceptée une fois l'outil en service (b) les réservations prises par téléphone sont ensuite ressaisies manuellement par le gérant dans l'outil, ce qui confirmerait le besoin décrit en US06 | non |
| 3 | Incohérence potentielle entre deux réponses : le contrôle de jauge a lieu à **J-24h**, une durée fixe avant le départ (Q16), alors que les réservations en ligne ferment à **midi**, une heure fixe (et non une durée) — la veille pour un départ du matin, le jour même pour un départ l'après-midi (CR-01/Q09) | (a) pour un départ du matin, l'écart entre les deux règles est faible (quelques heures) et sans grande conséquence (b) pour un départ l'après-midi, l'écart est important : la jauge est contrôlée la veille, mais les réservations restent ouvertes jusqu'à midi le jour même — un créneau déjà annulé pourrait donc rester réservable une bonne partie de la journée précédente et de la matinée | non |
| 4 | « sur la plateforme avoir email pour pouvoir envoyer un mail pour annuler au besoin » (Q04) | (a) il s'agit d'un canal armateur → client pour notifier l'annulation par e-mail (b) il s'agit d'une fonctionnalité permettant au client de demander lui-même l'annulation par e-mail | non |
| 5 | « What'sApp est bien (à garder) mais quand même utiliser la nouvelle application » (Q07) | (a) WhatsApp reste le canal principal, l'application vient en complément (b) l'application devient le canal principal, WhatsApp n'est conservé qu'en secours | non |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
| --- | --- | --- |
| 1 | Un seul naturaliste disponible : une seule sortie baleines à la fois, tous bateaux confondus | métier / réglementaire |
| 2 | Back-office limité à la modification des tarifs et à l'export du planning ; pas de gestion de flotte, de créneaux ni de contenu depuis cette interface | technique / métier |
| 3 | Le gérant est l'unique utilisateur prévu de l'outil (aucun salarié, aucun accès distinct pour un capitaine à ce stade) | organisationnelle |
| 4 | Aucun site web ni logiciel comptable existant : la plateforme et son suivi financier sont à construire de A à Z | technique |

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.

| Priorité | Question | Pourquoi elle compte |
| --- | --- | --- |
| 1 | Le contrôle de jauge a lieu à J-24h (Q16), alors que les réservations en ligne ne ferment qu'à midi — la veille pour un départ du matin, le jour même pour un départ l'après-midi (CR-01/Q09) : pour les départs de l'après-midi notamment, un client peut-il encore réserver un créneau déjà annulé faute de jauge, entre le contrôle de la veille et la fermeture de midi le jour même ? | Incohérence à lever avant de spécifier le cycle de vie d'un créneau — l'écart peut atteindre une journée entière pour un départ l'après-midi |
| 2 | Le back-office ne semble permettre que la modification des tarifs (Q03) : confirmez-vous qu'aucune configuration de la flotte, des créneaux ou du contenu du site n'est nécessaire depuis l'interface d'administration, malgré ce que prévoyaient nos user stories US12 et US13 ? | Réduit ou redéfinit fortement le périmètre du back-office déjà esquissé dans les user stories |
| 3 | Le gérant veut valider chaque annulation lui-même (Q05) et gère la reprogrammation par téléphone (Q06) : confirmez-vous qu'il n'y a pas d'auto-service pour le client (pas de lien de reprogrammation autonome, pas d'annulation « un clic » sans validation) ? | Remet en cause l'automatisation envisagée dans US09, US10 et US11, qui supposaient un parcours plus autonome pour le client |
| 4 | Comment se répartissent précisément les passagers entre les deux bateaux, notamment lorsque le nombre de personnes se situe entre 12 et 24 ? | Réponse explicitement signalée comme floue par le client (Q10) ; nécessaire pour l'algorithme d'attribution des bateaux |
| 5 | « Toutes les réservations passent par la plateforme » (Q12) signifie-t-il qu'aucune réservation téléphonique ne sera plus acceptée, ou que les réservations téléphoniques seront simplement ressaisies dans l'outil par le gérant ? | Conditionne le maintien ou non de la fonctionnalité de saisie manuelle (US06) |
| 6 | Le canal e-mail d'annulation (Q04) doit-il permettre au client de demander lui-même l'annulation, ou sert-il uniquement à notifier une annulation déjà décidée ? | Détermine si une fonctionnalité côté client (demande d'annulation par e-mail) doit être spécifiée |
| 7 | La page Facebook actuelle (Q13) est-elle à jour ? Contient-elle des tarifs ou créneaux à reprendre, ou au contraire des informations obsolètes à corriger avant le lancement ? | Évite de propager des informations erronées lors du lancement de la plateforme |

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que l'entretien n'a
pas touchés. C'est là que se cachent les découvertes tardives et coûteuses.

- La formalisation de la règle de modification de groupe après réservation
  (CR-01, ambiguïté §6 n°2, « à la discrétion de l'armateur ») n'a pas été
  reposée lors de ce second rendez-vous : elle reste ouverte.
- Aucune anticipation sur l'arrivée éventuelle de salariés (aujourd'hui,
  « uniquement le gérant » utilise l'outil) : que se passe-t-il si l'équipe
  grandit et que plusieurs comptes deviennent nécessaires ?
- Le contenu exact du message de rappel à J-1 (modèle de texte, langue,
  ton) n'a pas été précisé, seulement son échéance et ses grandes lignes.
- Ce qui se passe si le nouveau créneau proposé après une annulation météo
  (Q06) ne convient pas au client, ou tombe hors saison.
