# Spécifications — NFR (exigences transverses non fonctionnelles)

**Domaine :** `NFR`
**Source :** `docs/cahier-des-charges.md` (v2), §10 « Exigences non
fonctionnelles »

Ces exigences sont transverses aux trois cas d'usage Must have plutôt que
rattachées à un seul domaine. Elles sont pour la plupart marquées
« déduit » dans le cahier des charges (non validées avec le client) : les
critères ci-dessous décrivent l'hypothèse retenue par l'équipe en
attendant, pas un engagement client.

---

## SPEC-NFR-01 — Volumétrie et pics de charge

**Exigences :** REQ-100

**Description.** Usage attendu très faible en dehors de la saison haute (un
seul gérant, deux bateaux, trois créneaux par jour), avec un pic de
réservations du 15 juin au 31 octobre.

**Critère de vérification.** Pas de dégradation perceptible avec quelques
dizaines de réservations simultanées en période de pointe.

## SPEC-NFR-02 — Langue

**Exigences :** REQ-102

**Description.** Site en français uniquement pour cette version.

**Critère de vérification.** Absence de contenu traduit dans la première
version livrée.

## SPEC-NFR-03 — Hébergement et coût

**Exigences :** REQ-103

**Description.** Solution à faible coût — cf. `docs/adr/ADR-001-stack.md`
(hébergement mutualisé Hostinger, ≈ 2,99 €/mois).

**Critère de vérification.** Coût d'hébergement mensuel documenté (fait,
`ADR-001`) ; budget total du projet toujours en attente de validation
client (cf. CdC §11).

## SPEC-NFR-04 — Données personnelles et durée de conservation

**Exigences :** REQ-105

**Description.** Seules les informations minimales du formulaire de
réservation (`SPEC-BOOKING-01`) sont collectées ; aucune donnée de paiement
sensible n'est stockée (`SPEC-BOOKING-07`) ; conservation minimale
envisagée de 3 mois avant suppression ou anonymisation.

**Critère de vérification.** Suppression ou anonymisation effective des
données passé le délai retenu.

## SPEC-NFR-05 — Déploiement

**Exigences :** REQ-106

**Description.** Fréquence de mise à jour et environnement de recette non
discutés avec le client à ce stade — cf. `architecture.md` §8 et CdC §11.

**Critère de vérification.** À définir avec le client.

## SPEC-NFR-06 — Maintenance après livraison

**Exigences :** REQ-107

**Description.** Responsable et durée de la maintenance après livraison non
discutés avec le client à ce stade — cf. CdC §11.

**Critère de vérification.** À définir avec le client.
