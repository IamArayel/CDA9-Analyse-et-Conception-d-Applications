# Spécifications — ADMIN (espace de gestion du gérant)

**Domaine :** `ADMIN`
**Source :** `docs/cahier-des-charges.md` (v2), cas d'usage Must have
« modifier les tarifs et suivre le planning sans ressaisie manuelle »

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

---

## Hors périmètre applicatif

Ces exigences existent dans le cahier des charges mais ne donnent lieu à
aucune fonctionnalité dans cette version — mentionnées ici pour que la
chaîne de traçabilité (`tools/traceability.sh`) ne les signale pas comme
non couvertes, sans qu'elles ne deviennent des specs applicatives :

- **REQ-030** (Won't) — l'espace de gestion ne permet ni de modifier le
  contenu présenté aux clients, ni la composition de la flotte, ni les
  créneaux.
- **REQ-033** — la flotte (Ti Kap 12 places, Le Grand Bleu 24 places) est
  une donnée de référence fixe, non éditable depuis l'espace de gestion ;
  contrainte de conception plutôt que fonctionnalité (cf.
  `specs/booking.md` `SPEC-BOOKING-03`, et le futur MCD/MLD).
