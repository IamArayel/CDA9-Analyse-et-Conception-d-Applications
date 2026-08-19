<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * Ce qui peut entrer dans la grille tarifaire (SPEC-ADMIN-02 AC-3).
 *
 * Le refus du 0 € est une décision d'équipe : le client n'a jamais prévu de
 * sortie gratuite. Elle est écrite ici pour être discutable, plutôt que cachée
 * dans une validation de formulaire.
 */
final class ValiditeDunTarif
{
    public function estValide(int $prixEnCentimes): bool
    {
        return $prixEnCentimes > 0;
    }
}
