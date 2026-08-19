<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\ResultatDeConnexion;
use App\Infrastructure\Persistance\GerantRepository;

/**
 * Ouvrir une session sur l'espace de gestion (SPEC-ADMIN-01).
 *
 * **Les deux refus possibles sont indiscernables.** Une adresse inconnue et un
 * mot de passe erroné rendent exactement le même message : les distinguer
 * permettrait de savoir quelles adresses existent (AC-4). C'est pourquoi le
 * résultat de connexion ne transporte aucun motif, contrairement aux autres
 * résultats du domaine.
 *
 * Le condensat est vérifié même quand l'adresse est inconnue, pour que les deux
 * refus prennent le même temps.
 */
final class SeConnecter
{
    private const CONDENSAT_FACTICE = '$2y$12$0000000000000000000000000000000000000000000000000000';

    public function __construct(
        private readonly GerantRepository $gerants,
        private readonly SessionDeGestion $sessions,
    ) {
    }

    public function executer(string $email, string $motDePasse): ResultatDeConnexion
    {
        $gerant = $this->gerants->parEmail($email);
        $condensat = $gerant?->motDePasse() ?? self::CONDENSAT_FACTICE;

        if (!password_verify($motDePasse, $condensat) || $gerant === null) {
            return ResultatDeConnexion::refusee();
        }

        return ResultatDeConnexion::acceptee($this->sessions->ouvrir());
    }
}
