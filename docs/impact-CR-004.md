# Analyse d'impact - CR-004

**Demande du client :** paiement d'un acompte à la réservation, 30 % pour une
sortie et 50 % pour une privatisation, le solde étant réglé soit en ligne
avant le départ, soit par carte sur place le jour de la sortie ; et
plafonnement de la retenue d'annulation au montant effectivement encaissé.
**Reçue le :** 2026-08-19, entretien oral, formalisé en
[`compte-rendu-entretien-06.md`](./compte-rendu-entretien-06.md) (CR-06).
**Rédigée par :** l'équipe, avec revue critique de l'IA.

---

> **Interdiction de modifier le code avant que cette analyse soit complète.**
>
> La modification descend la chaîne dans cet ordre : cahier des charges → specs →
> UML → modèle de données → cas de test → tests → code.

**Ce CR-004 est le premier qui arrive sur du code existant.** Les trois
précédents portaient sur des documents ; celui-ci trouve 76 tests au vert,
une base migrée et une soixantaine de classes. Il ne se contente pas d'ajouter
un dispositif : **il renverse une exigence `Must` qui interdisait explicitement
ce qu'il demande.**

Il arrive par ailleurs au plus mauvais moment de la mission, la veille de la
revue croisée et à deux jours du gel du dépôt. Le §9 en tire les conséquences
sans les maquiller.

---

## 1. Ce que le client demande, reformulé

Le gérant veut cesser d'exiger le prix entier au moment de réserver. Il pense
que la totalité à payer d'avance freine la réservation, et il préfère
encaisser une part ferme puis le reste au plus près du départ, quitte à le
prendre lui-même au quai.

En contrepartie il accepte de **perdre de l'argent sur les annulations
tardives** : là où il retenait 50 % du prix, il ne gardera plus que l'acompte
de 30 %, et il renonce explicitement à réclamer la différence.

Trois choses ne bougent pas, et il le dit : les bons cadeaux, les avoirs, et
les taux, qu'il ne veut pas pouvoir régler lui-même.

---

## 2. Questions posées au client

Dix-huit, toutes répondues, tracées `CR-06/Q01` à `CR-06/Q18`. **Une seule
réponse ne vient pas de lui** : `Q05`, la borne de la fenêtre de paiement en
ligne, est une déduction de l'équipe et doit être sourcée `déduit` tant qu'il
ne l'a pas validée.

Huit questions restent ouvertes et sont listées au §8 de `CR-06`. Trois
d'entre elles **empêchent d'écrire une spécification complète** :

1. le taux de retenue applicable en deçà de 24 heures, tranche que le barème
   de `R-05` n'a jamais couverte ;
2. le sort de la part d'acompte qui excède la commission dans les deux
   tranches hautes, où le client récupérerait 5 € puis 30 € sur une sortie à
   100 € ;
3. l'interdiction ou non du paiement en ligne pour une réservation prise à
   moins de 24 heures.

---

## 3. Impact - cahier des charges

| Exigence | Nature | Ce qui change |
|---|---|---|
| `REQ-017` | **contredite** | « Le paiement de la totalité du montant est exigé en ligne […] **aucun acompte** » devient l'inverse exact. À réécrire entièrement, pas à amender |
| `REQ-019` | **contredite en partie** | le barème dégressif subsiste mais devient un **plafond** : la retenue ne peut plus excéder le montant encaissé |
| `R-05` | **contredite en partie** | même chose, côté règles de gestion |
| `REQ-023` | **ambiguë** | « remboursé intégralement » avait un sens unique tant que tout était payé d'avance. À préciser : la totalité du **versé** |
| `REQ-056` | **ambiguë** | même correction, pour le renoncement après alerte météo |
| `REQ-047` | **à requalifier** | la différence d'un bon cadeau se payait « selon les mêmes règles que `REQ-017` ». Elle devient une **exception explicite** à l'acompte : due en totalité |
| `REQ-020` | **complétée** | le report emporte l'acompte, et une annulation après report applique le taux initial |
| `REQ-018` | **fragilisée** | la facturation est déléguée au prestataire, qui ne verra jamais un solde encaissé sur place. `CR-06/Q11` demande une facture unique acquittée à la fin |

**Exigences nouvelles**, à partir de `REQ-108`, le dernier identifiant utilisé
étant `REQ-107` :

