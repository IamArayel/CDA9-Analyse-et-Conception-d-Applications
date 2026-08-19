<!-- Généré par tools/traceability.sh — ne pas éditer à la main.
     Les sections « Exigences non couvertes » et « Trous connus » sont
     alimentées par docs/traceability-trous.md, lui tenu à la main. -->

# Matrice de traçabilité — équipe `Le Trio`

Reprise au créneau 16h15, avec le journal. C'est le seul endroit où l'état de la
chaîne se lit d'un coup d'œil.

```text
CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit
```

Une ligne par spécification. **Ce document ne se reconstitue pas la veille du
rendu** : `git log -- docs/traceability.md` montre les jours où il a été tenu.

---

## Comment la lire

| Colonne | Ce qu'on y met | Où le trouver |
|---|---|---|
| SPEC | l'identifiant de la spécification | titre de section dans `specs/<domaine>.md` |
| REQ | la ou les exigences qu'elle réalise | `docs/cahier-des-charges.md` |
| Source | l'échange dont l'exigence est issue, ou `déduit` | `docs/compte-rendu-entretien-nn.md` |
| Cas de test | le ou les cas qui la couvrent | `tests/cases/CASE-*.md` |
| Tests | le nom du test automatisé | `tests/` |
| Commits | le ou les sha courts | `git log --grep=<SPEC-ID>` |

Un maillon qui n'existe pas encore se note `—`. Plusieurs valeurs dans une case se
séparent par une virgule.

**Les six ruptures surveillées** par `tools/traceability.sh --check` : une exigence
sans source · une source citée qui n'existe pas dans nos comptes rendus · une
spécification qu'aucun cas de test ne couvre · un cas de test sans test automatisé ·
une exigence que plus aucune spécification ne reprend · un cas de test utilisé dans
`tests/` mais défini nulle part.

---

## Matrice

