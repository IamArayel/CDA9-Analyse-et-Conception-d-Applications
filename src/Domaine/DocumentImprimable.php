<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * L'export imprimable du planning (SPEC-ADMIN-03).
 *
 * Le planning liste **ce qui embarque**, pas ce qui est en cours d'achat : une
 * réservation non payée n'y figure pas. Et une journée sans réservation produit
 * un document, pas une erreur : hors saison, c'est un résultat métier normal, et
 * le gérant doit pouvoir l'imprimer sans se demander si l'outil a échoué.
 */
final class DocumentImprimable
{
    /**
     * @param list<string>                    $creneaux heures de départ, ordre chronologique
     * @param list<array<string, mixed>>      $lignes
     */
    public function __construct(
        private readonly bool $estUnPdf,
        private readonly array $creneaux,
        private readonly array $lignes,
        private readonly bool $mentionneLabsenceDeReservation,
    ) {
    }

    public function estUnPdf(): bool
    {
        return $this->estUnPdf;
    }

    /** @return list<string> */
    public function creneaux(): array
    {
        return $this->creneaux;
    }

    /** @return list<array<string, mixed>> */
    public function lignes(): array
    {
        return $this->lignes;
    }

    public function mentionneLabsenceDeReservation(): bool
    {
        return $this->mentionneLabsenceDeReservation;
    }
}
