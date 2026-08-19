<?php

declare(strict_types=1);

namespace App\Domaine;

use DomainException;

/**
 * Une sortie baleines a été demandée hors de la saison du 15 juin au
 * 31 octobre, bornes incluses (SPEC-BOOKING-02 AC-5).
 *
 * Le refus vient de l'enregistrement, pas de l'affichage : masquer une option
 * ne suffit pas à la rendre impossible.
 */
final class HorsSaison extends DomainException
{
}
