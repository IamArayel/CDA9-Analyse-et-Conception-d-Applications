<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\AppliquerUnCode;
use App\Domaine\ResultatDapplicationDunCode;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CodeController extends AbstractController
{
    public function __construct(private readonly AppliquerUnCode $appliquerUnCode)
    {
    }

    #[Route(
        '/{_locale}/code/verifier',
        name: 'code_verifier',
        requirements: ['_locale' => 'fr|en'],
        methods: ['POST'],
    )]
    public function verifier(Request $request): Response
    {
        $locale = $request->getLocale();
        $session = $request->getSession();
        $reference = $session->get(ReservationController::CLE_SESSION_REFERENCE);
        $sortie = $session->get(ReservationController::CLE_SESSION_SORTIE);

        if ($reference === null || $sortie === null) {
            return $this->redirectToRoute('calendrier', ['_locale' => $locale]);
        }

        try {
            $resultat = $this->appliquerUnCode->executer((string) $reference, (string) $request->request->get('code', ''));
        } catch (InvalidArgumentException) {
            throw $this->createNotFoundException();
        }

        if ($resultat->estRefuse()) {
            $this->addFlash(
                'erreur',
                $resultat->motifDuRefus() === ResultatDapplicationDunCode::MOTIF_CODES_NON_CUMULABLES
                    ? 'erreur.code_non_cumulable'
                    : 'erreur.code_invalide',
            );
        }

        return $this->redirectToRoute('reservation_formulaire', ['_locale' => $locale, 'sortie' => $sortie]);
    }
}