| ID | Exigence | Source |
|---|---|---|
| `REQ-108` | Acompte de 30 % du montant total exigé en ligne à la réservation d'une sortie | `CR-06`, point 1 |
| `REQ-109` | Acompte de 50 % du forfait exigé à la réservation d'une privatisation | `CR-06`, point 2 |
| `REQ-110` | L'acompte confirme la réservation : la place est décomptée et le client compte dans le seuil de 6 inscrits | `CR-06/Q03` |
| `REQ-111` | Le solde se paie en ligne entre 24 heures avant le départ et l'heure de fermeture du créneau | `déduit`, `CR-06/Q05` |
| `REQ-112` | Le solde peut être réglé par carte sur place ; l'encaissement ne passe pas par l'outil, le gérant pointe le règlement | `CR-06/Q04` |
| `REQ-113` | Le pointage du solde est réversible et laisse une trace | `CR-06/Q14` |
| `REQ-114` | Le planning d'embarquement distingue les réservations soldées de celles qui ne le sont pas | `CR-06/Q13` |
| `REQ-115` | La retenue en cas d'annulation par le client est plafonnée au montant encaissé ; aucun solde n'est réclamé | `CR-06/Q01` |
| `REQ-116` | Une réservation portant un bon cadeau ou un avoir n'a pas d'acompte : le code la solde, ou la différence est due en totalité | `CR-06/Q06` |
| `REQ-117` | L'acompte et le solde sont deux transactions distinctes chez le prestataire | `CR-06/Q09` |
| `REQ-118` | Un client absent au départ est traité comme un client qui annule | `CR-06/Q15` |

Les taux de `REQ-108` et `REQ-109` sont **figés** (`CR-06/Q08`) : ils ne
rejoignent pas la table `parametre`.

---

## 4. Impact - spécifications

| Spécification | Ampleur | Ce qu'il faut reprendre |
|---|---|---|
| `SPEC-BOOKING-07` | **refonte** | c'est la spécification du paiement intégral. Ses huit critères parlent tous d'un encaissement unique. Elle devient la spécification de l'acompte, et le solde en sort |
| `SPEC-BOOKING-03` | modifiée | le mot « confirmée » change de sens : une réservation l'est dès l'acompte. Le décompte des places et le seuil de 6 s'en trouvent redéfinis sans changer de comportement |
| `SPEC-BOOKING-05` | modifiée | l'acompte de privatisation est de 50 %, et le forfait n'est plus encaissé en une fois |
| `SPEC-BOOKING-09` et `10` | modifiées | exception « tout ou rien » à écrire noir sur blanc, sans quoi l'articulation code/acompte reste au jugé |
| `SPEC-CANCEL-04` | modifiée | « remboursé intégralement » devient « la totalité du versé » |
| `SPEC-ADMIN-03` | modifiée | le planning porte l'état du solde |
| `SPEC-ADMIN-06` | modifiée | la retenue est plafonnée, et le barème perd sa portée dans deux tranches sur trois |
| **`SPEC-BOOKING-12`** | **nouvelle** | règlement du solde en ligne : fenêtre, montant, effet sur l'état de la réservation |
| **`SPEC-ADMIN-07`** | **nouvelle** | pointage du solde encaissé sur place, réversible et tracé |

Neuf spécifications touchées sur vingt-neuf, dont **deux nouvelles et une
refonte**.

---

## 5. Impact - conception

**Modèle de données.** `reservation.montant` ne suffit plus : il dit ce qui
est dû, pas ce qui a été versé. Deux options ont été pesées.

| Option | Ce qu'elle apporte | Ce qu'elle coûte |
|---|---|---|
| Colonnes supplémentaires sur `reservation` : `montant_acompte`, `montant_verse`, `statut_paiement` | une seule table, une migration courte | ne trace ni les deux transactions de `REQ-117`, ni les pointages réversibles de `REQ-113`. Il faudrait de toute façon une seconde table pour l'historique |
| **Table `PAIEMENT`** (`#reservation_id`, `type`, `montant`, `canal`, `date`, `pointe_par`) | porte les deux transactions, le canal en ligne ou sur place, et l'historique des pointages, y compris les annulations de pointage. Le versé se déduit par somme | une table de plus, et un calcul là où une colonne suffisait |

**La table `PAIEMENT` est retenue** : c'est la seule qui satisfasse `REQ-113`
et `REQ-117` sans redondance, et elle rend l'historique lisible, ce que le
gérant demandera le jour où un client contestera un pointage.

