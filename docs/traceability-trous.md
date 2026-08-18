<!-- Tenu à la main. Repris tel quel par tools/traceability.sh dans docs/traceability.md. -->

## motifs

Une ligne par exigence qu'aucune spécification ne reprend encore, au format
`REQ-0xx | pourquoi elle n'est pas encore spécifiée`. Le script y va chercher
la troisième colonne du tableau « Exigences non couvertes » ; la priorité,
elle, est lue dans le cahier des charges.

Aucune exigence non couverte à ce jour.

## trous

<!-- Une ligne par trou connu, au format du gabarit :
     | Quoi | Depuis | Pourquoi | Ce qu'on en fait | -->

| Quoi | Depuis | Pourquoi | Ce qu'on en fait |
|---|---|---|---|
| `SPEC-NFR-05` et `SPEC-NFR-06` sont sans cas de test | J3 | statut brouillon, aucun critère technique : leurs `AC` sont des actions de projet, poser une question au client et consigner sa réponse, pas des comportements logiciels | aucun cas ne sera écrit ; la vérification est de reposer les deux questions au prochain entretien |
| Quatre spécifications sans plan de délégation | J7 | `SPEC-NFR-01` et `SPEC-NFR-03` n'ont qu'un cas `manuel assumé` et **ne donnent lieu à aucune production** : une mesure de charge et une vérification documentaire, toutes deux faites par l'équipe. `SPEC-NFR-05` et `SPEC-NFR-06` n'ont aucun cas. Rien n'étant confié à l'agent, il n'y a rien à cadrer | aucun plan ne sera écrit ; la distinction avec `SPEC-BOOKING-08`, qui a bien un plan, tient à ce que celle-ci demande du code et n'a que sa vérification manuelle |
| 3 cas de test sont `manuel assumé` | J6 | rendu multi-support, charge et coût documenté ne se testent pas en continu, motifs au §4 de `docs/strategie-de-test.md` | `CASE-BOOKING-37` avant J10, `CASE-NFR-05` avant la mise en production, `CASE-NFR-06` à la revue croisée de J9 |
| 48 des 79 cas automatisables n'ont pas encore de test | J6 | les cas viennent d'être écrits ; l'automatisation est une tâche de production de l'agent, qui ne peut pas être lancée avant le plan de délégation (README §6bis) | 31 tests écrits à J7, les deux premiers paliers du §8 de `docs/strategie-de-test.md` ; les 48 restants suivent le même ordre |
| Texte des trois messages automatiques, en français et en anglais | J3 | jamais fourni par le client, ni pour le rappel, ni pour l'alerte, ni pour la confirmation d'annulation (`CR-05/Q15`) | reposé au prochain entretien ; sans lui, `SPEC-CANCEL-05` et `SPEC-CANCEL-06` ne sont testables que sur leur déclenchement, pas sur leur contenu |
| Mode d'envoi des SMS | J5 | `CR-05/Q21` répond sur le forfait conservé, pas sur la passerelle d'envoi. La lecture retenue est la seule compatible avec un envoi automatique | question 1 du §8 de `CR-05`, prioritaire : c'est le seul point qui puisse encore faire tomber l'automatisation demandée |
| Fusion de `BonCadeau` et `Avoir` | J4 | les deux dispositifs ne diffèrent plus que par leur origine depuis la v4 (question 8 du §11) | deux tables maintenues tant que le client n'a pas répondu, choix réversible documenté dans `mcd-mld.md` §5 |
| Nom exact de la plateforme d'envoi | J6 | `ADR-004` retient une plateforme française multicanal et pressent Brevo, mais trois vérifications ne peuvent pas se faire depuis le dépôt : couverture du plan de numérotation du territoire, expéditeur alphanumérique, contrat de sous-traitance RGPD | à confirmer à l'ouverture du compte ; si l'une des trois manque, l'option C de l'ADR reprend la main |
| Message associé à une annulation faute de 6 inscrits | J5 | cas non abordé par le client (`CR-05/Q14`), alors que c'est la seule annulation automatique de l'outil | question 13 du §11, à reposer ; en attendant, aucun message spécifique n'est spécifié |
| Les 31 tests écrits sont tous au rouge | J7 | ils sont écrits avant le code, sur l'API que fixent `docs/architecture.md` §3 et §4 ; aucune classe de `src/` n'existe encore, le socle technique étant monté à J8 | attendu et assumé : chaque test nomme en clair la classe de production qui lui manque, et passe au vert quand la tâche de délégation correspondante est livrée |
