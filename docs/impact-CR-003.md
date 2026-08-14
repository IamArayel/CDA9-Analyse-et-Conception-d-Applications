# Analyse d'impact - CR-003

**Demande du client :** alerte météo préventive envoyée la veille d'une
sortie, confirmation d'annulation deux heures avant le départ, abandon de
l'appel téléphonique au profit de l'écrit, et correction d'une lecture
erronée de `CR-02/Q04` sur le choix laissé au client après une annulation.
**Reçue le :** 2026-08-14, entretien oral, formalisé en
[`compte-rendu-entretien-05.md`](./compte-rendu-entretien-05.md) (CR-05).
**Rédigée par :** l'équipe, avec revue critique de l'IA.

---

> **Interdiction de modifier le code avant que cette analyse soit complète.**
>
> La modification descend la chaîne dans cet ordre : cahier des charges → specs →
> UML → modèle de données → cas de test → tests → code.

Ce CR-003 est le plus lourd des trois. Il ajoute un dispositif entier,
l'alerte météo préventive, il inverse le canal d'annonce d'une annulation, et
il **corrige une erreur d'analyse remontant au deuxième entretien** qui avait
produit deux exigences `Must` inexactes et un diagramme de séquence faux.
Aucun code ni test automatisé n'existe encore (`src/`, `tests/cases/` sont
vides) : l'analyse porte sur le cahier des charges, les spécifications et
l'UML.

---

## 1. Ce que le client demande, reformulé

Le gérant veut pouvoir prévenir ses clients d'un risque d'annulation sans
s'engager à annuler. Quand la météo l'inquiète, il place un créneau en alerte
depuis son espace de gestion : les clients inscrits reçoivent la veille à 18h
un message les avertissant que la sortie pourrait ne pas partir, et qu'ils
seront fixés deux heures avant le départ. Si la sortie est maintenue, ils ne
reçoivent rien de plus. Si elle est annulée, un second message le leur
confirme deux heures avant l'heure prévue. Le créneau reste vendu pendant
toute la durée de l'alerte, avec la mention du risque, et l'alerte court
jusqu'à l'heure de départ.

Le gérant profite de l'échange pour abandonner le téléphone : il ne veut plus
appeler personne pour annoncer une annulation, tout passera par écrit, par
SMS et par e-mail simultanément. Il accepte donc le SMS, qu'il avait écarté.
Il précise enfin que le choix entre report, avoir et remboursement n'a jamais
concerné les annulations qu'il décide lui-même : celles-ci donnent toujours
un remboursement intégral, et le triptyque ne vaut que pour le client qui
annule de son côté, par téléphone.

## 2. Questions posées au client

| # | Question | Réponse |
|---|---|---|
| 1 | *(non posée, formulée par le client)* | Alerte la veille à 18h, confirmation 2h avant le départ, silence si la sortie est maintenue (`CR-05/Q01`) |
| 2 | Canal des messages | SMS et e-mail systématiquement, WhatsApp en secours sans garantie (`CR-05/Q02`) |
| 3 | L'écrit remplace-t-il l'appel | Oui, du gérant vers le client. Le client garde le téléphone pour ses propres annulations (`CR-05/Q03`) |
| 4 | Message non délivré | Responsabilité du client, non-sujet (`CR-05/Q04`) |
| 5 | Coût des SMS | Budget illimité pour l'exercice, non-sujet (`CR-05/Q05`) |
| 6 | Réservabilité d'un créneau en alerte | Reste réservable, risque signalé (`CR-05/Q06`) |
| 7 | Durée de l'alerte | Jusqu'à l'heure de début de la sortie (`CR-05/Q07`) |
| 8 | Déclenchement | Manuel, par le gérant, créneau par créneau (`CR-05/Q08`) |
| 9 | Remboursement d'un client ayant réservé en alerte | Mêmes conditions que les autres (`CR-05/Q09`) |
| 10 | Client alerté qui renonce | Remboursé intégralement, même si la sortie part (`CR-05/Q10`) |
| 11 | Choix report / avoir / remboursement après annulation météo | N'a jamais existé : remboursement intégral. `CR-02/Q04` mal transcrit (`CR-05/Q11`) |
| 12 | Exécution du remboursement | Stripe, après validation du gérant (`CR-05/Q12`) |
| 13 | Alerte et message de rappel | Les deux partent, à quelques heures d'intervalle (`CR-05/Q13`) |
| 14 | Privatisation, et annulation au seuil de 6 | Oui pour la privatisation ; **sans réponse** pour le seuil de 6 (`CR-05/Q14`) |
| 15 | Texte des messages | **Sans réponse**, à définir (`CR-05/Q15`) |

