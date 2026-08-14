# Spécifications - NFR (exigences transverses non fonctionnelles)

**Domaine :** `NFR`
**Source :** `docs/cahier-des-charges.md` (v5), §10 « Exigences non
fonctionnelles », complété par `docs/compte-rendu-entretien-03.md` (CR-03).
**Gabarit :** `docs/cle-specification.md` ; chaque spécification en reprend
les sept rubriques, dans le même ordre.

Ces exigences sont transverses aux trois cas d'usage Must have plutôt que
rattachées à un seul domaine. La plupart sont marquées « déduit » dans le
cahier des charges, faute d'avoir été discutées avec le client : les règles
ci-dessous décrivent l'hypothèse retenue par l'équipe en attendant, pas un
engagement client.

**Convention de traçabilité.** Les exigences couvertes sont listées une par
ligne, immédiatement sous le titre de la spécification. `tools/traceability.sh`
rattache une exigence au dernier identifiant `SPEC-xxx-nn` rencontré au-dessus
d'elle et ne retient que la première de chaque ligne : aucune exigence n'est
donc citée ailleurs que dans cette liste, et les renvois vers une autre
spécification n'apparaissent qu'après elle.

**Consigne de revue IA**, utilisée pour la rubrique « Revue IA » de chaque
spécification de ce fichier :

> Analyse cette spécification. Recherche les ambiguïtés, contradictions,
> comportements non définis, cas limites oubliés et exigences impossibles à
> tester. Ne réécris pas la spécification.

Les remarques refusées sont aussi reportées dans `docs/journal.md`.

---

## SPEC-NFR-01 - Volumétrie et pics de charge

**Exigences :**

- `REQ-100` : usage faible hors saison, pic du 15 juin au 31 octobre.

**Statut :** revue IA faite
**Version :** v1

### Règle

L'application absorbe sans dégradation perceptible les volumes de la saison
haute, soit quelques dizaines de réservations simultanées.

> Un seul gérant, deux bateaux, trois créneaux par jour : le pic tient dans
> quelques dizaines de réservations simultanées, pas dans des milliers.

### Portée

Couvre le dimensionnement attendu du parcours client en période de pointe.
Ne couvre ni la disponibilité contractuelle, ni la sauvegarde, ni la reprise
après incident.

- Ne couvre pas la concurrence sur la dernière place, qui est une règle
  fonctionnelle : `SPEC-BOOKING-03`.
- Ne couvre pas le coût de l'hébergement retenu : `SPEC-NFR-03`.
- Ne couvre pas la fréquence de mise à jour ni l'exploitation :
  `SPEC-NFR-05`.

### Scénarios nominaux

