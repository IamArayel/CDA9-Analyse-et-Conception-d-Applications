<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * L'état d'un bon cadeau ou d'un code d'avoir.
 *
 * Il n'y a pas d'état intermédiaire : un code se consomme en une fois, quel que
 * soit le reliquat, et le surplus est perdu (SPEC-BOOKING-09 AC-4).
 */
enum StatutDeCode: string
{
    case DISPONIBLE = 'DISPONIBLE';
    case UTILISE = 'UTILISE';
}
