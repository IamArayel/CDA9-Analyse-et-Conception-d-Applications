# Spécifications — CANCEL (annulation météo, à l'initiative du gérant)

**Domaine :** `CANCEL`
**Source :** `docs/cahier-des-charges.md` (v3), cas d'usage Must have
« annuler un créneau météo et informer les clients concernés », complété
par `docs/compte-rendu-entretien-03.md` (CR-03) et `docs/impact-CR-001.md`.

L'annulation d'un **créneau** pour raison météo est décidée par le gérant.
Elle ne doit pas être confondue avec l'annulation/le report d'**une
réservation individuelle** à l'initiative du client, qui reste hors
périmètre applicatif (voir en bas de ce fichier).

---

## SPEC-CANCEL-01 — Visualisation des clients inscrits avant décision

**Exigences :** REQ-022

**Critères d'acceptation.**
- Étant donné un créneau à venir, quand le gérant le consulte depuis
  l'espace de gestion, alors la liste des clients inscrits (nom, contact,
  nombre de participants) s'affiche avant toute décision d'annulation.

## SPEC-CANCEL-02 — Annulation météo décidée manuellement par le gérant

**Exigences :** REQ-021, REQ-022

**Description.** La décision d'annuler un créneau pour raison météo
appartient uniquement au gérant ; elle n'est jamais déclenchée
automatiquement, et n'a lieu qu'après consultation de la situation du
créneau (`SPEC-CANCEL-01`).

**Critères d'acceptation.**
- Étant donné un créneau consulté, quand le gérant décide d'annuler pour
  raison météo, alors le créneau passe à l'état « annulé » et n'apparaît
  plus disponible côté client.
- Étant donné un créneau non consulté explicitement par le gérant, alors
  aucune annulation ne se déclenche automatiquement (aucune règle météo
  automatisée n'existe dans l'application).

## SPEC-CANCEL-03 — Répercussion en temps réel côté client de l'annulation

**Exigences :** REQ-021, REQ-004

**Critères d'acceptation.**
- Étant donné un créneau annulé par le gérant, quand un client consulte ce
  créneau après l'annulation, alors il apparaît indisponible/annulé sans
  qu'un redéploiement ou une action technique manuelle soit nécessaire.
- Étant donné un client en train de consulter les places disponibles au
  moment où le gérant annule le créneau, alors l'affichage se met à jour
  pour refléter l'indisponibilité, de la même manière que la mise à jour
  temps réel de la capacité après une réservation (`SPEC-BOOKING-03`).

## SPEC-CANCEL-04 — Contact et enregistrement du choix de chaque client concerné

**Exigences :** REQ-023, REQ-024, REQ-026

**Critères d'acceptation.**
- Étant donné un créneau annulé, quand le gérant contacte par téléphone
  chaque client inscrit, alors il peut enregistrer pour chacun le choix
  retenu (report, avoir ou remboursement) dans l'espace de gestion.
- Étant donné une proposition de report tenant compte des disponibilités
  et de la météo, quand le client la refuse, alors gérant et client
  s'accordent directement par téléphone sur un remplacement — aucune
  procédure automatisée de désaccord n'est prévue dans l'application.

**Note (v3).** La matérialisation de l'avoir — un code de réduction
unique, saisi par le client au moment de payer une réservation future
(`REQ-050`) — est désormais spécifiée dans `specs/booking.md`, plutôt que
laissée à un simple choix enregistré sans mécanique définie.

## SPEC-CANCEL-05 — Message de rappel automatisé à J-1

**Exigences :** REQ-025, REQ-042

**Description.** Ajoutée en v3, sur confirmation du contenu et de
l'automatisation par le client (`CR-03/Q03`) — voir `docs/impact-CR-001.md`.
Un message type, incluant les conditions météo prévues et la liste des
affaires à prévoir, est envoyé automatiquement par le site à chaque client
inscrit, par défaut 24 heures avant sa sortie. Le gérant peut personnaliser
cet horaire d'envoi depuis l'espace de gestion.

**Critères d'acceptation.**
- Étant donné une réservation confirmée pour une sortie à venir, quand
  l'horaire d'envoi configuré (par défaut J-1) est atteint, alors le
  message type est envoyé automatiquement au client, sans action manuelle
  du gérant.
- Étant donné l'espace de gestion, quand le gérant modifie l'horaire
  d'envoi du message de rappel, alors les prochains envois respectent le
  nouvel horaire.

---

## Hors périmètre applicatif

Ces exigences existent dans le cahier des charges mais ne donnent lieu à
aucune fonctionnalité dans cette version — mentionnées ici pour que la
chaîne de traçabilité (`tools/traceability.sh`) ne les signale pas comme
non couvertes, sans qu'elles ne deviennent des specs applicatives :

- **REQ-019, REQ-020** — l'annulation et le report d'une réservation à
  l'initiative du client se font exclusivement par téléphone avec le
  gérant, jamais en autonomie sur le site (cf. CdC §6, hors périmètre).
  Aucune fonctionnalité « annuler ma réservation » côté client. Le barème
  de remboursement dégressif (R-05 / REQ-019 : 100 % au-delà de 7 jours,
  75 % entre 7 j et 48 h, 50 % entre 48 h et 24 h) reste une règle métier
  connue et appliquée manuellement par le gérant au moment du
  remboursement, hors de toute automatisation applicative pour cette
  version.
- **REQ-027** (Should) — WhatsApp comme canal de secours reste un usage
  manuel du gérant en dehors de l'outil ; aucune intégration technique
  WhatsApp n'est prévue dans cette version.
