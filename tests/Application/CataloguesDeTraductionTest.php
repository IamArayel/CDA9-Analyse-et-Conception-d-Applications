<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ComparerLesCatalogues;
use App\Tests\CasDapplication;

/**
 * SPEC-NFR-02 - complétude des catalogues de traduction.
 *
 * Ce test est un garde-fou dans la durée, plus qu'un contrôle ponctuel : un
 * contenu ajouté après la livraison sans sa traduction le fait échouer. C'est
 * sa raison d'être, et il ne juge pas la qualité de la traduction, qui relève
 * d'une relecture humaine.
 */
final class CataloguesDeTraductionTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 09:00';
    }

    /**
     * AC-1 : aucun contenu ne reste sans traduction dans l'une des deux langues.
     */
    public function test_CASE_NFR_02_aucun_contenu_sans_traduction(): void
    {
        $rapport = ($this->service(ComparerLesCatalogues::class))->executer(['fr', 'en']);

        self::assertSame(
            [],
            $rapport->clesManquantes(),
            'les deux catalogues portent exactement les mêmes clés',
        );
        self::assertSame(
            [],
            $rapport->valeursVides(),
            'une clé traduite par une chaîne vide n\'est pas une traduction',
        );
        self::assertSame(
            [],
            $rapport->gabaritsDeMessageManquants(),
            'les gabarits des trois messages automatiques existent dans les deux langues',
        );
    }
}