`sortie.statut` et `reservation.statut` ne bougent pas. En revanche
`StatutDeReservation::CONFIRMEE` change de **fait générateur** : l'acompte, et
non le paiement total.

**UML.** Le diagramme de séquence du paiement est à refaire entièrement. Un
diagramme d'états de la réservation, jusqu'ici inutile, le devient : trois
états de paiement et deux chemins vers le troisième.

**ADR.** `ADR-001` §5 a retenu Stripe sans instruire le paiement en deux
temps. `CR-06/Q09` tranche pour deux transactions distinctes, ce qui évite
l'autorisation différée mais impose que l'application tienne elle-même le lien
entre elles. **Un `ADR-006` est nécessaire**, ne serait-ce que pour écrire que
l'option de l'empreinte de carte a été écartée par le client.

---

## 6. Impact - tests

**25 cas de test sur 82 portent une assertion de montant.** Sur ces 25,
**12 deviennent faux** et 13 restent vrais.

| Cas qui cassent | Pourquoi |
|---|---|
| `CASE-BOOKING-09`, `-11`, `-28`, `-32` | le montant encaissé à la réservation devient 30 % du total |
| `CASE-BOOKING-29` | le forfait de privatisation devient 50 % à la réservation |
| `CASE-BOOKING-05`, `-13` | le remboursement porte sur l'acompte, non sur le prix |
| `CASE-CANCEL-05`, `-07`, `-10`, `-11` | même chose, côté annulation par le gérant |
| `CASE-ADMIN-15` | le montant proposé au renoncement devient le versé |

Les treize autres survivent, et c'est instructif : ce sont ceux qui vérifient
une **absence** de débit ou un **comptage** de remboursements, et ceux qui
portent sur les codes, que `CR-06/Q06` met hors acompte. Une assertion écrite
sur un comportement plutôt que sur un chiffre résiste au changement de besoin.

**Cas nouveaux à écrire**, au moins six : versement de l'acompte, refus de
l'acompte, règlement du solde en ligne dans la fenêtre, règlement hors
fenêtre, pointage sur place, annulation de pointage. Deux de plus si le client
répond aux questions 3 et 4 du §8 de `CR-06`, sur les tranches du barème.

---

## 7. Impact - code

**Vingt-sept classes touchent au montant ou au paiement.** Toutes ne changent
pas, mais le noyau est concentré :

| Composant | Ampleur |
|---|---|
| `Application\ConfirmerLePaiement` | **refonte** : il encaisse aujourd'hui la totalité |
| `Application\CreerReservation` | calcule et exige l'acompte |
| `Application\PrivatiserUnBateau` | acompte de 50 % |
| `Application\AnnulerCreneau`, `Tache\ControlerSeuilDeMaintien` | remboursent le versé et non le montant |
| `Application\EnregistrerUneIssueDannulation` | applique le plafond |
| `Application\ExporterLePlanning`, `ConsulterUneReservation` | exposent l'état du solde |
| `Domaine\Entite\Reservation` | perd le monopole du montant au profit de `Paiement` |
| **`Domaine\Politique\Acompte`** | **nouvelle** : taux par formule, arrondi au centime |
| **`Domaine\Politique\RetenueDannulation`** | **nouvelle** : barème plafonné par le versé |
| **`Application\SolderUneReservation`** | **nouvelle** : règlement en ligne dans la fenêtre |
| **`Application\PointerLeSolde`** | **nouvelle** : pointage réversible et tracé |
| **`Domaine\Entite\Paiement`** + mapping + migration | **nouvelle** |

Soit **quatre classes nouvelles, une refonte, huit adaptations**, une entité
et une migration.

---

## 8. Effets de bord identifiés

- **Le seuil de 6 inscrits change silencieusement de base de calcul.**
  `CR-06/Q03` fait qu'un client ayant versé 30 % compte dans les six. Le
  comportement du contrôle des 24 heures est identique, mais le gérant peut
  désormais annuler une sortie dont il n'a encaissé que 30 % de six clients,
  soit 18 % du chiffre attendu. Il ne l'a pas dit, et il ne l'a probablement
  pas vu.
- **La facture unique contredit `REQ-018`.** Le prestataire ne connaîtra que
  la transaction en ligne. Une facture acquittée après un encaissement au quai
  ne peut pas venir de lui, donc l'outil doit l'émettre, ce qu'aucune
  spécification ne prévoit.
