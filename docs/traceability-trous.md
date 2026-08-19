<!-- Tenu à la main. Repris tel quel par tools/traceability.sh dans docs/traceability.md. -->

## motifs

Une ligne par exigence qu'aucune spécification ne reprend encore, au format
`REQ-0xx | pourquoi elle n'est pas encore spécifiée`. Le script y va chercher
la troisième colonne du tableau « Exigences non couvertes » ; la priorité,
elle, est lue dans le cahier des charges.

Les douze exigences ci-dessous viennent de `CR-06`, reçu à J8 après l'écriture du code. La
descente vers les spécifications est planifiée au §10 de `impact-CR-004.md` ; tant
qu'elle n'est pas faite, l'écart est déclaré ici plutôt que découvert à la revue.

REQ-108 | acompte de 30 % : `SPEC-BOOKING-07` doit être refondue, elle spécifie aujourd'hui le paiement intégral que CR-06 renverse
REQ-109 | acompte de 50 % : dépend de la même refonte, plus une reprise de `SPEC-BOOKING-05`
REQ-110 | l'acompte confirme la réservation : change le fait générateur de l'état « confirmée » dans `SPEC-BOOKING-03`, dont neuf critères sont à relire
REQ-111 | fenêtre de paiement du solde : demande une spécification neuve, `SPEC-BOOKING-12`. La borne elle-même est une déduction d'équipe non validée, question 16 du §11
REQ-112 | solde réglé sur place : même spécification neuve, plus le pointage côté gestion
REQ-113 | pointage réversible et tracé : demande une spécification neuve, `SPEC-ADMIN-07`
REQ-114 | planning distinguant les soldés : reprise de `SPEC-ADMIN-03`, dont l'export ne rend d'ailleurs pas encore de PDF
REQ-115 | commission plafonnée : reprise de `SPEC-ADMIN-06`. La restitution de la différence est une hypothèse d'équipe, question 19 du §11
REQ-116 | codes hors acompte : reprise de `SPEC-BOOKING-09` et `SPEC-BOOKING-10`, quatorze critères à relire
REQ-117 | deux transactions distinctes : demande `ADR-006`, le choix du prestataire n'ayant pas instruit le paiement en deux temps
REQ-118 | absent traité comme annulation : le barème n'a aucune tranche en deçà de 24 heures, le taux est une hypothèse d'équipe, question 17 du §11
REQ-119 | facture unique acquittée : entre en tension avec `REQ-018`, qui délègue la facturation à un prestataire qui ne verra jamais un solde encaissé sur place. Question 18 du §11

## trous

<!-- Une ligne par trou connu, au format du gabarit :
     | Quoi | Depuis | Pourquoi | Ce qu'on en fait | -->