Cinq lectures restent des hypothèses d'équipe non confirmées : horaires
réglables ou figés, heure limite d'annulation, contrôle du format du mobile,
portée de l'alerte sur les deux bateaux d'un créneau, destinataires du
message de confirmation. Voir `CR-05` §6. Elles seront marquées `déduit` au
cahier des charges plutôt que sourcées `CR-05/Qnn`.

## 3. Impact - cahier des charges

| Exigence | Impact | Action |
|---|---|---|
| REQ-023 | **inversée** | L'annulation météo ne donne plus lieu à un choix : remboursement intégral systématique, annoncé par écrit. Identifiant conservé, comme pour `REQ-045` en v4. Source complétée par `CR-05/Q11`, avec mention de l'erreur de lecture de `CR-02/Q04` |
| REQ-024 | **inversée**, priorité `Must` → `Won't` | Plus aucune proposition de report après une annulation météo. L'exigence reste au document pour que la correction soit lisible |
| REQ-026 | modifiée | « prévenus par téléphone » devient « prévenus par écrit, par SMS et par e-mail ». Source complétée `CR-05/Q03` |
| REQ-019 | modifiée | Rattache explicitement le triptyque avoir, report ou remboursement à l'annulation **à l'initiative du client**, et ajoute l'exception du créneau mis en alerte (remboursement intégral) |
| REQ-050 | modifiée | Origine de l'avoir corrigée : accordé à la suite d'une annulation à l'initiative du client, convenue par téléphone et validée par le gérant depuis l'espace de gestion, et non plus à la suite d'une annulation météo |
| REQ-009 | modifiée | Le téléphone collecté devient un **numéro de mobile**, contrôlé sur sa forme, puisqu'il porte désormais un canal de notification |
| REQ-025, REQ-042 | inchangées | Le message de rappel subsiste et n'est pas remplacé par l'alerte (`CR-05/Q13`) |
| REQ-027 | inchangée | Le client confirme WhatsApp comme secours sans garantie : aucune intégration technique |
| REQ-103 | inchangée | Le choix d'hébergement tient, mais sa justification par le budget est à revoir (`CR-05/Q05`) |
| REQ-052 *(nouvelle)* | ajoutée | Le gérant peut placer un créneau en alerte météo, créneau par créneau, depuis l'espace de gestion |
| REQ-053 *(nouvelle)* | ajoutée | La mise en alerte envoie aux clients inscrits, la veille à 18h, un message annonçant un risque d'annulation et une décision communiquée 2 heures avant le départ |
| REQ-054 *(nouvelle)* | ajoutée | Si le créneau est annulé, un message de confirmation part 2 heures avant l'heure de départ ; si la sortie est maintenue, aucun message supplémentaire n'est envoyé |
| REQ-055 *(nouvelle)* | ajoutée | Un créneau en alerte reste réservable, le risque étant signalé au client avant qu'il ne réserve ; l'alerte court jusqu'à l'heure de départ |
| REQ-056 *(nouvelle)* | ajoutée | Un client dont le créneau a été mis en alerte et qui renonce est remboursé intégralement, même si la sortie a finalement lieu |
| REQ-057 *(nouvelle)* | ajoutée | Les messages sortants partent systématiquement par SMS et par e-mail |
| REQ-058 *(nouvelle)* | ajoutée | Le remboursement est exécuté par le prestataire de paiement après validation du gérant, la communication associée relevant du prestataire |