```gherkin
Étant donné une journée de saison haute
Quand une trentaine de clients consultent et réservent en même temps
Alors chaque écran du parcours de réservation répond en moins de 2 secondes
Et aucune réservation n'est perdue
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | plusieurs clients visant la dernière place d'un créneau | une seule réservation aboutit, cf. `SPEC-BOOKING-03` |
| 2 | export du planning demandé en pleine pointe | l'export n'empêche pas les réservations en cours |
| 3 | envoi groupé des messages de rappel | les envois n'affectent pas le temps de réponse du parcours client |
| 4 | journée hors saison, trafic quasi nul | aucun coût d'infrastructure supplémentaire |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Volumétrie chiffrée : le client n'a fourni aucun chiffre. Hypothèse
  retenue : le pic est borné par la capacité physique de la flotte, soit
  36 places par créneau et 108 par jour.
- Seuil de « dégradation perceptible » : hypothèse retenue, 2 secondes de
  temps de réponse sur les écrans du parcours de réservation, valeur
  d'équipe et non engagement client.

### Critères d'acceptation

- [ ] AC-1 : avec 30 parcours de réservation simultanés, chaque écran répond
      en moins de 2 secondes.
- [ ] AC-2 : aucune réservation confirmée n'est perdue lors d'un tel pic.
- [ ] AC-3 : un export de planning lancé pendant le pic n'interrompt aucun
      parcours de réservation.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « Pas de dégradation perceptible » n'est pas mesurable et ne peut pas donner lieu à un test | acceptée | seuil chiffré de 2 secondes et charge de 30 parcours simultanés retenus comme hypothèse d'équipe, explicitement non validés par le client |
| « Quelques dizaines de réservations simultanées » n'était rattaché à aucune borne métier | acceptée | borne déduite de la capacité de la flotte écrite dans « Ce qui n'est pas défini » |
| Ajouter une exigence de disponibilité annuelle | refusée | aucun engagement de disponibilité n'a été discuté avec le client, et l'hébergement mutualisé retenu ne permet pas de le garantir ; écrire un taux serait une promesse invérifiable |

## SPEC-NFR-02 - Site bilingue français et anglais

**Exigences :**

- `REQ-040` : consultation et réservation en français ou en anglais.
- `REQ-102` : aucun contenu non traduit, messages automatiques compris.

**Statut :** revue IA faite
**Version :** v2

### Règle

Tout contenu présenté au client existe en français et en anglais, y compris
les messages envoyés automatiquement.

> Réponse du client au troisième entretien : « Anglais Français ». La v2
> retenait le français seul comme hypothèse par défaut, faute d'avoir posé
> la question.

### Portée

Couvre l'ensemble des contenus destinés au client. Ne couvre ni l'espace de
gestion, ni la devise.

- Ne couvre pas le parcours de réservation lui-même, spécifié dans le
  domaine BOOKING : `SPEC-BOOKING-11`.
- Ne couvre pas le message de rappel avant la sortie : `SPEC-CANCEL-05`.
- Ne couvre pas l'espace de gestion, utilisé par le seul gérant :
  `SPEC-ADMIN-01`.
- Ne couvre pas la multidevise, écartée dans `docs/impact-CR-001.md` §9.

### Scénarios nominaux

```gherkin
Étant donné un client ayant choisi l'anglais
Quand il parcourt le site et reçoit ses messages automatiques
Alors l'intégralité des contenus lui parvient en anglais
Et aucun libellé, aucun e-mail et aucune erreur ne reste en français
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | contenu ajouté après la livraison sans traduction | manque détectable par une vérification des libellés des deux langues |
| 2 | message automatique envoyé à un client ayant choisi l'anglais | envoyé en anglais |
| 3 | libellé saisi par le gérant, par exemple un nom de bateau | conservé tel quel dans les deux langues |
| 4 | montants et dates | montants en euros dans les deux langues, dates au format de la langue affichée |
| 5 | documents produits par le prestataire de paiement | hors du contrôle du projet, langue imposée par le prestataire |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Langue des documents émis par le prestataire de paiement, justificatif
  compris : hors du périmètre maîtrisé. Hypothèse retenue : la langue
  proposée par le prestataire est acceptée telle quelle.
- Traduction des mentions légales et des conditions générales : leur contenu
  n'existe pas encore. Hypothèse retenue : elles suivront la même règle des
  deux langues.

### Critères d'acceptation

- [ ] AC-1 : aucun contenu du site ne reste non traduit dans l'une des deux
      langues livrées.
- [ ] AC-2 : les messages automatiques sont envoyés dans la langue choisie
      par le client au moment de sa réservation.
- [ ] AC-3 : le français s'applique lorsque le client n'exprime aucun choix.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| La langue des messages automatiques n'était pas rattachée au choix du client, seulement à leur existence en deux langues | acceptée | AC-2 ajouté, cohérent avec l'effet de bord identifié dans l'analyse d'impact |
| Les documents émis par le prestataire de paiement échappent à l'exigence, ce qui la rend intenable telle quelle | acceptée | cas limite 5 et hypothèse ajoutés, l'exigence est bornée au contenu maîtrisé |
| Le recouvrement avec la spécification du parcours bilingue n'était pas explicite | acceptée | la portée renvoie désormais à la spécification du domaine BOOKING, qui porte les critères du tunnel de réservation |
| Traduire aussi l'espace de gestion | refusée | le gérant est l'unique utilisateur et il est francophone ; traduire son back-office serait un coût sans bénéficiaire |

## SPEC-NFR-03 - Hébergement et coût

**Exigences :**

- `REQ-103` : solution d'hébergement à faible coût.

