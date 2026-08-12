# Spécifications — ADMIN (espace de gestion du gérant)

**Domaine :** `ADMIN`
**Source :** `docs/cahier-des-charges.md` (v3), cas d'usage Must have
« modifier les tarifs et suivre le planning sans ressaisie manuelle »,
complété par `docs/compte-rendu-entretien-03.md` (CR-03) et
`docs/impact-CR-001.md`.

---

## SPEC-ADMIN-01 — Connexion à l'espace de gestion

**Exigences :** REQ-031, REQ-032, REQ-034, REQ-104

**Description.** L'espace de gestion est réservé à un compte unique, celui
du gérant, seul utilisateur prévu de l'outil à ce stade — pas de comptes
multi-utilisateurs (salariés, capitaines).

**Critères d'acceptation.**
- Étant donné l'espace de gestion, quand une tentative de connexion est
  soumise, alors seul le compte unique du gérant (e-mail + mot de passe)
  y a accès.
- Étant donné un mot de passe de moins de 8 caractères, ou sans majuscule,
  minuscule, chiffre ou caractère spécial, quand ce mot de passe est
  défini, alors il est refusé.
- Étant donné des identifiants incorrects, quand une tentative de
  connexion est soumise, alors l'accès est refusé.

## SPEC-ADMIN-02 — Modification des tarifs

**Exigences :** REQ-016, REQ-028

**Critères d'acceptation.**
- Étant donné l'espace de gestion, quand le gérant modifie un tarif
  (adulte/enfant par type de sortie, ou privatisation), alors le nouveau
  tarif s'applique aux réservations futures sans modifier le montant des
  réservations déjà payées.

## SPEC-ADMIN-03 — Export du planning des réservations

**Exigences :** REQ-029

**Critères d'acceptation.**
- Étant donné l'espace de gestion, quand le gérant demande l'export du
  planning, alors un document imprimable (PDF) listant les réservations
  par créneau est généré.

## SPEC-ADMIN-04 — Gestion des horaires d'ouverture et des jours de fermeture

**Exigences :** REQ-038, REQ-039

**Description.** Ajoutée en v3 (`CR-03/Q01`) — voir `docs/impact-CR-001.md`.
L'entreprise est fermée le 25 décembre et le 1ᵉʳ janvier. Le gérant peut
modifier ces jours de fermeture, ainsi que les horaires d'ouverture, depuis
une section dédiée de l'espace de gestion ; l'effet côté client (aucun
créneau proposé un jour de fermeture) est spécifié en `specs/booking.md`
(`SPEC-BOOKING-02`).

**Critères d'acceptation.**
- Étant donné l'espace de gestion, quand le gérant consulte la section
  horaires, alors le 25 décembre et le 1ᵉʳ janvier apparaissent comme jours
  de fermeture par défaut.
- Étant donné l'espace de gestion, quand le gérant ajoute ou retire un jour
  de fermeture, alors la disponibilité des créneaux côté client (`SPEC-BOOKING-02`)
  reflète ce changement sans intervention technique.

## SPEC-ADMIN-05 — Création d'un nouveau bateau

**Exigences :** REQ-041

**Description.** Ajoutée en v3 (`CR-03/Q06`) — voir `docs/impact-CR-001.md`.
Capacité anticipée par le client pour une évolution future de la flotte ;
aucun besoin immédiat à ce jour (la flotte compte toujours deux bateaux,
`REQ-033`). Le formulaire de création se limite à un nom et une capacité,
par hypothèse d'équipe non confirmée par le client (`CR-03, §6-2`, `§8`) :
aucun champ de types de sorties compatibles n'est prévu, tout bateau créé
étant considéré habilité à tous les types de sortie proposés.

**Critères d'acceptation.**
- Étant donné l'espace de gestion, quand le gérant crée un nouveau bateau
  avec un nom et une capacité, alors ce bateau apparaît immédiatement dans
  les créneaux proposés côté client, selon les mêmes règles de capacité que
  les bateaux existants (cf. `specs/booking.md`, disponibilité en temps
  réel).
- Étant donné un nouveau bateau créé, quand un client consulte les places
  disponibles, alors la capacité affichée correspond à celle saisie par le
  gérant.

---

## Hors périmètre applicatif

Ces exigences existent dans le cahier des charges mais ne donnent lieu à
aucune fonctionnalité dans cette version — mentionnées ici pour que la
chaîne de traçabilité (`tools/traceability.sh`) ne les signale pas comme
non couvertes, sans qu'elles ne deviennent des specs applicatives :

- **REQ-030** (Won't, sauf REQ-041) — l'espace de gestion ne permet ni de
  modifier le contenu présenté aux clients, ni les créneaux, ni les
  bateaux déjà existants (nom, capacité) ; l'ajout d'un nouveau bateau est
  couvert par `SPEC-ADMIN-05`.
- **REQ-033** — la flotte (Ti Kap 12 places, Le Grand Bleu 24 places) est
  une donnée de référence fixe pour les deux bateaux existants, non
  éditable depuis l'espace de gestion ; contrainte de conception plutôt
  que fonctionnalité (cf. `specs/booking.md` `SPEC-BOOKING-03`, et le futur
  MCD/MLD) — nuancée en v3 par la possibilité d'ajouter un bateau
  (`SPEC-ADMIN-05`).
