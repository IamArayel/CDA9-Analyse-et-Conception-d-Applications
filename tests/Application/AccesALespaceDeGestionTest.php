<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterLespaceDeGestion;
use App\Application\SeConnecter;
use App\Tests\CasDapplication;

/**
 * SPEC-ADMIN-01 - accès à l'espace de gestion.
 *
 * Un seul compte existe, celui du gérant : aucun écran de création d'un second
 * compte n'est prévu. Et les deux refus possibles produisent le même message,
 * pour que rien ne permette de deviner si une adresse existe.
 */
final class AccesALespaceDeGestionTest extends CasDapplication
{
    private const EMAIL_DU_GERANT = 'gerant@ti-baleine.test';
    private const MOT_DE_PASSE = 'Abc1!def';

    protected function instantInitial(): string
    {
        return '2026-07-18 09:00';
    }

    /**
     * AC-1 : le compte unique du gérant accède aux quatre sections de gestion.
     */
    public function test_CASE_ADMIN_01_compte_unique_du_gerant_accede_a_lespace(): void
    {
        $connexion = ($this->service(SeConnecter::class))
            ->executer(self::EMAIL_DU_GERANT, self::MOT_DE_PASSE);

        self::assertTrue($connexion->estAcceptee());

        $sections = ($this->service(ConsulterLespaceDeGestion::class))
            ->sections($connexion->session());

        self::assertSame(
            ['tarifs', 'planning', 'horaires', 'flotte'],
            $sections,
            'les quatre sections de gestion sont accessibles',
        );
    }

    /**
     * AC-3 et AC-4 : un accès sans session, ou avec de mauvais identifiants,
     * est refusé, et rien ne trahit l'existence d'une adresse.
     */
    public function test_CASE_ADMIN_02_acces_sans_session_ou_identifiants_errones_refuse(): void
    {
        $connexion = $this->service(SeConnecter::class);

        $emailInconnu = $connexion->executer('inconnu@example.test', self::MOT_DE_PASSE);
        $mauvaisMotDePasse = $connexion->executer(self::EMAIL_DU_GERANT, 'Xyz9?ghi');

        self::assertTrue($emailInconnu->estRefusee());
        self::assertTrue($mauvaisMotDePasse->estRefusee());
        self::assertSame(
            $emailInconnu->messageDerreur(),
            $mauvaisMotDePasse->messageDerreur(),
            'rien ne permet de deviner si l\'e-mail existe',
        );

        self::assertSame(
            [],
            ($this->service(ConsulterLespaceDeGestion::class))->sections(null),
            'l\'accès direct par URL ne contourne rien',
        );
    }
}
