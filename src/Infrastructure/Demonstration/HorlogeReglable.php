<?php

declare(strict_types=1);

namespace App\Infrastructure\Demonstration;

use App\Domaine\FuseauDexploitation;
use App\Domaine\Horloge;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Une horloge qu'on avance à la main, pour la démonstration de bout en bout.
 *
 * Le parcours à montrer couvre trois jours : réserver, recevoir le lien la
 * veille à 7h, solder. Attendre trois jours devant l'auditoire n'étant pas une
 * option, l'horloge est réglable.
 *
 * **Cet adaptateur n'est câblé que dans l'environnement `demo`.** En production,
 * `HorlogeSysteme` reste seule, et c'est elle qui rend l'heure réelle du fuseau
 * d'exploitation.
 */
final class HorlogeReglable implements Horloge
{
    private DateTimeImmutable $maintenant;

    public function __construct()
    {
        $this->maintenant = new DateTimeImmutable(
            'now',
            new DateTimeZone(FuseauDexploitation::IDENTIFIANT),
        );
    }

    public function maintenant(): DateTimeImmutable
    {
        return $this->maintenant;
    }

    public function nousSommesLe(DateTimeImmutable $instant): void
    {
        $this->maintenant = $instant;
    }
}
