# language: fr
#
# Écrit à J9, non exécuté : cf. le fichier de cas et docs/strategie-de-test.md §9.
# Le scénario est repris tel quel de tests/cases/CASE-BOOKING-08.md.
#
# L'étiquette @socle-absent dit que ce scénario n'a aucune implémentation
# d'étapes, pour deux raisons cumulées : il n'existe aucune couche HTTP à
# piloter, et behat/behat n'est installable sur aucune de ses versions avec
# Symfony 8. tools/traceability.sh écarte les fichiers qui la portent, sinon un
# nom de fichier suffirait à éteindre une rupture que rien ne couvre.

@bout-en-bout @socle-absent
Fonctionnalité: CASE-BOOKING-08 - les places affichées diminuent après le paiement d'un autre client

  Couvre SPEC-BOOKING-03 AC-7. Le décompte est vérifié côté domaine par
  CASE-BOOKING-09 ; ce qui reste ici est l'affichage qui se met à jour sans
  action du client, et cela ne s'observe que dans un navigateur.

  Contexte:
    Étant donné une sortie dauphins du 20 juillet à 10h sur le Ti Kap, avec 5 places restantes
    Et un client A qui consulte la page du créneau
    Et un client B en cours de paiement pour 2 places

  Scénario: CASE-BOOKING-08 les places affichées diminuent après le paiement d'un autre client
    Étant donné un client A affichant un créneau à 5 places restantes
    Quand le paiement du client B pour 2 places est confirmé
    Alors l'affichage du client A indique 3 places restantes
    Et il n'a pas rechargé la page
