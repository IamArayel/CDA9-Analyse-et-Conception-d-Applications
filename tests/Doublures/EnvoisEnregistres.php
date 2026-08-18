<?php

declare(strict_types=1);

namespace App\Tests\Doublures;

use App\Domaine\Notificateur;
use DateTimeImmutable;

/**
 * Le canal d'envoi des tests : il n'expédie rien, il enregistre ce qui aurait
 * été envoyé, avec son type, son canal, son destinataire et sa date.
 *
 * L'envoi réel d'un SMS ou d'un e-mail n'est pas testé, cf.
 * docs/strategie-de-test.md §4. Ce que nous vérifions, c'est qu'un envoi est
 * demandé, sur les deux canaux, au bon destinataire et au bon instant, et
 * qu'il laisse une trace, ce que SPEC-CANCEL-04 AC-6 exige de toute façon en
 * production.
 */
final class EnvoisEnregistres implements Notificateur
{
    /**
     * Les types et canaux sont ici en attendant qu'ils vivent dans le domaine :
     * ils reprennent les valeurs de la table `notification` du MLD.
     */
    public const TYPE_RAPPEL = 'RAPPEL';
    public const TYPE_ALERTE_METEO = 'ALERTE_METEO';
    public const TYPE_CONFIRMATION_ANNULATION = 'CONFIRMATION_ANNULATION';

    public const CANAL_SMS = 'SMS';
    public const CANAL_EMAIL = 'EMAIL';

    /** Les deux canaux exigés systématiquement par REQ-057. */
    public const LES_DEUX_CANAUX = [self::CANAL_EMAIL, self::CANAL_SMS];

    /**
     * @var list<array{reservation: string, type: string, canal: string,
     *                 destinataire: string, envoyeLe: DateTimeImmutable}>
     */
    private array $envois = [];

    public function envoyer(
        string $referenceDeReservation,
        string $type,
        string $canal,
        string $destinataire,
        DateTimeImmutable $envoyeLe,
    ): void {
        $this->envois[] = [
            'reservation' => $referenceDeReservation,
            'type' => $type,
            'canal' => $canal,
            'destinataire' => $destinataire,
            'envoyeLe' => $envoyeLe,
        ];
    }

    /**
     * Les envois enregistrés, filtrés sur ce qui est précisé.
     *
     * @return list<array{reservation: string, type: string, canal: string,
     *                    destinataire: string, envoyeLe: DateTimeImmutable}>
     */
    public function envois(
        ?string $type = null,
        ?string $canal = null,
        ?string $destinataire = null,
        ?string $reservation = null,
    ): array {
        $retenus = [];

        foreach ($this->envois as $envoi) {
            if ($type !== null && $envoi['type'] !== $type) {
                continue;
            }
            if ($canal !== null && $envoi['canal'] !== $canal) {
                continue;
            }
            if ($destinataire !== null && $envoi['destinataire'] !== $destinataire) {
                continue;
            }
            if ($reservation !== null && $envoi['reservation'] !== $reservation) {
                continue;
            }

            $retenus[] = $envoi;
        }

        return $retenus;
    }

    public function nombreDenvois(?string $type = null): int
    {
        return count($this->envois($type));
    }

    public function aucunEnvoi(): bool
    {
        return $this->envois === [];
    }

    /**
     * Les canaux par lesquels un destinataire a reçu un type de message,
     * triés pour être comparables à LES_DEUX_CANAUX.
     *
     * @return list<string>
     */
    public function canauxUtilises(string $type, string $destinataire): array
    {
        $canaux = array_column($this->envois($type, destinataire: $destinataire), 'canal');
        $canaux = array_values(array_unique($canaux));
        sort($canaux);

        return $canaux;
    }

    /** @return list<string> */
    public function destinataires(?string $type = null): array
    {
        $destinataires = array_column($this->envois($type), 'destinataire');
        $destinataires = array_values(array_unique($destinataires));
        sort($destinataires);

        return $destinataires;
    }

    /** L'instant auquel un message est parti, ou null s'il n'est jamais parti. */
    public function dateDenvoi(string $type, string $canal, string $destinataire): ?DateTimeImmutable
    {
        $envois = $this->envois($type, $canal, $destinataire);

        return $envois === [] ? null : $envois[0]['envoyeLe'];
    }
}
