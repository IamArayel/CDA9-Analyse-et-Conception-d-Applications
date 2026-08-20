<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\IssueDannulation;
use App\Domaine\ResultatDissue;

/**
 * Constater qu'un client ne s'est pas présenté à l'embarquement
 * (SPEC-ADMIN-06 AC-8).
 *
 * **Un absent est traité comme une annulation de dernière minute**, et le client
 * l'a confirmé en CR-07/Q03 : il ne récupère rien. Le service n'a donc pas de
 * barème propre, il pose l'issue à l'instant du départ et laisse
 * `EnregistrerUneIssueDannulation` appliquer la retenue, qui à cette heure-là a
 * déjà mangé l'acompte.
 *
 * Écrire ici un « montant zéro » en dur serait plus court et faux : le jour où
 * le gérant assouplira le barème, l'absence doit suivre sans qu'on y touche.
 */
final class EnregistrerUneAbsence
{
    public function __construct(private readonly EnregistrerUneIssueDannulation $issues)
    {
    }

    public function executer(string $reference): ResultatDissue
    {
        return $this->issues->executer($reference, IssueDannulation::REMBOURSEMENT);
    }
}
