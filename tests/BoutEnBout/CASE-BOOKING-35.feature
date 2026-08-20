# language: fr
#
# Écrit à J9, non exécuté : cf. le fichier de cas et docs/strategie-de-test.md §9.
# Le scénario est repris tel quel de tests/cases/CASE-BOOKING-35.md.
#
# L'étiquette @socle-absent dit que ce scénario n'a aucune implémentation
# d'étapes, pour deux raisons cumulées : il n'existe aucune couche HTTP à
# piloter, et behat/behat n'est installable sur aucune de ses versions avec
# Symfony 8. tools/traceability.sh écarte les fichiers qui la portent, sinon un
# nom de fichier suffirait à éteindre une rupture que rien ne couvre.

@bout-en-bout @socle-absent
Fonctionnalité: CASE-BOOKING-35 - le parcours complet s'affiche en anglais quand l'anglais est choisi

  Couvre SPEC-BOOKING-11 AC-1 et AC-2. La langue des messages envoyés après la
  réservation est vérifiée, elle, par CASE-NFR-01 en PHPUnit : ce qui reste ici
  est la traduction des écrans, y compris les messages de validation du
  formulaire, là où les oublis se logent.

  Contexte:
    Étant donné une sortie dauphins du 20 juillet à 10h, avec des places disponibles
    Et un client qui choisit l'anglais dès la page d'accueil

  Scénario: CASE-BOOKING-35 le parcours complet s'affiche en anglais
    Étant donné un client qui choisit la langue anglaise
    Quand il consulte les places disponibles, remplit le formulaire et atteint le paiement
    Alors chaque écran du parcours s'affiche en anglais
    Et aucun libellé, aucun message d'erreur, aucun bouton ne reste en français
    Et le montant reste exprimé en euros
