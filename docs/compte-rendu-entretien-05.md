# Compte rendu d'entretien n° 5

**Date :** 2026-08-14
**Durée :** …
**Interlocuteur :** le commanditaire (armateur, Ti Baleine)
**Présents pour l'équipe :** …
**Source brute :** échange oral. Le client formule d'abord une demande
nouvelle, puis répond à une série de questions préparées par l'équipe à
partir de la relecture des spécifications d'annulation.

> ⚠️ **Statut particulier de ce compte rendu.** Comme
> [`compte-rendu-entretien-04.md`](./compte-rendu-entretien-04.md), il ne
> s'appuie sur aucune source brute écrite. La colonne « formulation du
> client » du [§5](#5-règles-métier-découvertes) reproduit les propos **tels
> que rapportés par l'équipe**. Les identifiants `CR-05/Qnn` sont utilisables
> dès à présent par le cahier des charges, mais ce document doit être relu
> par la personne qui a mené l'échange avant d'être considéré comme
> définitif.

> Cet échange ajoute un dispositif entier, l'alerte météo préventive, et
> **corrige une lecture erronée de [`CR-02/Q04`](./compte-rendu-entretien-02.md#2-questions-posées-et-réponses-obtenues)**
> qui avait produit deux exigences `Must` inexactes. Voir l'analyse d'impact
> dédiée, [`impact-CR-003.md`](./impact-CR-003.md).

---

## 1. Ce que le client a dit

Ses mots, pas les vôtres. Citer quand la formulation est ambiguë, c'est
précisément l'ambiguïté qu'il faudra lever.

Le client ouvre l'échange sur un besoin qu'il n'avait jamais évoqué :

> « Pouvoir avertir les clients la veille à 18h (par sms ou par mail) que
> potentiellement, leur sortie du créneau de 7h, 10h ou 14h risque d'être
> annulé, et qu'il les tiendra au courant 2h avant leur sortie ; c'est
> maintenu pour le moment mais possible d'annuler le jour même. »

> « Si la sortie est maintenue il n'y a pas besoin d'envoyer un nouveau
> message, le client va seulement recevoir la veille le message d'alerte
> météo. Si la sortie est annulée, le client va recevoir l'alerte la veille
> + un message 2h avant la sortie pour confirmer l'annulation (exemple : sms
> à 5h si sortie a 7h). »

Interrogé sur les conséquences, il revient de lui-même sur deux positions
antérieures : il accepte le SMS, qu'il avait écarté, et il ne veut plus
appeler ses clients pour les prévenir d'une annulation.

## 2. Questions posées et réponses obtenues

Le client ne répond qu'à ce qu'on lui demande. Ce tableau est donc aussi la
trace de ce que vous n'avez **pas** demandé.

**Chaque question reçoit un identifiant `Qnn`.** C'est lui que citeront les
exigences du cahier des charges : `CR-05/Q03` désigne la question 3 de ce
compte rendu. La numérotation est définitive, on n'insère pas, on ajoute à
la suite.

| ID | Question posée | Réponse |
|---|---|---|
| Q01 | **Demande formulée spontanément par le client, en ouverture d'échange** *(aucune question posée)* | Alerte préventive la veille à 18h, puis message de confirmation 2h avant le départ si la sortie est annulée. Aucun message si elle est maintenue. Voir §1 |
| Q02 | **Canal :** SMS, e-mail, ou les deux ? Et WhatsApp, aujourd'hui simple canal de secours manuel ? | Les deux, systématiquement : SMS **et** e-mail. Le client revient sur son refus du SMS et l'accepte. Il craint que l'e-mail seul finisse en indésirable. WhatsApp reste un secours sans garantie, « tout le monde n'a pas WhatsApp » |
| Q03 | **Canal :** Ce message écrit remplace-t-il votre appel téléphonique, ou s'y ajoute-t-il ? | Il le remplace. Le gérant ne souhaite plus avoir à appeler les clients pour les prévenir d'une annulation, tout passera par écrit. Le client, lui, garde la possibilité d'appeler le gérant pour ses annulations personnelles |
| Q04 | **Fiabilité :** Que fait-on si un message n'est pas délivré, puisque plus personne n'appelle ? | Choix assumé du gérant. Un message qui n'arrive pas à cause d'un mauvais numéro ou d'une adresse erronée relève de la responsabilité du client, qui devait saisir des informations correctes. Considéré comme un non-sujet |
| Q05 | **Coût :** Qui paie les SMS, sur quel budget ? | Budget « illimité » pour l'exercice. Considéré comme un non-sujet |
| Q06 | **Réservation :** Un créneau en alerte reste-t-il réservable ? | Oui, et l'alerte est signalée sur la plateforme aux clients qui réservent ce créneau |
| Q07 | **Durée :** Jusqu'à quand l'alerte court-elle ? | Jusqu'à l'heure de début de la sortie |
| Q08 | **Déclenchement :** L'alerte part-elle automatiquement pour toutes les sorties du lendemain, ou la déclenchez-vous ? | Déclenchement par le gérant depuis l'espace de gestion, créneau par créneau, indépendamment. Aucune alerte automatique |
| Q09 | **Remboursement :** Un client qui réserve un créneau déjà en alerte est-il remboursé aux mêmes conditions que les autres ? | Oui, mêmes conditions |
| Q10 | **Remboursement :** Un client alerté qui préfère renoncer paie-t-il la retenue prévue au barème ? | Non, il est remboursé intégralement, le risque d'annulation venant du gérant. Le remboursement lui reste acquis même si la sortie est finalement maintenue |
| Q11 | **Annulation météo :** Le client garde-t-il le choix entre report, avoir et remboursement, comme le laisse entendre `CR-02/Q04` ? | Non. Une annulation décidée par le gérant donne toujours un remboursement intégral. Le choix entre avoir, report et remboursement n'a **jamais** concerné l'annulation météo : il ne vaut que pour une annulation à l'initiative du client : celui-ci appelle le gérant, ils s'accordent sur l'une des trois issues, et le gérant la valide depuis son espace de gestion, y compris pour délivrer un avoir. `CR-02/Q04` a été mal transcrit |
| Q12 | **Remboursement :** Le remboursement part-il tout seul, sous quel délai, et le client en est-il averti ? | Exécuté par Stripe après validation du remboursement par le gérant. Toute communication liée au remboursement est celle de Stripe |
| Q13 | **Messages :** L'alerte de la veille remplace-t-elle le message de rappel, qui annonce déjà la météo prévue ? | Non. Les deux messages partent, à quelques heures d'intervalle |
| Q14 | **Périmètre :** L'alerte vaut-elle aussi pour une privatisation ? Et une sortie annulée faute de 6 inscrits déclenche-t-elle le même message ? | Oui pour la privatisation. **Sans réponse** pour l'annulation au seuil de 6 inscrits : le cas n'a pas été abordé |
| Q15 | **Contenu :** Quel est le texte exact des messages, en français et en anglais ? | **Sans réponse** : reste à définir avec le gérant |

Deux questions restent donc sans réponse complète, `Q14` pour sa seconde
moitié et `Q15`. Elles sont reprises au §8.

## 3. Ce que nous avons compris

Reformulation en langage métier. À relire au client au prochain passage :
s'il répond « non, pas tout à fait », la compréhension n'est pas acquise.

Un créneau connaît désormais un **état intermédiaire**, entre ouvert et
annulé. Le gérant, quand la météo l'inquiète, place un créneau en alerte
depuis son espace de gestion. Ce geste est manuel, créneau par créneau, et
il déclenche un message d'avertissement aux clients déjà inscrits. Le
créneau reste vendu, avec la mention du risque, et l'alerte court jusqu'à
l'heure de départ. Si le gérant annule, un second message part deux heures
avant le départ. S'il ne fait rien, la sortie a lieu et le client ne reçoit
rien de plus : **le silence vaut maintien**, et le client l'a explicitement
assumé.

Le parcours d'annulation change de canal. Le gérant ne veut plus appeler
personne : ses clients seront prévenus par écrit, par SMS et par e-mail
simultanément, WhatsApp restant ce qu'il est aujourd'hui, un secours manuel
sans garantie. Le sens de l'appel s'inverse, du gérant vers le client il
disparaît, du client vers le gérant il subsiste pour les annulations
personnelles.

L'échange corrige surtout une erreur d'analyse qui remonte au deuxième
entretien. `CR-02/Q04` a été transcrit comme si le gérant, après une
annulation météo, appelait chaque client pour lui proposer un report, un
avoir ou un remboursement. Le client dit aujourd'hui que ce choix n'a jamais
concerné la météo : une annulation qu'il décide donne toujours un
remboursement intégral. Le triptyque report, avoir ou remboursement ne vaut
que pour le client qui annule lui-même, et qui appelle pour cela. Deux
exigences `Must` reposent sur la mauvaise lecture, `REQ-023` et `REQ-024`,
ainsi que l'origine de l'avoir écrite en `REQ-050`.

Enfin, une règle nouvelle apparaît sans avoir été demandée : un client dont
la sortie a été mise en alerte et qui préfère renoncer est remboursé
intégralement, y compris si la sortie part finalement. C'est la première
exception au barème dégressif `R-05`, et le client la justifie par le fait
que le doute vient de lui.

## 4. Parties prenantes identifiées

| Personne / rôle | Ce qu'elle fait | Comment on l'a découverte |
|---|---|---|
| Gérant / armateur Ti Baleine | Place un créneau en alerte, décide l'annulation, valide les remboursements ; n'appelle plus les clients | Q01, Q03, Q08, Q12 |
| Client / passager | Reçoit l'alerte puis, le cas échéant, la confirmation d'annulation ; peut renoncer et être remboursé intégralement ; garde le téléphone pour ses propres annulations | Q01, Q03, Q10 |
| Prestataire de paiement (Stripe) | Exécute le remboursement après validation du gérant et assure la communication qui l'accompagne | Q12 |

Aucune partie prenante nouvelle. Le prestataire de paiement, jusqu'ici
cantonné à l'encaissement, prend un rôle sortant vers le client.

## 5. Règles métier découvertes

Rappel du statut de ce compte rendu : la colonne de formulation reproduit
les propos **tels que rapportés par l'équipe**, et non une transcription.

| # | Règle | Formulation rapportée du client | Sûre ? |
|---|---|---|---|
| 1 | Le gérant place un créneau en alerte météo depuis l'espace de gestion, créneau par créneau | « l'envoi de l'alerte est un choix du gérant via le backoffice, pas une alerte automatique » ; « le gérant provoque l'alerte sur chaque créneau indépendamment » | oui |
| 2 | L'alerte est envoyée la veille à 18h et annonce un risque d'annulation, la décision définitive étant communiquée 2h avant le départ | « avertir les clients la veille à 18h […] qu'il les tiendra au courant 2h avant leur sortie » | oui, horaires à confirmer réglables ou figés |
| 3 | Si la sortie est maintenue, aucun second message n'est envoyé | « si la sortie est maintenue il n'y a pas besoin d'envoyer un nouveau message » | oui |
| 4 | Si la sortie est annulée, un message de confirmation part 2h avant l'heure de départ | « un message 2h avant la sortie pour confirmer l'annulation (exemple : sms à 5h si sortie a 7h) » | oui |
| 5 | Un créneau en alerte reste réservable, le risque étant signalé sur la plateforme | « le créneau en alerte est signalé sur la plateforme pour les nouvelles réservations sur ce créneau » | oui |
| 6 | L'alerte court jusqu'à l'heure de début de la sortie | « l'alerte court jusqu'à l'heure de début de la sortie » | oui |
| 7 | Les messages partent systématiquement par SMS **et** par e-mail ; WhatsApp reste un secours sans garantie | « on envoie finalement systématiquement SMS + email. Tout le monde n'a pas WhatsApp donc ça reste un backup sans garantie » | oui, **annule le refus du SMS retenu jusqu'ici** |
| 8 | Le gérant ne prévient plus ses clients par téléphone ; tout passe par écrit | « le gérant ne souhaite plus avoir à appeler les clients pour les prévenir d'une annulation, tout sera par écrit » | oui, **annule la règle 7 de `CR-02`** |
| 9 | Un message non délivré pour cause de coordonnées erronées relève de la responsabilité du client | « c'est la faute du client qui avait à entrer des infos correctes. C'est un non sujet » | oui |
| 10 | Une annulation décidée par le gérant donne toujours un remboursement intégral | « l'annulation du créneau entraîne toujours un remboursement intégral, l'action étant à l'initiative du gérant » | oui |
| 11 | Le choix entre avoir, report et remboursement ne concerne que les annulations à l'initiative du client : le client appelle le gérant, et le gérant valide l'issue retenue depuis son espace de gestion | « les avoirs ne sont disponibles que pour des annulations venant du client de l'application. Il peut SOIT obtenir un avoir, SOIT déplacer son créneau, SOIT être remboursé » ; « appel du client au gérant, validation en backoffice du gérant pour un avoir » | oui, **corrige la lecture retenue de `CR-02/Q04`** |
| 12 | Un client qui renonce à une sortie mise en alerte est remboursé intégralement, même si la sortie part finalement | « le client alerté qui préfère annuler est remboursé intégralement, le risque d'annulation étant à l'initiative du gérant » | oui |
| 13 | Le remboursement est exécuté par Stripe après validation du gérant, et la communication associée est celle de Stripe | « le remboursement est géré par Stripe suite à la validation du gérant » | oui |
| 14 | Le budget de l'exercice est illimité | « le budget pour l'exercice est illimité » | oui, à lire comme une réponse de contexte pédagogique et non comme un engagement d'exploitation |

## 6. Ambiguïtés détectées

Ce que le client a dit et qui peut se comprendre de plusieurs façons. Une
ambiguïté détectée mais non levée reste une ambiguïté : elle va au §8.

| # | Formulation | Lectures possibles | Levée ? |
|---|---|---|---|
| 1 | « la veille à 18h » et « 2h avant » (Q01) | (a) horaires figés dans l'outil (b) valeurs par défaut réglables depuis l'espace de gestion, comme l'horaire du message de rappel | non, **hypothèse d'équipe : lecture (b)**, par cohérence avec `REQ-042` ; question 1 du §8 |
| 2 | « possible d'annuler le jour même » (Q01) | (a) jusqu'au repère des 2h, au-delà duquel plus rien n'est annulable dans l'outil (b) jusqu'à l'heure de départ, un message partant alors immédiatement au lieu d'être programmé | non, **hypothèse d'équipe : lecture (b)** ; question 2 du §8 |
| 3 | « c'est la faute du client » (Q04) | (a) constat de responsabilité, sans conséquence applicative (b) implique au minimum de contrôler le format du numéro de mobile à la saisie, faute de quoi la règle est inapplicable | non, **hypothèse d'équipe : lecture (b)**, contrôle de forme du mobile ajouté au formulaire |
| 4 | « le gérant provoque l'alerte sur chaque créneau » (Q08) | (a) l'alerte porte sur le créneau entier, donc sur les deux bateaux qui y naviguent (b) l'alerte peut viser un seul bateau du créneau | non, **hypothèse d'équipe : lecture (a)**, alignée sur celle déjà retenue pour l'annulation |
| 5 | Portée du message de confirmation (Q01, Q06) | (a) seuls les clients présents au moment de l'alerte le reçoivent (b) tout client inscrit au moment de l'annulation le reçoit, y compris celui qui a réservé après l'alerte | non, **hypothèse d'équipe : lecture (b)**, la seule qui protège le client réservant le matin même une sortie de 14h |

## 7. Contraintes évoquées

| # | Contrainte | Nature |
|---|---|---|
| 1 | L'envoi de SMS impose un prestataire tiers supplémentaire, un numéro de mobile valide et un format contrôlé, alors que le formulaire ne collecte aujourd'hui qu'un téléphone au format libre | technique |
| 2 | La suppression de l'appel téléphonique retire le dernier rattrapage humain : aucun message non délivré n'est plus repêché | métier |
| 3 | Deux exigences `Must` déjà descendues jusqu'à l'UML reposent sur une transcription erronée de `CR-02/Q04` et doivent être reprises | méthode |
| 4 | Le remboursement intégral systématique, étendu au client qui renonce après une alerte même si la sortie part, est une exposition financière assumée par le commanditaire | métier |
| 5 | Un troisième message automatique s'ajoute au rappel existant, sans que les textes types d'aucun des trois ne soient encore fournis, alors que le site est bilingue | technique |

## 8. Questions à poser au prochain entretien

Formulées, pas juste évoquées. Priorisées : le prochain passage est court.

| Priorité | Question | Pourquoi elle compte |
|---|---|---|
| 1 | L'heure d'envoi de l'alerte (18h) et le délai de confirmation (2h avant) sont-ils figés, ou réglables depuis l'espace de gestion comme l'horaire du message de rappel ? | Détermine si deux valeurs entrent dans la configuration de l'espace de gestion ou dans le code ; §6, ambiguïté 1 |
| 1 | Jusqu'à quelle heure pouvez-vous annuler un créneau, et qu'envoie-t-on si vous décidez après le repère des deux heures ? | Sans borne, la règle des deux heures n'est pas spécifiable ; §6, ambiguïté 2 |
| 2 | Une sortie annulée faute d'atteindre 6 inscrits déclenche-t-elle le même message de confirmation ? | Resté sans réponse en `Q14`. Ce sont aujourd'hui les seules annulations automatiques de l'outil, et elles n'ont aucun message associé |
| 2 | Un client qui renonce après une alerte peut-il aussi demander un report ou un avoir, ou seulement le remboursement intégral ? | Il annule de sa propre initiative, ce qui ouvre le triptyque, mais vous lui accordez un remboursement intégral ; les deux règles se rencontrent sans que le résultat soit écrit |
| 2 | Le montant d'un avoir accordé après une annulation client suit-il le barème dégressif, par exemple la moitié du montant payé à moins de 48 heures, ou vaut-il la totalité de la somme versée ? | L'avoir est désormais rattaché à l'annulation client, à laquelle s'applique une retenue. Sans réponse, l'écran de validation en back-office n'est pas spécifiable |
| 2 | Quel est le texte des trois messages automatiques, en français et en anglais ? | Resté sans réponse en `Q15`. Le rappel de la veille lui-même n'a jamais reçu son texte type |
| 3 | Le client doit-il consentir explicitement à recevoir des SMS au moment de réserver ? | Non abordé. Un envoi automatisé vers un mobile suppose une information du client, et le formulaire ne prévoit aucune case |

Les questions du §8 de `CR-04` (fusion du bon cadeau et de l'avoir, bornes
du montant d'un bon, sort d'une réservation payée par bon cadeau lors d'une
annulation, information avant expiration d'un avoir) **n'ont pas été
reposées** lors de cet échange et restent ouvertes.

## 9. Ce que nous n'avons pas abordé

Relire le brief initial et lister les sujets qu'il contient et que
l'entretien n'a pas touchés. C'est là que se cachent les découvertes
tardives et coûteuses.

- Le sort d'une réservation payée avec un bon cadeau lorsqu'elle est
  annulée par le gérant : la question devient plus pressante maintenant que
  le remboursement intégral est systématique, un bon cadeau n'étant pas
  remboursable en argent.
- Le consentement du client à être contacté par SMS, et la mention
  correspondante dans les informations légales du site.
- La réponse « budget illimité » clôt formellement la question 2 du §11 du
  cahier des charges, ouverte depuis le premier entretien, mais elle relève
  du cadre pédagogique et ne constitue pas un engagement d'exploitation.
- Le format de transmission de la facture (`CdC`, §11 question 3), la durée
  de conservation des données clients (question 4) et les modalités de
  connexion à l'espace de gestion (question 5) n'ont pas été reposés.
- La règle de modification d'un groupe après réservation (`CR-01`, §6
  ambiguïté 2) **reste ouverte**, pour le cinquième entretien consécutif.