| Quoi | Depuis | Pourquoi | Ce qu'on en fait |
|---|---|---|---|
| `SPEC-NFR-05` et `SPEC-NFR-06` sont sans cas de test | J3 | statut brouillon, aucun critère technique : leurs `AC` sont des actions de projet, poser une question au client et consigner sa réponse, pas des comportements logiciels | aucun cas ne sera écrit ; la vérification est de reposer les deux questions au prochain entretien |
| Quatre spécifications sans plan de délégation | J7 | `SPEC-NFR-01` et `SPEC-NFR-03` n'ont qu'un cas `manuel assumé` et **ne donnent lieu à aucune production** : une mesure de charge et une vérification documentaire, toutes deux faites par l'équipe. `SPEC-NFR-05` et `SPEC-NFR-06` n'ont aucun cas. Rien n'étant confié à l'agent, il n'y a rien à cadrer | aucun plan ne sera écrit ; la distinction avec `SPEC-BOOKING-08`, qui a bien un plan, tient à ce que celle-ci demande du code et n'a que sa vérification manuelle |
| 3 cas de test sont `manuel assumé` | J6 | rendu multi-support, charge et coût documenté ne se testent pas en continu, motifs au §4 de `docs/strategie-de-test.md` | `CASE-BOOKING-37` avant J10, `CASE-NFR-05` avant la mise en production, `CASE-NFR-06` à la revue croisée de J9 |
| Les 3 cas de bout en bout n'ont pas de scénario Behat | J6 | les 76 cas de niveau domaine et application sont automatisés en PHPUnit à J7 ; `CASE-BOOKING-08`, `CASE-BOOKING-22` et `CASE-BOOKING-35` relèvent du troisième niveau, qui suppose un socle applicatif déployé | à écrire quand le socle tournera, au plus tard à J9 |
| Texte des trois messages automatiques, en français et en anglais | J3 | jamais fourni par le client, ni pour le rappel, ni pour l'alerte, ni pour la confirmation d'annulation (`CR-05/Q15`) | des gabarits **provisoires** sont écrits à J8 dans `translations/`, ne disant que ce que les spécifications établissent ; ils seront remplacés dès que le client fournira sa rédaction. Aucun test ne vérifie leur contenu, seulement leur existence dans les deux langues |
| Mode d'envoi des SMS | J5 | `CR-05/Q21` répond sur le forfait conservé, pas sur la passerelle d'envoi. La lecture retenue est la seule compatible avec un envoi automatique | question 1 du §8 de `CR-05`, prioritaire : c'est le seul point qui puisse encore faire tomber l'automatisation demandée |
| Fusion de `BonCadeau` et `Avoir` | J4 | les deux dispositifs ne diffèrent plus que par leur origine depuis la v4 (question 8 du §11) | deux tables maintenues tant que le client n'a pas répondu, choix réversible documenté dans `mcd-mld.md` §5 |
| Nom exact de la plateforme d'envoi | J6 | `ADR-004` retient une plateforme française multicanal et pressent Brevo, mais trois vérifications ne peuvent pas se faire depuis le dépôt : couverture du plan de numérotation du territoire, expéditeur alphanumérique, contrat de sous-traitance RGPD | à confirmer à l'ouverture du compte ; si l'une des trois manque, l'option C de l'ADR reprend la main |
| Message associé à une annulation faute de 6 inscrits | J5 | cas non abordé par le client (`CR-05/Q14`), alors que c'est la seule annulation automatique de l'outil | question 13 du §11, à reposer ; en attendant, aucun message spécifique n'est spécifié |
| L'export du planning ne produit pas de PDF | J8 | `ExporterLePlanning` produit le contenu du document, groupé par créneau et ordonné ; sa mise en page appartient à la couche Interface, qui n'existe pas encore. **`CASE-ADMIN-06` ne fait pas la différence** : il vérifie `estUnPdf()`, qui rend une valeur constante | à rendre avec la couche de présentation ; d'ici là, le cas de test surestime ce qu'il prouve, et c'est écrit ici plutôt que découvert à J10 |
| La couche `Interface` n'est pas écrite | J8 | les 76 cas de test entrent par la couche Application ; aucun écran n'est donc nécessaire pour les faire passer, et en écrire un sans spécification d'écran serait du code non couvert | à ouvrir quand un parcours devra être montré à l'écran, au plus tard pour la démonstration de J10 |
| Deux ports n'ont pas d'adaptateur réel | J8 | `Notificateur` et `PrestataireDePaiement` sont liés à des adaptateurs qui **échouent bruyamment**, l'intégration de Brevo relevant d'`ADR-004` et celle de Stripe de `SPEC-BOOKING-07`. En test, les doublures les remplacent | assumé : un envoi ou un encaissement silencieusement perdu coûterait bien plus cher qu'une erreur visible. À intégrer avant toute mise en production |
| Le code et les cas de test restent en v5 | J8 | `CR-06` renverse `REQ-017` le soir de la journée d'implémentation. Le cahier des charges passe en v6 le jour même, les spécifications suivent à J9 ; **les 82 cas de test, les 76 tests et le code décrivent encore le paiement intégral** | écart voulu et ordonné : la chaîne descend du cahier des charges vers le code, jamais l'inverse. Le lot code est conditionné à un point d'arrêt le 21/08 à 09h00, cf. §9 de `impact-CR-004.md` |