Au §11, la question 2 (budget) reçoit une réponse et se ferme. Quatre
questions s'ouvrent : horaires d'envoi réglables ou figés, heure limite
d'annulation, message associé à une annulation au seuil de 6 inscrits,
consentement du client à recevoir des SMS.

Le reste des exigences est **inchangé** : ni la réservation, ni la
tarification, ni le paiement, ni les bons cadeaux ne sont touchés.

## 4. Impact - spécifications

| Spécification | Impact | Ce qui change exactement |
|---|---|---|
| `SPEC-CANCEL-01` | modifiée | Sa portée s'étend : le gérant consulte les inscrits avant de mettre en alerte, pas seulement avant d'annuler |
| `SPEC-CANCEL-02` | modifiée | Un état « en alerte » s'intercale entre ouvert et annulé. Le cas limite « météo redevenue favorable » cesse d'être un cas limite : lever une alerte devient normal, et silencieux |
| `SPEC-CANCEL-03` | modifiée | Distinguer deux répercussions : un créneau annulé disparaît, un créneau en alerte reste vendu avec un signalement visible |
| `SPEC-CANCEL-04` | **réécrite** | Perd l'appel téléphonique et l'enregistrement du choix. Devient : notification écrite de l'annulation et remboursement intégral validé par le gérant puis exécuté par le prestataire |
| `SPEC-CANCEL-05` | modifiée | Le canal devient SMS et e-mail. L'hypothèse « e-mail seul » tombe, et le refus IA du SMS consigné en revue est inversé par le client |
| `SPEC-CANCEL-06` *(nouvelle)* | ajoutée | Alerte météo préventive : mise en alerte, message de la veille, message de confirmation, silence si maintien |
| `SPEC-ADMIN-06` *(nouvelle)* | ajoutée | Validation d'un avoir par le gérant en back-office, à la suite d'une annulation client convenue par téléphone. Sans elle, l'usage du code est spécifié mais sa création ne l'est plus, `SPEC-CANCEL-04` AC-4 disparaissant, voir §8 |
| `SPEC-BOOKING-01` | modifiée | Le téléphone devient un mobile contrôlé sur sa forme ; l'hypothèse « format libre » tombe |
| `SPEC-BOOKING-10` | modifiée | L'origine de l'avoir change : annulation à l'initiative du client, plus annulation météo |
| `SPEC-NFR-04` | modifiée | Le mobile devient une donnée de contact indispensable, et le consentement à recevoir des SMS reste à écrire |
| `SPEC-NFR-03` | modifiée | Justification du choix d'hébergement à reformuler, le budget n'étant plus le motif |

Toutes les autres spécifications sont **inchangées** : aucune réponse de
`CR-05` ne touche aux créneaux, à la capacité, à la tarification, au
paiement, aux bons cadeaux ni à l'espace de gestion hors émission d'un avoir.

## 5. Impact - conception

