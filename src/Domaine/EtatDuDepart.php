<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce qu'un départ donne à voir au client sur le calendrier public.
 *
 * Priorité de classement, du plus bloquant au plus informatif : `FERME` et
 * `COMPLET` interdisent la vente, `ALERTE` prévient sur un départ qui reste
 * réservable (cf. `StatutDeSortie::EN_ALERTE`). Une sortie annulée n'a pas
 * d'état : elle disparaît de l'offre.
 *
 * Concept d'affichage, jamais persisté : une énumération simple, sans valeur
 * portée en base.
 */
enum EtatDuDepart
{
    case DISPONIBLE;
    case ALERTE;
    case COMPLET;
    case FERME;
}
