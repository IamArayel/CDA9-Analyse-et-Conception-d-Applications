# Arborescence Twig cible

```
templates/
├── base.html.twig                  en-tête, pied, sélecteur de langue, rail de profondeur
├── _partials/
│   ├── entete.html.twig
│   ├── pied.html.twig
│   ├── selecteur_langue.html.twig
│   ├── rail_profondeur.html.twig
│   ├── kicker.html.twig            {{ kicker(numero, texte) }}
│   ├── carte_creneau.html.twig     une cellule du calendrier public
│   ├── encart_chiffre.html.twig    grand chiffre + intitulé en capitales
│   └── message_flash.html.twig
├── public/
│   ├── accueil.html.twig
│   ├── sorties.html.twig           inclut _partials/frise_saison.html.twig
│   ├── flotte.html.twig
│   ├── charte.html.twig
│   └── tarifs.html.twig
├── reservation/
│   ├── calendrier.html.twig
│   ├── formulaire.html.twig        inclut _recapitulatif.html.twig
│   ├── _recapitulatif.html.twig    colonne collante, acompte, compte à rebours
│   ├── _compteur.html.twig         le champ − n + réutilisé pour adultes et enfants
│   └── confirmation.html.twig
├── bon-cadeau/
│   └── acheter.html.twig
└── gestion/
    ├── base_gestion.html.twig      barre latérale de 232 px
    ├── connexion.html.twig         hors base_gestion, pleine page
    ├── journee.html.twig           inclut _carte_sortie.html.twig, _bandeau_seuil.html.twig
    ├── creneau.html.twig           inclut _tableau_inscrits.html.twig
    ├── alerte.html.twig
    ├── annulation.html.twig        inclut _bareme.html.twig
    └── reglages.html.twig          inclut _grille_tarifaire.html.twig, _flotte.html.twig
```

## Blocs de `base.html.twig`

`{% block titre %}`, `{% block corps %}`, `{% block pied_scripts %}`, et `{% block rail %}` (le rail de profondeur, désactivé sur les pages de gestion).

## Contrôleurs Stimulus

| Contrôleur | Où | Ce qu'il fait |
| --- | --- | --- |
| `depth_rail` | `base.html.twig` | Suit le défilement, déplace le curseur, calcule la profondeur, inverse l'encre sur fond sombre, se désactive sous 1400 px. |
| `compteur` | formulaire | Incrémente et décrémente adultes/enfants, met à jour le récapitulatif sans rechargement. |
| `compte_a_rebours` | récapitulatif | Décompte l'immobilisation depuis `data-expire-a`, redirige à zéro. |
| `code_promo` | formulaire | Soumet le code, affiche le résultat de `VueDeCode`. |

Aucune bibliothèque de composants : le reste est du HTML et du CSS.
