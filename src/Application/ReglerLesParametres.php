<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Persistance\ParametreRepository;

/**
 * Régler les horaires d'envoi depuis l'espace de gestion (SPEC-CANCEL-05 AC-3,
 * SPEC-CANCEL-06 AC-9).
 *
 * Chaque réglage n'est modifié que s'il est fourni : le gérant change l'heure
 * d'alerte sans avoir à ressaisir les deux autres.
 *
 * Les nouvelles valeurs valent pour les **envois à venir**. Un message déjà
 * parti n'est pas rejoué, et un message programmé n'est pas figé : il est
 * recalculé au passage suivant de la tâche.
 */
final class ReglerLesParametres
{
    public function __construct(private readonly ParametreRepository $parametres)
    {
    }

    public function executer(
        ?string $heureDenvoiDeLalerte = null,
        ?int $delaiDeConfirmationEnHeures = null,
        ?int $delaiDeRappelEnHeures = null,
    ): void {
        $reglages = $this->parametres->reglages();

        $reglages->regler(
            $heureDenvoiDeLalerte,
            $delaiDeConfirmationEnHeures,
            $delaiDeRappelEnHeures,
        );

        $this->parametres->enregistrer($reglages);
    }
}
