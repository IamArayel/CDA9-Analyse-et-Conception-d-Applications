# Spécifications — BOOKING (réservation en ligne)

**Domaine :** `BOOKING`
**Source :** `docs/cahier-des-charges.md` (v3), cas d'usage Must have « réserver
et payer une sortie en ligne », complété par `docs/compte-rendu-entretien-03.md`
(CR-03) et `docs/impact-CR-001.md`.

Chaque spécification cite au moins une exigence (`REQ-xxx`) du cahier des
charges. Les critères d'acceptation sont écrits pour être directement
transposables en cas de test (`tests/cases/CASE-BOOKING-nn.md`, J4).

---

## SPEC-BOOKING-01 — Formulaire et validité d'une réservation standard

**Exigences :** REQ-001, REQ-008, REQ-009, REQ-015, REQ-036

**Description.** Le client réserve une sortie en renseignant nom, prénom,
e-mail, téléphone, nombre d'adultes et nombre d'enfants, créneau et type de
sortie. Aucune information supplémentaire n'est demandée — en particulier,
**l'âge exact de chaque enfant n'est pas un champ du formulaire** (REQ-009
ne demande qu'un nombre d'enfants, pas une liste d'âges). Le formulaire en
ligne est l'unique point d'entrée pour toute nouvelle réservation : le
gérant n'a pas d'écran de saisie manuelle d'une nouvelle réservation dans
l'espace de gestion (cf. `specs/admin.md`).

**Règles.**
- Nombre total de participants (adultes + enfants) ≥ 1 (REQ-001) : une
  réservation individuelle d'une seule place est acceptée, sans minimum de
  personnes — *corrigé en v3 : la v1/v2 imposait à tort un minimum de 2
  personnes, sur une lecture trop stricte de CR-01/Q02 (voir CR-03/Q04)*.
- L'accès est interdit aux enfants de moins de 4 ans (REQ-008) — **règle
  affichée en avertissement sur le site**, pas un contrôle de saisie : en
  l'absence de champ d'âge par enfant, l'application ne peut pas valider
  cette règle automatiquement ; le respect de la limite d'âge relève de
  l'information donnée au client au moment de réserver, pas d'une
  validation bloquante du formulaire.
- Champs obligatoires et exhaustifs : nom, prénom, e-mail, téléphone,
  nombre d'adultes, nombre d'enfants, créneau, type de sortie (REQ-009).
- Le tarif enfant s'applique de 4 à 11 ans, le tarif adulte à partir de 12
  ans (REQ-015) — appliqué globalement au nombre d'enfants déclaré, sans
  distinction d'âge individuel puisque celui-ci n'est pas collecté.

**Critères d'acceptation.**
- Étant donné un formulaire avec un seul participant au total, quand le
  client valide, alors la réservation est acceptée (aucun minimum de
  personnes n'est imposé à une réservation individuelle).
- Étant donné le formulaire de réservation, quand le client consulte les
  conditions d'accès (avant ou pendant la réservation), alors un
  avertissement clair indique que l'accès est interdit aux enfants de
  moins de 4 ans.
- Étant donné un formulaire complet et valide, quand le client valide,
  alors la réservation est créée à l'état « en attente de paiement » —
  sans qu'aucun âge individuel d'enfant n'ait été demandé ni vérifié par
  le système.
- Étant donné un champ obligatoire manquant, quand le client valide, alors
  la réservation est refusée avec indication du champ concerné.

## SPEC-BOOKING-02 — Créneaux et types de sortie proposés selon la saison

**Exigences :** REQ-010, REQ-011, REQ-038

**Description.** Trois créneaux par jour (7h, 10h, 14h), sorties d'environ
3 heures. Les sorties dauphins sont proposées toute l'année ; les sorties
baleines uniquement du 15 juin au 31 octobre. Aucun créneau n'est proposé
le 25 décembre ni le 1ᵉʳ janvier, jours de fermeture (REQ-038) ; les
horaires d'ouverture et jours de fermeture, modifiables depuis l'espace de
gestion, sont spécifiés en `specs/admin.md` (`SPEC-ADMIN-04`).

**Critères d'acceptation.**
- Étant donné une date hors saison baleines (ex. 1er décembre), quand le
  client consulte les créneaux, alors seule la sortie dauphins est
  proposée sur les 3 créneaux.
- Étant donné une date en saison (ex. 1er août), quand le client consulte
  les créneaux, alors dauphins et baleines sont tous deux proposés.
- Étant donné le 25 décembre ou le 1ᵉʳ janvier, quand le client consulte
  les créneaux de ce jour, alors aucun créneau n'est proposé à la
  réservation.

## SPEC-BOOKING-03 — Capacité, seuil minimal et places disponibles en temps réel

**Exigences :** REQ-002, REQ-003, REQ-004, REQ-007, REQ-033

**Description.** Chaque bateau a une capacité fixe (Ti Kap : 12 places, Le
Grand Bleu : 24 places — flotte non modifiable, REQ-033). Le nombre de
places restantes s'affiche en temps réel au moment de la réservation. Une
sortie n'est maintenue qu'à partir de 6 inscrits, contrôlé à J-24h ; en deçà,
la sortie est annulée et les clients déjà inscrits sont remboursés
intégralement. Un seul bateau à la fois peut être engagé sur une sortie
baleines (un seul naturaliste disponible).

**Critères d'acceptation.**
- Étant donné un créneau à 3 places restantes sur le Ti Kap, quand un
  client tente de réserver pour 4 personnes (adultes + enfants confondus),
  alors la réservation est refusée — la capacité n'est jamais dépassée.
- Étant donné deux réservations concurrentes visant la dernière place
  disponible, quand elles sont soumises au même instant, alors une seule
  aboutit et l'autre est rejetée (cf. `architecture.md` §5, contrainte de
  concurrence).
- Étant donné un créneau avec moins de 6 inscrits à J-24h, quand le
  contrôle automatique s'exécute, alors la sortie est annulée et chaque
  client inscrit est remboursé intégralement.
- Étant donné une sortie baleines déjà affectée à un bateau sur un
  créneau donné, quand un client tente de réserver une sortie baleines sur
  l'autre bateau au même créneau, alors la réservation est refusée.
- Étant donné une réservation confirmée, quand un autre client consulte le
  même créneau, alors le nombre de places affiché diminue sans qu'il ait
  besoin de recharger manuellement la page.

## SPEC-BOOKING-04 — Fermeture des réservations en ligne selon le créneau

**Exigences :** REQ-005

**Critères d'acceptation.**
- Étant donné le créneau de 14h le jour J, quand l'heure dépasse midi ce
  même jour, alors ce créneau n'est plus réservable en ligne.
- Étant donné les créneaux de 7h ou 10h, quand l'heure dépasse midi la
  veille, alors ces créneaux ne sont plus réservables en ligne.

## SPEC-BOOKING-05 — Privatisation d'un bateau

**Exigences :** REQ-006, REQ-014

**Critères d'acceptation.**
- Étant donné un client qui choisit la privatisation, quand il valide sa
  réservation, alors le bateau entier est bloqué sur le créneau choisi
  (matin ou après-midi), au tarif forfaitaire (600 € Ti Kap, 1 100 € Le
  Grand Bleu), sans tarif préférentiel par personne.
- Étant donné un bateau privatisé sur un créneau, quand un autre client
  tente de réserver une place sur ce même bateau/créneau, alors la
  réservation est refusée.

## SPEC-BOOKING-06 — Tarification standard par type de sortie et nombre de personnes

**Exigences :** REQ-012, REQ-014, REQ-015

**Critères d'acceptation.**
- Étant donné une réservation standard sortie baleines, quand le tarif est
  calculé, alors chaque adulte compte 65 € et chaque enfant déclaré 40 €.
- Étant donné une réservation standard sortie dauphins, quand le tarif est
  calculé, alors chaque adulte compte 50 € et chaque enfant déclaré 30 €.

## SPEC-BOOKING-07 — Paiement en ligne intégral par carte, délégué à un prestataire tiers

**Exigences :** REQ-017, REQ-018

**Description.** Le paiement, intégral et par carte bancaire uniquement, est
exigé au moment de la réservation. Il est délégué au prestataire Stripe
(`ADR-001`) : aucune donnée de paiement sensible n'est stockée par
l'application.

**Critères d'acceptation.**
- Étant donné une réservation valide, quand le client procède au paiement,
  alors le montant total est exigé en carte bancaire, sans acompte ni
  autre moyen de paiement en ligne.
- Étant donné un paiement refusé ou abandonné, quand la transaction
  échoue, alors la réservation n'est pas confirmée et les places ne sont
  pas décomptées.
- Étant donné un paiement accepté, quand la transaction est confirmée par
  Stripe, alors la réservation passe à l'état « confirmée » et les places
  correspondantes sont décomptées de la capacité du créneau.

## SPEC-BOOKING-08 — Accessibilité multi-support

**Exigences :** REQ-035, REQ-101

**Critères d'acceptation.**
- Étant donné un client sur mobile, tablette ou ordinateur, quand il
  effectue le parcours complet (consultation des places → formulaire →
  paiement), alors ce parcours reste utilisable sans défilement horizontal
  ni élément inaccessible, y compris en connexion mobile standard (4G).

## SPEC-BOOKING-09 — Achat et usage d'un bon cadeau

**Exigences :** REQ-043, REQ-044, REQ-045, REQ-046, REQ-047, REQ-048, REQ-049

**Description.** Ajoutée en v3 — voir `docs/impact-CR-001.md`. Un bon
cadeau est acheté sur la plateforme pour un type de sortie déterminé
(baleines, dauphins ou privatisation) ; il est utilisable, exclusivement
sur la plateforme, par son bénéficiaire (potentiellement une personne
différente de l'acheteur), pendant 1 an à compter de l'achat, une seule
fois. Au moment de payer une réservation du type correspondant, le
bénéficiaire renseigne le code du bon : si le prix de la sortie dépasse le
montant du bon, il paie la différence en carte bancaire (REQ-017) ; si le
bon dépasse le prix, le surplus est perdu, sans remboursement.
L'exclusivité plateforme (aucun achat ni usage par téléphone) est une
hypothèse d'équipe non confirmée par le client — voir `CR-03, §8`.

**Critères d'acceptation.**
- Étant donné un client qui achète un bon cadeau pour un type de sortie
  donné, quand le paiement est confirmé, alors un code unique est généré,
  rattaché à ce type de sortie et à une date d'expiration à 1 an.
- Étant donné un bon cadeau valide et non utilisé, quand son bénéficiaire
  saisit le code au paiement d'une réservation du même type de sortie,
  alors le montant du bon est déduit du montant dû.
- Étant donné un bon cadeau dont le montant est inférieur au prix de la
  sortie réservée, quand le paiement est finalisé, alors la différence est
  exigée en carte bancaire.
- Étant donné un bon cadeau dont le montant est supérieur au prix de la
  sortie réservée, quand le paiement est finalisé, alors aucun
  remboursement du surplus n'est proposé ni effectué.
- Étant donné un bon cadeau déjà utilisé une fois, quand son code est
  ressaisi pour une nouvelle réservation, alors il est refusé.
- Étant donné un bon cadeau dont la date d'expiration (1 an après l'achat)
  est dépassée, quand son code est saisi, alors il est refusé.
- Étant donné un bon cadeau rattaché à un type de sortie, quand son code
  est saisi pour réserver un autre type de sortie, alors il est refusé.

## SPEC-BOOKING-10 — Saisie d'un code d'avoir au paiement

**Exigences :** REQ-050

**Description.** Ajoutée en v3 — voir `docs/impact-CR-001.md`. Un avoir
accordé par le gérant après une annulation météo (`specs/cancel.md`,
`SPEC-CANCEL-04`) prend la forme d'un code de réduction unique, distinct
d'un bon cadeau (`SPEC-BOOKING-09`) : sa valeur est décidée au cas par cas
par le gérant, sans type de sortie imposé ni durée de validité fixée par
une règle client.

**Critères d'acceptation.**
- Étant donné un code d'avoir valide, quand le client le saisit au moment
  de payer une réservation future, alors son montant est déduit du montant
  dû, selon les mêmes règles de différentiel et de surplus qu'un bon
  cadeau (`SPEC-BOOKING-09`).
- Étant donné un code d'avoir déjà utilisé, quand il est ressaisi, alors il
  est refusé.

## SPEC-BOOKING-11 — Site bilingue français/anglais

**Exigences :** REQ-040, REQ-102

**Critères d'acceptation.**
- Étant donné un client sur le site, quand il choisit la langue anglaise,
  alors l'ensemble du parcours de réservation (places disponibles,
  formulaire, paiement) s'affiche en anglais, sans contenu resté en
  français.
- Étant donné un client sur le site, quand il n'exprime aucun choix de
  langue, alors le français reste la langue par défaut.

---

## Hors périmètre applicatif

Ces exigences existent dans le cahier des charges mais ne donnent lieu à
aucune fonctionnalité dans cette version — mentionnées ici pour que la
chaîne de traçabilité (`tools/traceability.sh`) ne les signale pas comme
non couvertes, sans qu'elles ne deviennent des specs applicatives :

- **REQ-013** (Won't) — les suppléments personnalisables (ex. champagne à
  bord) restent vendus uniquement par téléphone ; aucune vente en ligne.
- **REQ-037** (Won't) — la répartition des passagers entre les deux
  bateaux, quand plusieurs sont disponibles, reste une décision manuelle du
  gérant, hors outil ; aucune règle de répartition automatique.