| SPEC | REQ | Source | Cas de test | Tests | Commits |
|---|---|---|---|---|---|
| `SPEC-ADMIN-01` | `REQ-031`, `REQ-032`, `REQ-034`, `REQ-104` | `CR-02/Q03`, `CR-02/Q10`, `déduit` | `CASE-ADMIN-01`, `CASE-ADMIN-02`, `CASE-ADMIN-03` | `test_CASE_ADMIN_01_compte_unique_du_gerant_accede_a_lespace`, `test_CASE_ADMIN_02_acces_sans_session_ou_identifiants_errones_refuse`, `test_CASE_ADMIN_03_mot_de_passe_non_conforme_refuse` | `91ed181`, `876412a`, `3877256` |
| `SPEC-ADMIN-02` | `REQ-016`, `REQ-028` | `CR-01/Q07`, `CR-02/Q03` | `CASE-ADMIN-04`, `CASE-ADMIN-05`, `CASE-BOOKING-32` | `test_CASE_ADMIN_04_tarif_modifie_epargne_les_reservations_payees`, `test_CASE_ADMIN_05_tarif_negatif_ou_nul_refuse`, `test_CASE_BOOKING_32_montant_affiche_egal_au_montant_encaisse` | `ba29c7a`, `ab844db` |
| `SPEC-ADMIN-03` | `REQ-029`, `REQ-114` | `CR-02/Q03`, `CR-06/Q13` | `CASE-ADMIN-06`, `CASE-ADMIN-07` | `test_CASE_ADMIN_06_export_du_planning_groupe_par_creneau`, `test_CASE_ADMIN_07_periode_sans_reservation_produit_un_document_lisible` | `be32351`, `3d8b113`, `e34934b`, `277679f` |
| `SPEC-ADMIN-04` | `REQ-038`, `REQ-039` | `CR-03/Q01` | `CASE-ADMIN-08`, `CASE-ADMIN-09` | `test_CASE_ADMIN_08_jours_de_fermeture_par_defaut_et_modifiables`, `test_CASE_ADMIN_09_fermeture_dune_date_reservee_nannule_rien` | `036f3f4`, `6195bfd` |
| `SPEC-ADMIN-05` | `REQ-041` | `CR-03/Q06` | `CASE-ADMIN-10`, `CASE-ADMIN-11`, `CASE-ADMIN-12` | `test_CASE_ADMIN_10_bateau_cree_apparait_avec_sa_capacite`, `test_CASE_ADMIN_11_bateau_sans_forfait_non_privatisable`, `test_CASE_ADMIN_12_nom_deja_pris_ou_capacite_invalide_refuses` | `e828447`, `2e21f71`, `1ef9ddd` |
| `SPEC-ADMIN-06` | `REQ-019`, `REQ-050`, `REQ-056`, `REQ-115`, `REQ-118` | `CR-03/Q05`, `CR-06/Q01`, `CR-06/Q07`, `CR-06/Q15` | `CASE-ADMIN-13`, `CASE-ADMIN-14`, `CASE-ADMIN-15`, `CASE-ADMIN-16`, `CASE-ADMIN-17` | `test_CASE_ADMIN_13_enregistrement_dun_avoir_produit_un_code_dun_an`, `test_CASE_ADMIN_14_report_et_remboursement_ne_produisent_aucun_code`, `test_CASE_ADMIN_15_renoncement_apres_alerte_rembourse_integralement`, `test_CASE_ADMIN_16_retenue_plafonnee_a_lacompte`, `test_CASE_ADMIN_17_client_absent_perd_son_acompte` | `0653f25`, `548ad16`, `51dba2a` |
| `SPEC-ADMIN-07` | `REQ-111`, `REQ-112`, `REQ-113`, `REQ-114` | `CR-06/Q04`, `CR-06/Q05`, `CR-06/Q13`, `CR-06/Q14` | `CASE-ADMIN-18`, `CASE-ADMIN-19` | `test_CASE_ADMIN_18_pointage_du_solde_sans_transaction`, `test_CASE_ADMIN_19_pointage_reversible_et_trace` | `be32351` |
| `SPEC-BOOKING-01` | `REQ-001`, `REQ-008`, `REQ-009`, `REQ-015`, `REQ-036` | `CR-01/Q02`, `CR-02/Q02`, `CR-02/Q12`, `CR-02/Q18` | `CASE-BOOKING-20`, `CASE-BOOKING-21`, `CASE-BOOKING-22`, `CASE-BOOKING-23` | `test_CASE_BOOKING_20_reservation_une_seule_personne_acceptee`, `test_CASE_BOOKING_21_reservation_sans_participant_ou_sans_adulte_refusee`, `test_CASE_BOOKING_23_coordonnees_controlees_et_numero_normalise` | `0a99c87`, `f5ca249`, `9a16808`, `27aa920`, `89943cf`, `db1251e` |
| `SPEC-BOOKING-02` | `REQ-010`, `REQ-011`, `REQ-038` | `CR-01/Q05`, `CR-03/Q01` | `CASE-BOOKING-24`, `CASE-BOOKING-25`, `CASE-BOOKING-26` | `test_CASE_BOOKING_24_saison_des_baleines_bornes_incluses`, `test_CASE_BOOKING_25_jour_de_fermeture_aucun_creneau`, `test_CASE_BOOKING_26_sortie_baleines_hors_saison_refusee` | `6f1c01d`, `18255d4`, `7e7798f`, `6ef0ea9` |
| `SPEC-BOOKING-03` | `REQ-002`, `REQ-003`, `REQ-004`, `REQ-007`, `REQ-018`, `REQ-033`, `REQ-059`, `REQ-108`, `REQ-110`, `REQ-116`, `REQ-119` | `CR-01/Q01`, `CR-01/Q02`, `CR-01/Q08`, `CR-01/Q11`, `CR-02/Q10`, `CR-02/Q16`, `CR-06/Q03`, `CR-06/Q06`, `CR-06/Q10`, `CR-06/Q11`, `déduit` | `CASE-BOOKING-01`, `CASE-BOOKING-02`, `CASE-BOOKING-03`, `CASE-BOOKING-04`, `CASE-BOOKING-05`, `CASE-BOOKING-06`, `CASE-BOOKING-07`, `CASE-BOOKING-08`, `CASE-BOOKING-39` | `test_CASE_BOOKING_01_demande_egale_aux_places_restantes_acceptee`, `test_CASE_BOOKING_02_demande_superieure_aux_places_restantes_refusee`, `test_CASE_BOOKING_03_derniere_place_second_client_refuse_avant_paiement`, `test_CASE_BOOKING_04_immobilisation_expiree_libere_les_places`, `test_CASE_BOOKING_05_seuil_non_atteint_annule_et_rembourse`, `test_CASE_BOOKING_06_seuil_exactement_atteint_maintient_la_sortie`, `test_CASE_BOOKING_07_seconde_sortie_baleines_refusee_sur_le_creneau`, `test_CASE_BOOKING_39_six_acomptes_maintiennent_la_sortie` | `40462fa`, `bee544a` |
| `SPEC-BOOKING-04` | `REQ-005` | `CR-01/Q09` | `CASE-BOOKING-27`, `CASE-BOOKING-28` | `test_CASE_BOOKING_27_creneaux_ferment_a_midi_la_veille_ou_le_jour_meme`, `test_CASE_BOOKING_28_validation_avant_midi_paiement_apres_accepte` | `4eee4a4`, `67e6509` |
| `SPEC-BOOKING-05` | `REQ-006`, `REQ-014`, `REQ-109` | `CR-01/Q03`, `CR-01/Q07`, `CR-06/Q12` | `CASE-BOOKING-29`, `CASE-BOOKING-30` | `test_CASE_BOOKING_29_privatisation_bloque_le_bateau_au_forfait`, `test_CASE_BOOKING_30_privatisation_refusee_si_places_deja_vendues` | `e828447`, `f376bb8` |
| `SPEC-BOOKING-06` | `REQ-012`, `REQ-014`, `REQ-015` | `CR-01/Q04`, `CR-01/Q07`, `CR-02/Q02` | `CASE-BOOKING-31`, `CASE-BOOKING-32` | `test_CASE_BOOKING_31_montant_selon_la_grille_du_type_de_sortie`, `test_CASE_BOOKING_32_montant_affiche_egal_au_montant_encaisse` | `d4567f3`, `d297463` |
| `SPEC-BOOKING-07` | `REQ-017`, `REQ-018`, `REQ-108`, `REQ-109`, `REQ-110`, `REQ-116`, `REQ-117` | `CR-01/Q10`, `CR-01/Q11`, `CR-06/Q03`, `CR-06/Q06`, `CR-06/Q09`, `CR-06/Q10`, `CR-06/Q12` | `CASE-BOOKING-09`, `CASE-BOOKING-10`, `CASE-BOOKING-11`, `CASE-BOOKING-12`, `CASE-BOOKING-13`, `CASE-BOOKING-15`, `CASE-BOOKING-38` | `test_CASE_BOOKING_09_paiement_confirme_decompte_les_places`, `test_CASE_BOOKING_10_paiement_refuse_ne_confirme_ni_ne_decompte`, `test_CASE_BOOKING_11_double_soumission_un_seul_debit`, `test_CASE_BOOKING_12_montant_du_nul_confirme_sans_paiement_carte`, `test_CASE_BOOKING_13_paiement_apres_expiration_place_vendue_rembourse`, `test_CASE_BOOKING_15_bon_cadeau_insuffisant_solde_paye_par_carte`, `test_CASE_BOOKING_38_acompte_arrondi_au_centime` | `916d0a6`, `4057885`, `87891be`, `0d28879` |
| `SPEC-BOOKING-08` | `REQ-035`, `REQ-101` | `déduit` | `CASE-BOOKING-37` | — | `45cf006`, `8091450` |
| `SPEC-BOOKING-09` | `REQ-043`, `REQ-044`, `REQ-045`, `REQ-046`, `REQ-047`, `REQ-048`, `REQ-049`, `REQ-116` | `CR-03/Q07`, `CR-04/Q01`, `CR-06/Q06` | `CASE-BOOKING-14`, `CASE-BOOKING-15`, `CASE-BOOKING-16`, `CASE-BOOKING-17`, `CASE-BOOKING-18`, `CASE-BOOKING-19` | `test_CASE_BOOKING_14_achat_bon_cadeau_delivre_un_code_unique_dun_an`, `test_CASE_BOOKING_15_bon_cadeau_insuffisant_solde_paye_par_carte`, `test_CASE_BOOKING_16_surplus_du_bon_cadeau_est_perdu`, `test_CASE_BOOKING_17_bon_cadeau_deja_utilise_refuse`, `test_CASE_BOOKING_18_bon_cadeau_expire_le_lendemain_de_lanniversaire`, `test_CASE_BOOKING_19_non_cumul_bon_cadeau_et_avoir` | `00e60f8`, `33656ed`, `deaf28b`, `392a2ab` |
| `SPEC-BOOKING-10` | `REQ-050`, `REQ-051`, `REQ-116` | `CR-03/Q05`, `CR-04/Q04`, `CR-06/Q06` | `CASE-BOOKING-19`, `CASE-BOOKING-33`, `CASE-BOOKING-34` | `test_CASE_BOOKING_19_non_cumul_bon_cadeau_et_avoir`, `test_CASE_BOOKING_33_avoir_deduit_quel_que_soit_le_type_de_sortie`, `test_CASE_BOOKING_34_avoir_utilise_ou_expire_refuse` | `dfee14e`, `deaf28b`, `721ed6e` |
| `SPEC-BOOKING-11` | `REQ-040`, `REQ-102` | `CR-03/Q02` | `CASE-BOOKING-35`, `CASE-BOOKING-36` | `test_CASE_BOOKING_36_francais_par_defaut_et_bascule_sans_perte` | `5b321ff` |
| `SPEC-BOOKING-12` | `REQ-017`, `REQ-111`, `REQ-112`, `REQ-117` | `CR-01/Q10`, `CR-06/Q04`, `CR-06/Q05`, `CR-06/Q09` | `CASE-BOOKING-40`, `CASE-BOOKING-41`, `CASE-BOOKING-42` | `test_CASE_BOOKING_40_solde_regle_en_ligne_en_une_transaction`, `test_CASE_BOOKING_41_solde_non_reglable_hors_fenetre`, `test_CASE_BOOKING_42_solde_nul_et_creneau_annule` | `916d0a6` |
| `SPEC-CANCEL-01` | `REQ-022` | `CR-02/Q05` | `CASE-CANCEL-14`, `CASE-CANCEL-15` | `test_CASE_CANCEL_14_consultation_affiche_les_inscrits_sans_effet_de_bord`, `test_CASE_CANCEL_15_creneau_vide_annulable_et_alerte_datee` | `a4a9ed9`, `065c7f2`, `369540d`, `bc37c97`, `4ae1077` |
| `SPEC-CANCEL-02` | `REQ-021` | `CR-02/Q04` | `CASE-CANCEL-16`, `CASE-CANCEL-17`, `CASE-CANCEL-18` | `test_CASE_CANCEL_16_annulation_avec_ou_sans_alerte_prealable`, `test_CASE_CANCEL_17_aucune_annulation_sans_decision_du_gerant`, `test_CASE_CANCEL_18_double_annulation_et_creneau_passe_sans_effet` | `8e58d34` |
| `SPEC-CANCEL-03` | `REQ-004` | `CR-01/Q08` | `CASE-CANCEL-19`, `CASE-CANCEL-20` | `test_CASE_CANCEL_19_creneau_annule_disparait_creneau_en_alerte_reste`, `test_CASE_CANCEL_20_client_en_cours_arrete_sans_debit` | `dc850b2`, `41f7eba` |
| `SPEC-CANCEL-04` | `REQ-023`, `REQ-026`, `REQ-058` | `CR-05/Q03`, `CR-05/Q12`, `CR-06/Q07` | `CASE-CANCEL-10`, `CASE-CANCEL-11`, `CASE-CANCEL-12`, `CASE-CANCEL-13` | `test_CASE_CANCEL_10_annulation_previent_par_ecrit_et_rembourse_en_totalite`, `test_CASE_CANCEL_11_aucun_choix_propose_apres_annulation_gerant`, `test_CASE_CANCEL_12_reservation_non_payee_aucun_remboursement`, `test_CASE_CANCEL_13_trace_des_envois_type_canal_et_date` | `2447e1d`, `87cbf39`, `98a7f23` |
| `SPEC-CANCEL-05` | `REQ-025`, `REQ-042`, `REQ-057` | `CR-02/Q08`, `CR-03/Q03`, `CR-05/Q02` | `CASE-CANCEL-21`, `CASE-CANCEL-22`, `CASE-CANCEL-23`, `CASE-CANCEL-24` | `test_CASE_CANCEL_21_rappel_24h_avant_sur_les_deux_canaux`, `test_CASE_CANCEL_22_horaire_de_rappel_modifie_applique_aux_envois_a_venir`, `test_CASE_CANCEL_23_reservation_tardive_declenche_le_rappel_immediatement`, `test_CASE_CANCEL_24_aucun_rappel_si_annule_et_echec_dun_canal_isole` | `916d05e`, `c6f02a7`, `c7abfe7`, `ab148cc` |
| `SPEC-CANCEL-06` | `REQ-052`, `REQ-053`, `REQ-054`, `REQ-055`, `REQ-060` | `CR-05/Q01`, `CR-05/Q06`, `CR-05/Q08`, `CR-05/Q16` | `CASE-CANCEL-01`, `CASE-CANCEL-02`, `CASE-CANCEL-03`, `CASE-CANCEL-04`, `CASE-CANCEL-05`, `CASE-CANCEL-06`, `CASE-CANCEL-07`, `CASE-CANCEL-08`, `CASE-CANCEL-09` | `test_CASE_CANCEL_01_alerte_couvre_les_deux_bateaux_du_creneau`, `test_CASE_CANCEL_02_aucune_alerte_sans_action_du_gerant`, `test_CASE_CANCEL_03_message_alerte_la_veille_a_18h_sur_deux_canaux`, `test_CASE_CANCEL_04_sortie_maintenue_aucun_second_message`, `test_CASE_CANCEL_05_annulation_confirmee_deux_heures_avant_le_depart`, `test_CASE_CANCEL_06_creneau_en_alerte_reste_reservable_jusqua_la_fermeture`, `test_CASE_CANCEL_07_client_inscrit_apres_alerte_recoit_la_confirmation`, `test_CASE_CANCEL_08_alerte_posee_apres_lheure_part_immediatement`, `test_CASE_CANCEL_09_horaires_modifies_appliques_aux_envois_a_venir` | `cdb26fa` |
| `SPEC-NFR-01` | `REQ-100` | `déduit` | `CASE-NFR-05` | — | `347a598` |
| `SPEC-NFR-02` | `REQ-040`, `REQ-102` | `CR-03/Q02` | `CASE-NFR-01`, `CASE-NFR-02` | `test_CASE_NFR_01_messages_dans_la_langue_de_la_reservation`, `test_CASE_NFR_02_aucun_contenu_sans_traduction` | `807f20b`, `8f07933`, `15d9de4`, `0944f07` |
| `SPEC-NFR-03` | `REQ-103` | `déduit` | `CASE-NFR-06` | — | `347a598`, `ffc7ad3` |
| `SPEC-NFR-04` | `REQ-105` | `déduit` | `CASE-NFR-03`, `CASE-NFR-04` | `test_CASE_NFR_03_seules_les_donnees_du_formulaire_sont_stockees`, `test_CASE_NFR_04_purge_a_trois_mois_sauf_bon_cadeau_vivant` | `ffa947b`, `72861fe`, `ffc7ad3` |
| `SPEC-NFR-05` | `REQ-106` | `déduit` | — | — | — |
| `SPEC-NFR-06` | `REQ-107` | `déduit` | — | — | — |