| Artefact | Impact | Ce qui change |
|---|---|---|
| `uml/domain.puml` | modifié | `Sortie.statut` accueille l'état « en alerte » ; nouvelle classe `Notification` (type, canal, destinataire, dateEnvoi, statut) pour savoir ce qui a été envoyé et à qui ; `ChoixAnnulation` quitte le parcours météo et se rattache à l'annulation client ; `Réservation.téléphone` devient un mobile |
| `uml/use-cases.puml` | modifié | Nouveau cas d'usage « Mettre un créneau en alerte » ; UC8 « Enregistrer le choix d'un client » devient « Émettre un avoir » ; les envois automatiques, jamais représentés, apparaissent comme déclenchés par le système |
| `uml/sequences/annuler-creneau-meteo.puml` | **à refaire** | Commité hier, il montre l'appel téléphonique et le choix report / avoir / remboursement. Les deux disparaissent |
| `uml/sequences/` | à créer | Un diagramme « Mettre un créneau en alerte » couvrant les deux issues, maintien silencieux et annulation confirmée |
| MCD / MLD | non commencé | Ajoute une table de notifications et un statut de sortie enrichi. Cette analyse est un intrant direct de sa première version |
| `architecture.md` | non rempli | Deux services externes sortants à documenter au §6, envoi de SMS et envoi d'e-mails, avec leur comportement en cas d'indisponibilité |
| `adr/ADR-004` | à créer | Choix du prestataire d'envoi de SMS. Les numéros ADR-002 (persistance) et ADR-003 (concurrence sur la dernière place) sont déjà pris |

**État nouveau ou donnée nouvelle ?** Oui, sur deux points. L'état « en
alerte » d'une sortie n'existait dans aucune version antérieure du modèle, et
il n'est pas réductible à un booléen : il porte une date d'envoi, il court
jusqu'au départ, et il conditionne un droit au remboursement intégral. La
trace des notifications est également nouvelle : sans elle, rien ne permet de
savoir si le message de confirmation doit encore partir, ni de répondre à un
client qui affirme n'avoir rien reçu.

## 6. Impact - tests

Aucun cas de test n'existe à ce jour. Ce CR fixe le périmètre des premiers
`CASE-CANCEL-*` à écrire :

| Cas de test | Impact |
|---|---|
| `CASE-CANCEL-…` *(à écrire)* | mise en alerte d'un créneau, message envoyé la veille à 18h par SMS et par e-mail |
| `CASE-CANCEL-…` *(à écrire)* | sortie maintenue : aucun second message |
| `CASE-CANCEL-…` *(à écrire)* | sortie annulée : message de confirmation 2 heures avant le départ |
| `CASE-CANCEL-…` *(à écrire)* | annulation : remboursement intégral, sans choix proposé |
| `CASE-BOOKING-…` *(à écrire)* | réservation sur un créneau en alerte : risque signalé avant validation |
| `CASE-BOOKING-…` *(à écrire)* | client renonçant après une alerte : remboursement intégral, y compris si la sortie part |
| `CASE-BOOKING-…` *(à écrire)* | numéro de mobile invalide refusé au formulaire |

## 7. Impact - code

Aucun composant n'existe encore : sans objet. Composants à anticiper au
découpage : planificateur d'envois différés (rappel, alerte, confirmation),
adaptateur d'envoi de SMS, adaptateur d'envoi d'e-mails, machine à états de
la sortie.

## 8. Effets de bord identifiés

- **L'avoir change de fait générateur, et son émission n'était spécifiée
  nulle part.** Le client le situe sans ambiguïté (`CR-05/Q11`) : l'avoir est
  l'une des trois issues d'une annulation à l'initiative du client, convenue
  par téléphone puis validée par le gérant depuis son espace de gestion.
  Mais les documents actuels l'accrochent à l'annulation météo (`REQ-050`),
  et la seule spécification qui produisait un code était `SPEC-CANCEL-04`
  AC-4, réécrite par ce CR. Résultat : l'usage du code est spécifié
  (`SPEC-BOOKING-10`, `REQ-051`), sa création ne l'est plus. D'où
  `SPEC-ADMIN-06`, qui porte l'action de validation en back-office, la
  négociation téléphonique restant hors périmètre applicatif.
- **Le silence vaut maintien, sans filet.** Le client assume qu'un message
  non délivré relève de la responsabilité du destinataire. Aucune preuve de
  délivrance n'est donc conservée et aucune relance n'est prévue, alors que
  l'appel téléphonique jouait jusqu'ici ce rôle. La conséquence doit
  apparaître dans les informations légales du site, pas seulement dans une
  spécification.
