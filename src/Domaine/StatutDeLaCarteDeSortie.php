<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce qu'une carte de sortie affiche dans le tableau de bord du gérant (G2).
 *
 * Distinct de `EtatDuDepart` (le calendrier public) : le gérant a besoin de
 * savoir qu'une sortie est **déjà partie**, ce qui n'a pas de sens côté client,
 * et n'a pas de notion de fermeture des ventes à afficher ici.
 */
enum StatutDeLaCarteDeSortie
{
    case PARTIE;
    case OUVERTE;
    case COMPLETE;
    case EN_ALERTE;
}
