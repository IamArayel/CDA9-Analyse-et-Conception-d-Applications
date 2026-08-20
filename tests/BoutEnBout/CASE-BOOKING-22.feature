# language: fr
#
# Écrit à J9, non exécuté : cf. le fichier de cas et docs/strategie-de-test.md §9.
# Le scénario est repris tel quel de tests/cases/CASE-BOOKING-22.md.
#
# L'étiquette @socle-absent dit que ce scénario n'a aucune implémentation
# d'étapes, pour deux raisons cumulées : il n'existe aucune couche HTTP à
# piloter, et behat/behat n'est installable sur aucune de ses versions avec
# Symfony 8. tools/traceability.sh écarte les fichiers qui la portent, sinon un
# nom de fichier suffirait à éteindre une rupture que rien ne couvre.

@bout-en-bout @socle-absent
Fonctionnalité: CASE-BOOKING-22 - l'interdiction aux moins de 4 ans est affichée, pas contrôlée

  Couvre SPEC-BOOKING-01 AC-5. Ce cas est de bout en bout parce qu'il ne vérifie
  rien d'autre qu'un affichage : l'application ne collecte aucun âge et ne peut
  donc pas refuser la réservation. Aucun test de domaine ne saurait l'exprimer.

  Contexte:
    Étant donné une sortie dauphins du 20 juillet à 10h
    Et un client qui réserve pour 2 adultes et 1 enfant de 3 ans, ce que rien ne permet de savoir

  Scénario: CASE-BOOKING-22 l'avertissement moins de 4 ans est affiché sans contrôle
    Étant donné le formulaire de réservation
    Quand le client consulte les conditions d'accès avant de valider
    Alors un avertissement indique que l'accès est interdit aux enfants de moins de 4 ans
    Quand il valide pour 2 adultes et 1 enfant
    Alors la réservation est acceptée
