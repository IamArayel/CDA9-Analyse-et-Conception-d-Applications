<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\PrevisionMeteo;
use App\Domaine\FuseauDexploitation;
use App\Infrastructure\Persistance\PrevisionMeteoRepository;

/**
 * Saisir la prévision météo d'une journée (SPEC-CANCEL-05 AC-2).
 *
 * C'est **le gérant qui la saisit** : l'application n'interroge aucun service
 * météo, ni ici ni ailleurs. Cette absence est une règle du projet, pas une
 * limite technique, et c'est ce qui rend l'alerte entièrement manuelle.
 *
 * Une seconde saisie révise la première.
 */
final class SaisirLaPrevisionMeteo
{
    public function __construct(private readonly PrevisionMeteoRepository $previsions)
    {
    }

    public function executer(string $jour, string $prevision): void
    {
        $date = FuseauDexploitation::instant($jour)->setTime(0, 0);
        $existante = $this->previsions->pourLeJour($date);

        if ($existante !== null) {
            $existante->reviser($prevision);
            $this->previsions->enregistrer($existante);

            return;
        }

        $this->previsions->enregistrer(new PrevisionMeteo($date, $prevision));
    }
}
