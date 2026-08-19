<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Les trois issues d'une annulation **demandée par le client**, cf.
 * SPEC-ADMIN-06.
 *
 * Ce triptyque n'existe que là. Une annulation décidée par le gérant, météo
 * comprise, donne lieu à un remboursement intégral sans alternative : c'est la
 * correction du 2026-08-14, après une transcription erronée de CR-02/Q04.
 *
 * Seul `AVOIR` produit un code, et c'est sa seule origine.
 */
enum IssueDannulation: string
{
    case REPORT = 'REPORT';
    case AVOIR = 'AVOIR';
    case REMBOURSEMENT = 'REMBOURSEMENT';
}