---

## Exigences non couvertes

Une exigence qu'aucune spécification ne reprend n'apparaît nulle part dans le
tableau ci-dessus. C'est la rupture la plus facile à ne pas voir, et elle se
crée toute seule quand le client change d'avis.

| REQ | Priorité | Pourquoi elle n'est pas encore spécifiée |
|---|---|---|
| — | — | Aucune : toutes les exigences du cahier des charges sont reprises par au moins une spécification. |

---

## Trous connus

Ce que nous savons incomplet, et ce que nous comptons en faire. **Un trou déclaré
n'est pas une faute. Un trou qu'on découvre à notre place en est une.**

| Quoi | Depuis | Pourquoi | Ce qu'on en fait |
|---|---|---|---|
| `SPEC-NFR-05` et `SPEC-NFR-06` sont sans cas de test | J3 | statut brouillon, aucun critère technique : leurs `AC` sont des actions de projet, poser une question au client et consigner sa réponse, pas des comportements logiciels | aucun cas ne sera écrit ; la vérification est de reposer les deux questions au prochain entretien |
| Quatre spécifications sans plan de délégation | J7 | `SPEC-NFR-01` et `SPEC-NFR-03` n'ont qu'un cas `manuel assumé` et **ne donnent lieu à aucune production** : une mesure de charge et une vérification documentaire, toutes deux faites par l'équipe. `SPEC-NFR-05` et `SPEC-NFR-06` n'ont aucun cas. Rien n'étant confié à l'agent, il n'y a rien à cadrer | aucun plan ne sera écrit ; la distinction avec `SPEC-BOOKING-08`, qui a bien un plan, tient à ce que celle-ci demande du code et n'a que sa vérification manuelle |
| 3 cas de test sont `manuel assumé` | J6 | rendu multi-support, charge et coût documenté ne se testent pas en continu, motifs au §4 de `docs/strategie-de-test.md` | `CASE-BOOKING-37` avant J10, `CASE-NFR-05` avant la mise en production, `CASE-NFR-06` à la revue croisée de J9 |
| Les 3 cas de bout en bout n'ont pas de scénario Behat | J6 | les 76 cas de niveau domaine et application sont automatisés en PHPUnit à J7 ; `CASE-BOOKING-08`, `CASE-BOOKING-22` et `CASE-BOOKING-35` relèvent du troisième niveau, qui suppose un socle applicatif déployé | à écrire quand le socle tournera, au plus tard à J9 |
| Texte des trois messages automatiques, en français et en anglais | J3 | jamais fourni par le client, ni pour le rappel, ni pour l'alerte, ni pour la confirmation d'annulation (`CR-05/Q15`) | des gabarits **provisoires** sont écrits à J8 dans `translations/`, ne disant que ce que les spécifications établissent ; ils seront remplacés dès que le client fournira sa rédaction. Aucun test ne vérifie leur contenu, seulement leur existence dans les deux langues |
| Mode d'envoi des SMS | J5 | `CR-05/Q21` répond sur le forfait conservé, pas sur la passerelle d'envoi. La lecture retenue est la seule compatible avec un envoi automatique | question 1 du §8 de `CR-05`, prioritaire : c'est le seul point qui puisse encore faire tomber l'automatisation demandée |
| Fusion de `BonCadeau` et `Avoir` | J4 | les deux dispositifs ne diffèrent plus que par leur origine depuis la v4 (question 8 du §11) | deux tables maintenues tant que le client n'a pas répondu, choix réversible documenté dans `mcd-mld.md` §5 |
| Nom exact de la plateforme d'envoi | J6 | `ADR-004` retient une plateforme française multicanal et pressent Brevo, mais trois vérifications ne peuvent pas se faire depuis le dépôt : couverture du plan de numérotation du territoire, expéditeur alphanumérique, contrat de sous-traitance RGPD | à confirmer à l'ouverture du compte ; si l'une des trois manque, l'option C de l'ADR reprend la main |
| Message associé à une annulation faute de 6 inscrits | J5 | cas non abordé par le client (`CR-05/Q14`), alors que c'est la seule annulation automatique de l'outil | question 13 du §11, à reposer ; en attendant, aucun message spécifique n'est spécifié |
| L'export du planning ne produit pas de PDF | J8 | `ExporterLePlanning` produit le contenu du document, groupé par créneau et ordonné ; sa mise en page appartient à la couche Interface, qui n'existe pas encore. **`CASE-ADMIN-06` ne fait pas la différence** : il vérifie `estUnPdf()`, qui rend une valeur constante | à rendre avec la couche de présentation ; d'ici là, le cas de test surestime ce qu'il prouve, et c'est écrit ici plutôt que découvert à J10 |
| La couche `Interface` n'est pas écrite | J8 | les 76 cas de test entrent par la couche Application ; aucun écran n'est donc nécessaire pour les faire passer, et en écrire un sans spécification d'écran serait du code non couvert | à ouvrir quand un parcours devra être montré à l'écran, au plus tard pour la démonstration de J10 |
| Deux ports n'ont pas d'adaptateur réel | J8 | `Notificateur` et `PrestataireDePaiement` sont liés à des adaptateurs qui **échouent bruyamment**, l'intégration de Brevo relevant d'`ADR-004` et celle de Stripe de `SPEC-BOOKING-07`. En test, les doublures les remplacent | assumé : un envoi ou un encaissement silencieusement perdu coûterait bien plus cher qu'une erreur visible. À intégrer avant toute mise en production |
| Les 76 tests et le code restent en v5 | J8 | `CR-06` renverse `REQ-017` le soir de la journée d'implémentation. Le cahier des charges, les dix spécifications, le modèle de données, l'UML et les 91 cas de test sont passés en v6 ; **les 76 tests automatisés et le code décrivent encore le paiement intégral** | écart voulu et ordonné : la chaîne descend du cahier des charges vers le code, jamais l'inverse. Le lot code est conditionné à un point d'arrêt le 21/08 à 09h00, cf. §9 de `impact-CR-004.md` |
| 21 tests sont au rouge sur la branche `feat-modification-acompte` | J9 | les 12 tests repris et les 9 nouveaux décrivent l'acompte, le solde, le plafonnement et le pointage ; **le code produit encore le paiement intégral**. Écrits avant le code, comme les 76 premiers l'ont été à J7 | attendu : chaque rouge nomme la classe ou l'assertion qui manque. `main` reste vert et cohérent en v5 tant que la branche n'est pas fusionnée, et elle ne le sera qu'au vert, au plus tard au point d'arrêt du 21/08 à 09h00 |
