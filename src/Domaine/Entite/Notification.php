<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use DateTimeImmutable;

/**
 * La trace d'un message envoyé à un client (SPEC-CANCEL-04 AC-6).
 *
 * Une ligne **par canal**, pas par message : le canal fait partie de la trace,
 * et l'échec de l'un n'emporte pas l'autre. Depuis que le gérant ne téléphone
 * plus, c'est ce qui lui permet de répondre à un client affirmant n'avoir rien
 * reçu.
 */
class Notification
{
    public const TYPE_RAPPEL = 'RAPPEL';
    public const TYPE_ALERTE_METEO = 'ALERTE_METEO';
    public const TYPE_CONFIRMATION_ANNULATION = 'CONFIRMATION_ANNULATION';

    public const CANAL_SMS = 'SMS';
    public const CANAL_EMAIL = 'EMAIL';

    public const STATUT_ENVOYE = 'ENVOYE';
    public const STATUT_ECHEC = 'ECHEC';

    private ?int $id = null;

    public function __construct(
        private Reservation $reservation,
        private string $type,
        private string $canal,
        private DateTimeImmutable $dateDenvoi,
        private string $statut,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function reservation(): Reservation
    {
        return $this->reservation;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function canal(): string
    {
        return $this->canal;
    }

    public function dateDenvoi(): DateTimeImmutable
    {
        return $this->dateDenvoi;
    }

    public function statut(): string
    {
        return $this->statut;
    }

    public function aEchoue(): bool
    {
        return $this->statut === self::STATUT_ECHEC;
    }
}