- **Le remboursement intégral après alerte est réversible dans un seul
  sens.** Un client peut renoncer dès l'alerte, être remboursé, et voir la
  sortie partir sans lui. Deux clients d'un même créneau maintenu auront donc
  été traités différemment, et rien n'empêche d'utiliser l'alerte pour
  échapper au barème dégressif.
- **Un créneau en alerte continue de se vendre.** Pour le créneau de 14h,
  les réservations ferment à midi le jour même, soit à la minute exacte où
  part le message de confirmation d'annulation. Un client peut donc acheter
  une place quelques secondes avant que sa sortie ne soit annulée.
- **Le SMS introduit une dépendance externe payante et une donnée
  critique.** Le mobile devient indispensable, alors que la durée de
  conservation retenue est de trois mois et que le consentement à être
  contacté par SMS n'a jamais été évoqué.
- **Trois messages automatiques coexistent désormais**, rappel, alerte et
  confirmation, tous bilingues, et aucun des trois n'a reçu son texte type.
- **Un artefact commité hier devient faux.** Le diagramme de séquence de
  l'annulation météo montre l'appel et le choix du client.

## 9. Ce que nous ne ferons pas dans le temps restant

Assumé, et à annoncer au client lors de la présentation de J10.

- Aucune détection météo automatique ni proposition d'alerte : la mise en
  alerte reste un geste du gérant, comme l'annulation.
- Aucune preuve de délivrance, aucune relance, aucun canal de repli
  automatique en cas d'échec d'envoi.
- Aucune intégration technique de WhatsApp.
- Aucun message associé à une annulation faute de 6 inscrits, tant que le
  client n'a pas répondu.
- Aucun éditeur de contenu dans l'espace de gestion : les trois messages
  restent des gabarits fixes, bilingues, modifiables en configuration et non
  par le gérant.
- Aucune levée d'alerte notifiée au client : si la sortie est maintenue, le
  silence est la seule réponse, conformément à la demande.

## 10. Ordre d'exécution retenu

| # | Étape | Qui |
|---|---|---|
| 1 | Formaliser l'échange en `compte-rendu-entretien-05.md` | équipe, **fait** |
| 2 | Mettre à jour `docs/cahier-des-charges.md` (v5) : `REQ-023`, `REQ-024` inversées, `REQ-026`, `REQ-019`, `REQ-050`, `REQ-009` modifiées, `REQ-052` à `REQ-058` ajoutées, §8, §11, §12 et §13 mis à jour | équipe |
| 3 | Reprendre `specs/cancel.md` (`SPEC-CANCEL-01` à `05`, création de `SPEC-CANCEL-06`), `specs/admin.md` (`SPEC-ADMIN-06`), `specs/booking.md` (`SPEC-BOOKING-01`, `SPEC-BOOKING-10`), `specs/non-fonctionnel.md` (`SPEC-NFR-03`, `SPEC-NFR-04`) | équipe |
| 4 | Mettre à jour `docs/uml/domain.puml` et `use-cases.puml`, refaire `sequences/annuler-creneau-meteo.puml`, ajouter une séquence d'alerte | équipe |
| 5 | Créer `adr/ADR-004` pour le choix du prestataire d'envoi de SMS, `ADR-002` et `ADR-003` étant déjà pris | équipe |
| 6 | Régénérer `docs/traceability.md` via `./tools/traceability.sh` et vérifier l'absence de rupture nouvelle | équipe |
| 7 | Consigner au journal J5 l'inversion du refus IA sur le SMS et l'erreur de transcription de `CR-02/Q04` | équipe |
| 8 | Faire relire `CR-05` par la personne ayant mené l'échange, et corriger `CR-02/Q04` d'une note de rectification datée plutôt que d'une réécriture silencieuse | équipe |