**Statut :** revue IA faite
**Version :** v1

### Règle

L'application est hébergée sur une solution à faible coût, dont le montant
mensuel est documenté avant la mise en production.

> Hébergement mutualisé Hostinger, environ 2,99 € par mois, cf.
> `docs/adr/ADR-001-stack.md`.

### Portée

Couvre le coût et le choix d'hébergement. Ne couvre ni le budget global du
projet, ni les frais du prestataire de paiement.

- Ne couvre pas les commissions du prestataire de paiement :
  `SPEC-BOOKING-07`.
- Ne couvre pas la charge attendue de l'hébergement : `SPEC-NFR-01`.
- Ne couvre pas la mise en production elle-même : `SPEC-NFR-05`.
- Le budget total du projet reste en attente de validation client, cf.
  cahier des charges §11, question 2.

### Scénarios nominaux

```gherkin
Étant donné le choix d'hébergement documenté dans ADR-001
Quand l'équipe présente la solution au client
Alors le coût mensuel annoncé est celui de l'offre retenue
Et il est inférieur à 5 € par mois
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | hausse tarifaire de l'hébergeur | à signaler au client, le choix étant justifié par le coût |
| 2 | dépassement des ressources de l'offre mutualisée en saison haute | à réévaluer avec la charge mesurée, cf. `SPEC-NFR-01` |
| 3 | nom de domaine et certificat | coûts à documenter au même titre que l'hébergement |

### Ce qui n'est pas défini

Assumé au 2026-08-12.

- Budget total du projet : la question 2 du §11, ouverte depuis le premier
  entretien, a reçu une réponse le 2026-08-14, « budget illimité pour
  l'exercice ». Elle relève du cadre pédagogique et ne vaut pas engagement
  d'exploitation : le choix d'hébergement à faible coût est maintenu, mais
  sa justification n'est plus une contrainte client. Un second coût
  récurrent apparaît en v5, l'envoi de SMS, arbitré dans un ADR à venir.
- Prise en charge de l'abonnement après la livraison : non discutée, liée à
  la question de la maintenance.

### Critères d'acceptation

- [ ] AC-1 : le coût mensuel de l'hébergement est documenté dans une décision
      d'architecture.
- [ ] AC-2 : aucun coût récurrent non documenté n'est engagé pour le client.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| « Faible coût » n'est pas vérifiable sans montant de référence | acceptée | le montant de l'offre retenue est cité dans la règle et un plafond figure au scénario |
| Le nom de domaine et le certificat, non chiffrés, échappent au coût annoncé | acceptée | cas limite 3 ajouté |
| Comparer plusieurs hébergeurs dans la spécification | refusée | la comparaison appartient à la décision d'architecture, pas à la spécification ; la dupliquer créerait deux sources de vérité |

## SPEC-NFR-04 - Données personnelles et durée de conservation

**Exigences :**

- `REQ-105` : collecte minimale, aucune donnée de paiement, conservation limitée.

**Statut :** revue IA faite
**Version :** v1

### Règle

Seules les informations du formulaire de réservation sont collectées, aucune
donnée de paiement n'est stockée, et les données personnelles sont supprimées
ou anonymisées au terme du délai de conservation retenu.

> Nom, prénom, e-mail, téléphone, composition du groupe : rien d'autre n'est
> collecté, et rien n'est conservé au-delà du délai retenu.

### Portée

Couvre la collecte, le stockage et la suppression des données personnelles.
Ne couvre pas le traitement des données de paiement, qui n'entrent jamais
dans l'application.

- Ne couvre pas les champs collectés, qui sont définis fonctionnellement :
  `SPEC-BOOKING-01`.
- Ne couvre pas la délégation du paiement au prestataire tiers :
  `SPEC-BOOKING-07`.
- Ne couvre pas les données affichées au gérant avant une annulation :
  `SPEC-CANCEL-01`.
- Ne couvre pas la durée de vie d'un bon cadeau, qui est une règle métier :
  `SPEC-BOOKING-09`.

### Scénarios nominaux

```gherkin
Étant donné une réservation honorée il y a plus de trois mois
Quand le délai de conservation est atteint
Alors les données personnelles du client sont supprimées ou anonymisées
Et aucune donnée de carte bancaire n'a jamais été stockée
```

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | client titulaire d'un bon cadeau non utilisé, acheté il y a plus de trois mois | les données nécessaires au bon doivent survivre jusqu'à son expiration, voir la rubrique suivante |
| 2 | client demandant la suppression de ses données avant le délai | demande traitée manuellement par le gérant |
| 3 | client demandant l'accès à ses données | même traitement manuel |
| 4 | réservation annulée puis remboursée | soumise au même délai que les autres |
| 5 | obligations comptables portant sur les paiements | portées par le prestataire de paiement, hors de l'application |
| 6 | numéro de mobile d'un client dont la sortie est passée | supprimé au même délai que le reste, alors qu'il a servi à lui envoyer des SMS |

### Ce qui n'est pas défini

Assumé au 2026-08-12, question 4 du §11 du cahier des charges.

- Durée de conservation et point de départ : le client n'a jamais tranché.
  Hypothèse retenue : trois mois après la date de la sortie.
- Conservation des données rattachées à un bon cadeau : un bon vit un an, le
  délai de conservation retenu est de trois mois. Hypothèse retenue : les
  données strictement nécessaires à un bon cadeau non consommé sont
  conservées jusqu'à son expiration, par exception au délai général.
- Procédure d'exercice des droits d'accès et de suppression : aucune
  interface prévue, traitement manuel par le gérant.
- Numéro de mobile : il devient en v5 une donnée de contact indispensable,
  puisqu'il porte l'envoi des SMS. Hypothèse retenue : il suit le même délai
  de conservation que le reste, et sa collecte est signalée au client par une
  mention au formulaire plutôt que par une case à cocher. Question 14 du §11
  du cahier des charges.

### Critères d'acceptation

- [ ] AC-1 : aucun champ hors de ceux du formulaire de réservation n'est
      stocké.
- [ ] AC-2 : aucune donnée de carte bancaire n'est présente dans les données
      de l'application.
- [ ] AC-3 : les données personnelles d'une réservation sont supprimées ou
      anonymisées au terme du délai retenu.
- [ ] AC-4 : les données nécessaires à un bon cadeau non consommé subsistent
      jusqu'à son expiration.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Une conservation de trois mois contredit la validité d'un an d'un bon cadeau : le bon deviendrait inutilisable avant d'expirer | acceptée | cas limite 1, exception écrite dans « Ce qui n'est pas défini » et AC-4 ajoutés ; contradiction inédite, apparue avec la nouveauté du troisième entretien |
| « Conservation minimale envisagée » n'engage à rien et n'est pas testable | acceptée | la règle et AC-3 portent sur une suppression effective au terme du délai retenu, dont la valeur reste une hypothèse écrite |
| Aucune procédure n'existe pour une demande de suppression émanant d'un client | acceptée | cas limites 2 et 3 ajoutés, avec un traitement manuel assumé |
| Construire une interface d'exercice des droits RGPD | refusée | volumétrie de quelques dizaines de clients par semaine et un seul gérant : le traitement manuel est proportionné, et rien n'a été demandé par le client |

## SPEC-NFR-05 - Déploiement

**Exigences :**

- `REQ-106` : fréquence de mise à jour et environnement de recette.

**Statut :** brouillon
**Version :** v1

### Règle

La fréquence des mises à jour et l'existence d'un environnement de recette
restent à définir avec le client ; aucune règle n'est engagée à ce stade.

> Exigence non tranchée : elle est écrite pour être portée à l'ordre du jour
> du prochain entretien, pas pour être testée.

### Portée

Couvre l'organisation des mises en production. Ne couvre ni l'hébergement,
ni la maintenance après livraison.

- Ne couvre pas le choix et le coût de l'hébergement : `SPEC-NFR-03`.
- Ne couvre pas la maintenance après livraison : `SPEC-NFR-06`.
- Ne couvre pas la charge supportée en production : `SPEC-NFR-01`.

### Scénarios nominaux

Aucun scénario n'est écrit : l'exigence n'est pas tranchée, et en inventer un
donnerait l'illusion d'un engagement client qui n'existe pas.

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | correction urgente en pleine saison haute | non défini : aucune procédure convenue avec le client |
| 2 | recette d'une évolution avant mise en ligne | non défini : aucun environnement de recette validé |

### Ce qui n'est pas défini

Assumé au 2026-08-12, cf. `architecture.md` §8 et cahier des charges §11.

- Fréquence des mises à jour, existence d'un environnement de recette,
  responsable de la mise en ligne : aucun de ces points n'a été abordé.
  Hypothèse retenue en attendant : mise en ligne par l'équipe, sans
  environnement de recette distinct pendant la durée du projet.

### Critères d'acceptation

- [ ] AC-1 : la question du déploiement est portée à l'ordre du jour du
      prochain entretien client.
- [ ] AC-2 : la décision retenue est consignée dans `architecture.md` §8
      avant toute mise en production.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Une spécification sans critère vérifiable ne devrait pas exister | acceptée | les deux critères portent sur ce qui est réellement vérifiable aujourd'hui, poser la question et consigner la réponse ; le statut reste « brouillon » |
| L'absence d'environnement de recette est un risque en saison haute | acceptée | cas limite 1 écrit comme non défini plutôt que passé sous silence |
| Retirer cette spécification tant que le client n'a pas répondu | refusée | l'exigence existe au cahier des charges ; la supprimer romprait la chaîne de traçabilité et ferait disparaître une question ouverte du radar |

## SPEC-NFR-06 - Maintenance après livraison

**Exigences :**

- `REQ-107` : responsable et durée de la maintenance après livraison.

**Statut :** brouillon
**Version :** v1

### Règle

Le responsable et la durée de la maintenance après livraison restent à
définir avec le client ; aucun engagement n'est pris à ce stade.

> Exigence non tranchée : elle est écrite pour rester visible, pas pour être
> testée.

### Portée

Couvre le suivi de l'application après la livraison. Ne couvre ni les mises
en production pendant le projet, ni les coûts d'hébergement.

- Ne couvre pas l'organisation des mises à jour : `SPEC-NFR-05`.
- Ne couvre pas le coût récurrent de l'hébergement : `SPEC-NFR-03`.
- Ne couvre pas la formation du gérant à l'outil, jamais évoquée.

### Scénarios nominaux

Aucun scénario n'est écrit : l'exigence n'est pas tranchée, et en inventer un
donnerait l'illusion d'un engagement client qui n'existe pas.

### Cas limites

| # | Situation | Comportement attendu |
|---|---|---|
| 1 | anomalie découverte après la livraison | non défini : aucun interlocuteur ni délai convenu |
| 2 | perte des identifiants de l'espace de gestion après la livraison | non défini, cf. `SPEC-ADMIN-01` |

### Ce qui n'est pas défini

Assumé au 2026-08-12, cf. cahier des charges §11.

- Responsable, durée et périmètre de la maintenance : jamais abordés.
  Hypothèse retenue en attendant : aucune maintenance engagée au-delà de la
  livraison, ce qui doit être dit explicitement au client plutôt que
  découvert après coup.

### Critères d'acceptation

- [ ] AC-1 : la question de la maintenance est portée à l'ordre du jour du
      prochain entretien client.
- [ ] AC-2 : l'absence d'engagement de maintenance est annoncée au client
      lors de la présentation, tant qu'aucune décision contraire n'est prise.

### Revue IA

Consigne utilisée : voir l'en-tête de ce fichier.

| Remarque de l'IA | Décision | Motif |
|---|---|---|
| Un « à définir avec le client » sans échéance ne se règle jamais | acceptée | AC-1 rattache la question au prochain entretien, AC-2 à la présentation de fin de mission |
| La perte des identifiants du compte unique après livraison n'a aucun responsable désigné | acceptée | cas limite 2 ajouté et rattaché à la spécification de connexion, qui porte déjà l'hypothèse d'une réinitialisation par l'équipe technique |
| Proposer un contrat de maintenance forfaitaire dans la spécification | refusée | une spécification décrit ce que le logiciel doit faire, pas une offre commerciale ; le sujet appartient à la relation client |
