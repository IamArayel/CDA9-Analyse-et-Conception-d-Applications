# ADR-006 - Paiement en deux temps, acompte puis solde

**Statut :** accepté
**Date :** J8 (2026-08-19)
**Décidé par :** le client sur le principe (`CR-06/Q09`), l'équipe sur les conséquences
**Complète :** `ADR-001-stack.md` §5, qui a retenu le prestataire sans instruire
le paiement fractionné, et `ADR-003-concurrence-derniere-place.md`, dont une
évolution envisagée tombe ici

---

> **Cet ADR n'arbitre pas seul.** Le principe des deux transactions vient du
> client, qui a répondu sans hésiter à la question. Ce que l'équipe décide, ce
> sont les conséquences : qui tient le lien entre les deux paiements, et ce que
> le rejet des autres options ferme définitivement.

## Contexte

`CR-06` renverse `REQ-017` : une réservation n'exige plus la totalité du
montant mais un **acompte**, 30 % pour une sortie et 50 % pour une
privatisation. Le solde est réglé plus tard, en ligne ou par carte au quai.

Un prestataire de paiement offre trois façons de faire, et elles ne se
valent pas. Deux contraintes du projet pèsent sur le choix :

- **le solde peut ne jamais passer par le prestataire** (`REQ-112`), le gérant
  l'encaissant sur son terminal et se contentant de le pointer ;
- **aucune donnée de carte n'entre dans l'application** (`REQ-018`), ce qui
  exclut que nous conservions nous-mêmes de quoi débiter une seconde fois.

## Options envisagées

### Option A - Deux transactions indépendantes (retenue)

| | |
|---|---|
| Ce qu'elle apporte | rien à conserver entre les deux paiements : chacun se suffit. Le client ressaisit sa carte, ou n'en ressaisit aucune s'il règle au quai. Compatible sans réserve avec un solde encaissé hors de l'outil |
| Ce qu'elle coûte | deux frais de transaction au lieu d'un. Le client ressaisit sa carte, ce qui est une friction au moment précis où il pourrait renoncer. **Le lien entre les deux paiements n'existe que chez nous**, et doit donc être tenu explicitement |
| Ce qu'elle rend difficile plus tard | rien |

### Option B - Autorisation à la réservation, capture différée du total

| | |
|---|---|
| Ce qu'elle apporte | une seule saisie de carte, et une seule transaction du point de vue du client. `ADR-003` notait qu'elle **supprimerait le remboursement** du cas limite 9 de `SPEC-BOOKING-07` : une autorisation s'annule, un débit se rembourse |
| Ce qu'elle coûte | une autorisation expire, en général sous sept jours. Une réservation prise trois semaines avant le départ ne tiendrait pas jusque-là, et le projet vend des sorties réservables bien plus tôt. Surtout, **elle est incompatible avec un solde encaissé au quai** : l'autorisation porte sur le total, et le gérant ne peut pas la réduire depuis son terminal |
| Ce qu'elle rend difficile plus tard | elle impose un montant connu et figé à la réservation, ce qui interdit toute modification du groupe après coup, question 1 du §11 restée ouverte depuis CR-01 |

### Option C - Empreinte de carte conservée, second débit à notre initiative

| | |
|---|---|
| Ce qu'elle apporte | le client ne ressaisit rien, et le solde part tout seul à l'heure dite. C'est la solution la plus confortable pour lui |
| Ce qu'elle coûte | débiter un client sans qu'il agisse suppose un mandat explicite, une information préalable et un droit de rétractation à instruire. Le client n'a rien demandé de tel, et `CR-06/Q17` dit au contraire qu'un client dont le solde n'est pas réglé « se débrouille » : il n'attend donc aucun prélèvement automatique |
| Ce qu'elle rend difficile plus tard | elle crée une relation de prélèvement là où le projet n'en a aucune, avec les obligations qui vont avec |

## Décision

**Option A.** L'acompte et le solde sont deux transactions indépendantes chez
le prestataire, et **c'est l'application qui tient le lien entre elles**, par
la table `PAIEMENT` du `mcd-mld.md` §6.

## Raisons

Le client a tranché le principe (`CR-06/Q09`), mais l'option A serait de toute
façon la seule tenable : elle est la seule compatible avec `REQ-112`, qui
autorise un solde encaissé au quai, hors de la vue du prestataire. Les options
B et C supposent toutes deux que le prestataire connaisse et exécute le second
paiement, ce que le règlement au comptoir contredit frontalement.

L'option B tombe par ailleurs sur un fait simple : une autorisation bancaire
ne survit pas trois semaines, et rien n'empêche de réserver trois semaines à
l'avance.

L'option C est écartée sur un point de fond, pas de technique. Prélever un
client sans qu'il agisse est une relation d'un autre ordre que celle du
projet, et `CR-06/Q17` montre que le client ne l'envisage pas : pour lui, un
solde impayé est l'affaire du passager, pas un automatisme.

## Conséquences acceptées

- **Le lien entre les deux paiements est notre responsabilité.** Le prestataire
  ne le connaît pas. Une table `PAIEMENT` le porte, et le montant versé d'une
  réservation se calcule par somme plutôt que se lit dans une colonne.
- **Deux frais de transaction** par réservation soldée en ligne, au lieu d'un.
  Le client a répondu « budget illimité » pour l'exercice, mais ce coût-là est
  récurrent et proportionnel au volume : il devra le savoir.
- **Le client ressaisit sa carte** pour régler son solde. C'est une friction au
  moment où il pourrait renoncer, et aucune relance n'est prévue pour le
  rattraper (`CR-06/Q17`).
- **Le remboursement du cas limite 9 de `SPEC-BOOKING-07` reste nécessaire.**
  `ADR-003` envisageait de le supprimer si une capture différée était retenue ;
  l'option B étant écartée, un acompte encaissé sur une place entre-temps
  vendue devra bien être remboursé.
- **La facturation se fragmente.** Le prestataire n'émettra un justificatif que
  pour ce qui passe chez lui. Un solde encaissé au quai lui échappe, ce qui met
  `REQ-119`, la facture unique acquittée, en tension avec `REQ-018`. Question
  18 du §11 du cahier des charges.

## Ce qui nous ferait revenir dessus

- Le client renonce au règlement au comptoir et impose le paiement en ligne du
  solde : l'option C redevient discutable, et l'option B avec elle pour les
  réservations proches du départ.
- Le prestataire propose un fractionnement natif rattachant deux transactions à
  une même commande : le lien cesserait d'être notre responsabilité, et la
  table `PAIEMENT` perdrait la moitié de sa raison d'être.
- Le taux d'abandon au moment de solder se révèle élevé : la friction de la
  ressaisie deviendrait un coût mesurable, et non plus une gêne théorique.
