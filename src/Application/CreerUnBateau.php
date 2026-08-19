<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Entite\Bateau;
use App\Domaine\Politique\CreationDunBateau;
use App\Infrastructure\Persistance\BateauRepository;
use InvalidArgumentException;

/**
 * Ajouter un bateau à la flotte (SPEC-ADMIN-05).
 *
 * Le bateau créé apparaît côté client **sans intervention technique ni
 * redéploiement**, et il est habilité aux deux types de sortie : hypothèse
 * d'équipe, faute d'information même pour les deux bateaux existants.
 *
 * Le forfait de privatisation n'est pas demandé à la création, ce que le
 * formulaire du client ne prévoyait pas. Tant qu'il est vide, la privatisation
 * n'est simplement pas proposée sur ce bateau.
 */
final class CreerUnBateau
{
    public function __construct(
        private readonly BateauRepository $bateaux,
        private readonly CreationDunBateau $regle,
    ) {
    }

    public function executer(string $nom, int $capacite, ?int $forfaitDePrivatisation = null): void
    {
        if (!$this->regle->estValide($nom, $this->bateaux->noms(), $capacite)) {
            throw new InvalidArgumentException(
                'Un bateau porte un nom encore libre et une capacité entière strictement positive.'
            );
        }

        $this->bateaux->enregistrer(new Bateau($nom, $capacite, $forfaitDePrivatisation));
    }
}
