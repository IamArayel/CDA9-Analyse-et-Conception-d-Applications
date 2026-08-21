<?php

declare(strict_types=1);

namespace App\Interface\Web\Gestion;

use App\Application\ExporterLePlanning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * La mise en page du document produit par `ExporterLePlanning` : le contenu
 * est prêt côté Application depuis longtemps, sa présentation manquait
 * (docs/traceability-trous.md). Aucune bibliothèque PDF n'étant présente au
 * dépôt, la mise en page est une page imprimable (`@media print`), pas un
 * binaire PDF généré côté serveur.
 */
final class ExportController extends AbstractController
{
    public function __construct(private readonly ExporterLePlanning $exporterLePlanning)
    {
    }

    #[Route(
        '/{_locale}/gestion/planning/{jour}',
        name: 'gestion_export_planning',
        requirements: ['_locale' => 'fr|en', 'jour' => '\d{4}-\d{2}-\d{2}'],
    )]
    public function planning(string $jour): Response
    {
        return $this->render('gestion/export_planning.html.twig', [
            'jour' => $jour,
            'document' => $this->exporterLePlanning->executer($jour),
        ]);
    }
}
