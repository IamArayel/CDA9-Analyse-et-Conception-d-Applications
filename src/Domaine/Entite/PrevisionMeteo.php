<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use DateTimeImmutable;

/**
 * La prévision météo d'une journée, **saisie par le gérant**.
 *
 * Elle est portée par le message de rappel (SPEC-CANCEL-05 AC-2), avec les
 * affaires à prévoir. L'application n'interroge aucun service météo : c'est une
 * règle, pas une limite technique, et c'est ce qui rend l'alerte du gérant
 * entièrement manuelle.
 *
 * Cette table ne figurait pas au MLD de J5 : elle est apparue en écrivant le
 * code de SPEC-CANCEL-05, dont le cas de test exige que le message transporte
 * la prévision. `docs/mcd-mld.md` est à compléter.
 */
class PrevisionMeteo
{
    private ?int $id = null;

    public function __construct(
        private DateTimeImmutable $date,
        private string $texte,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function texte(): string
    {
        return $this->texte;
    }

    public function reviser(string $texte): void
    {
        $this->texte = $texte;
    }
}
