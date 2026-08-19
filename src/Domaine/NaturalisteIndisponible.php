<?php

declare(strict_types=1);

namespace App\Domaine;

use DomainException;

/**
 * Une seconde sortie baleines a été demandée sur un créneau qui en porte déjà
 * une (SPEC-BOOKING-03 AC-6) : il n'y a qu'un naturaliste.
 *
 * La règle vit dans un index unique en base, sur une colonne générée
 * (mcd-mld.md §7). Cette exception est la traduction en refus métier de
 * l'échec de cette contrainte, et non un contrôle applicatif qui pourrait être
 * contourné par deux demandes simultanées.
 */
final class NaturalisteIndisponible extends DomainException
{
}
