<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * Contrôle et normalisation des coordonnées d'un client (SPEC-BOOKING-01).
 *
 * C'est ce contrôle qui rend tenable la position du client, pour qui un message
 * non délivré relève de celui qui a mal saisi ses coordonnées (CR-05/Q04) :
 * sans lui, l'application accepterait des adresses et des numéros dont elle
 * sait qu'ils ne recevront rien.
 *
 * Le retrait des points, tirets et espaces vient de la réponse du client au
 * §6.3 de CR-05.
 */
final class Coordonnees
{
    public const CHAMP_EMAIL = 'email';
    public const CHAMP_MOBILE = 'telephone_mobile';

    /** Un mobile commence par 06 ou 07, ce qui écarte les numéros fixes. */
    private const MOBILE = '/^0[67]\d{8}$/';

    public function emailEstValide(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function mobileEstValide(string $mobile): bool
    {
        return preg_match(self::MOBILE, $this->normaliserLeMobile($mobile)) === 1;
    }

    /** « 06 12-34.56 78 » devient « 0612345678 ». */
    public function normaliserLeMobile(string $mobile): string
    {
        return str_replace([' ', '.', '-'], '', $mobile);
    }

    /** Le champ fautif, ou null si les coordonnées passent. */
    public function champEnCause(string $email, string $mobile): ?string
    {
        if (!$this->emailEstValide($email)) {
            return self::CHAMP_EMAIL;
        }

        if (!$this->mobileEstValide($mobile)) {
            return self::CHAMP_MOBILE;
        }

        return null;
    }
}
