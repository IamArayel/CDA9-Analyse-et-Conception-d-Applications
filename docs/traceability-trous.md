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
| Les 29 spécifications sont sans cas de test | J3 | les `CASE-*` ne s'écrivent qu'une fois le MCD/MLD stabilisé, ce qui est acquis depuis J5 | premiers cas de test à J6, en commençant par `SPEC-BOOKING-03` et `SPEC-CANCEL-06`, les deux plus exposées |
| Texte des trois messages automatiques, en français et en anglais | J3 | jamais fourni par le client, ni pour le rappel, ni pour l'alerte, ni pour la confirmation d'annulation (`CR-05/Q15`) | reposé au prochain entretien ; sans lui, `SPEC-CANCEL-05` et `SPEC-CANCEL-06` ne sont testables que sur leur déclenchement, pas sur leur contenu |
| Mode d'envoi des SMS | J5 | `CR-05/Q21` répond sur le forfait conservé, pas sur la passerelle d'envoi. La lecture retenue est la seule compatible avec un envoi automatique | question 1 du §8 de `CR-05`, prioritaire : c'est le seul point qui puisse encore faire tomber l'automatisation demandée |
| Fusion de `BonCadeau` et `Avoir` | J4 | les deux dispositifs ne diffèrent plus que par leur origine depuis la v4 (question 8 du §11) | deux tables maintenues tant que le client n'a pas répondu, choix réversible documenté dans `mcd-mld.md` §5 |
| `ADR-004`, prestataire d'envoi de SMS | J5 | sans objet : le client conserve le forfait et le numéro de l'entreprise (`CR-05/Q21`) | aucun ADR à écrire, la trace de la décision est dans `CR-05` et au journal de J5 |
| Message associé à une annulation faute de 6 inscrits | J5 | cas non abordé par le client (`CR-05/Q14`), alors que c'est la seule annulation automatique de l'outil | question 13 du §11, à reposer ; en attendant, aucun message spécifique n'est spécifié |
