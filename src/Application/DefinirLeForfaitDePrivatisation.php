<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Politique\ValiditeDunTarif;
use App\Infrastructure\Persistance\BateauRepository;
use InvalidArgumentException;

/**
 * Fixer le forfait de privatisation d'un bateau (SPEC-ADMIN-05 AC-5).
 *
 * C'est ce qui rend la privatisation disponible sur ce bateau. La contradiction
 * relevée en revue du modèle de données, une privatisation tarifée par bateau
 * alors que le formulaire de création n'en demande rien, se règle ainsi :
 * indisponible tant que le forfait est vide, disponible dès qu'il est saisi.
 */
final class DefinirLeForfaitDePrivatisation
{
    public function __construct(
        private readonly BateauRepository $bateaux,
        private readonly ValiditeDunTarif $validite,
    ) {
    }

    /** @param int $forfait en centimes */
    public function executer(string $bateau, int $forfait): void
    {
        if (!$this->validite->estValide($forfait)) {
            throw new InvalidArgumentException('Un forfait est strictement positif.');
        }

        $navire = $this->bateaux->parNom($bateau);

        if ($navire === null) {
            throw new InvalidArgumentException(sprintf('Aucun bateau nommé « %s ».', $bateau));
        }

        $navire->definirLeForfaitDePrivatisation($forfait);
        $this->bateaux->enregistrer($navire);
    }
}