- **Le message de rappel devient trompeur.** `CR-06/Q16` interdit d'y
  mentionner le solde. Or il part à H-24, exactement à l'ouverture de la
  fenêtre de paiement : le client reçoit un message au moment précis où il
  pourrait solder, et on ne le lui dit pas.
- **Le barème perd sa raison d'être dans deux tranches sur trois.** Au-delà de
  48 heures, la commission est inférieure à l'acompte, et le plafond joue en
  faveur du client. Le gérant n'a raisonné que sur la tranche où il joue en sa
  faveur.
- **Le mot « boutique » ouvre un lieu que la mission ne connaît pas.** Si ses
  horaires diffèrent de ceux de l'embarquement, un client peut se trouver dans
  l'impossibilité matérielle de solder.
- **`CASE-ADMIN-06` surestimait déjà ce qu'il prouvait**, l'export ne
  produisant pas de PDF. Y ajouter une colonne « soldé » aggrave l'écart entre
  ce que le cas affirme et ce que le code fait.

---

## 9. Ce que nous ne ferons pas dans le temps restant

Assumé, et à annoncer au client lors de la présentation de J10.

**Le code ne sera pas modifié avant le gel du dépôt.** La décision est
délibérée, et voici pourquoi :

1. **Trois questions bloquantes sont sans réponse** (§2). Écrire le
   plafonnement sans connaître le taux applicable en deçà de 24 heures
   reviendrait à inventer une règle métier, exactement ce que la mission
   interdit.
2. **Douze tests au vert deviendraient faux.** Les casser la veille de la
   revue croisée, sans le temps de les réécrire correctement, échangerait
   12 % de la note contre rien.
3. **La chaîne documentaire vaut plus que l'implémentation.** Le barème note
   les spécifications et la traçabilité à 22 %, l'analyse d'impact à 8 %, le
   code à 12 %. Descendre la chaîne jusqu'aux cas de test sert donc mieux le
   rendu que du code écrit dans l'urgence sur des règles incomplètes.

Ne seront pas faits non plus, quel que soit le temps disponible :

- aucune relance automatique du solde impayé, le client s'y étant opposé ;
- aucun encaissement sur place par l'outil, ni lien ni terminal ;
- aucun réglage des taux depuis l'espace de gestion ;
- aucune émission de facture par l'application, tant que la contradiction avec
  `REQ-018` n'est pas tranchée ;
- aucune gestion du paiement partagé au sein d'un groupe.

---

## 10. Ordre d'exécution retenu

| # | Étape | Qui |
|---|---|---|
| 1 | Formaliser l'échange en `compte-rendu-entretien-06.md` | équipe, **fait** |
| 2 | Écrire la présente analyse d'impact | équipe, **fait** |
| 3 | Poser au client les huit questions du §8 de `CR-06`, dont les trois bloquantes du §2 | équipe, **prioritaire** |
| 4 | Mettre à jour `docs/cahier-des-charges.md` (v6) : `REQ-017`, `REQ-019`, `REQ-020`, `REQ-023`, `REQ-047`, `REQ-056` reprises, `REQ-108` à `REQ-118` ajoutées, `R-05` requalifiée, §11 et §13 complétés | équipe |
| 5 | Reprendre `specs/booking.md` (`SPEC-BOOKING-03`, `05`, `07`, `09`, `10`, création de `SPEC-BOOKING-12`), `specs/admin.md` (`SPEC-ADMIN-03`, `06`, création de `SPEC-ADMIN-07`), `specs/cancel.md` (`SPEC-CANCEL-04`) | équipe |
| 6 | Ajouter la table `PAIEMENT` au MCD et au MLD, et le diagramme d'états de la réservation à l'UML | équipe |
| 7 | Écrire `ADR-006` sur le paiement en deux transactions, en consignant le rejet de l'empreinte de carte | équipe |
| 8 | Réécrire les 12 cas de test devenus faux et en ajouter au moins six | équipe |
| 9 | Régénérer `docs/traceability.md` et déclarer dans `traceability-trous.md` que 12 cas sont désormais en écart avec le code | équipe |
| 10 | Consigner au journal J8 l'arrivée du changement et la décision de ne pas toucher au code | équipe |

**Les étapes 4 à 9 ne seront pas toutes tenues avant J10.** Celles qui ne le
seront pas doivent être déclarées comme telles plutôt que laissées à
découvrir : c'est l'objet de l'étape 9.
