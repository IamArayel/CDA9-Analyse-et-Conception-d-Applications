<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\AcheterUnBonCadeau;
use App\Application\ConsulterLesSuggestionsDeBonCadeau;
use App\Application\ConsulterUnCode;
use App\Domaine\Politique\Coordonnees;
use App\Domaine\VueDeCode;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BonCadeauController extends AbstractController
{
    public function __construct(
        private readonly AcheterUnBonCadeau $acheterUnBonCadeau,
        private readonly ConsulterLesSuggestionsDeBonCadeau $suggestions,
        private readonly ConsulterUnCode $consulterUnCode,
        private readonly Coordonnees $coordonnees,
    ) {
    }

    #[Route(
        '/{_locale}/bon-cadeau',
        name: 'bon_cadeau',
        requirements: ['_locale' => 'fr|en'],
        methods: ['GET', 'POST'],
    )]
    public function acheter(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            return $this->traiterLachat($request);
        }

        return $this->render('bon-cadeau/acheter.html.twig', [
            'suggestions' => $this->suggestions->executer(),
            'code' => $request->query->get('code'),
            'vueDuCode' => $this->vueDuCodeAchete($request),
            'erreur' => null,
            'champEnCause' => null,
            'valeurs' => [],
        ]);
    }

    private function vueDuCodeAchete(Request $request): ?VueDeCode
    {
        $code = $request->query->get('code');

        if (!is_string($code)) {
            return null;
        }

        try {
            return $this->consulterUnCode->executer($code);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    private function traiterLachat(Request $request): Response
    {
        $locale = $request->getLocale();
        $montantSaisi = (string) $request->request->get('montant', '');
        $courrielAcheteur = (string) $request->request->get('courriel_acheteur', '');
        $valeurs = [
            'montant' => $montantSaisi,
            'beneficiaire' => (string) $request->request->get('beneficiaire', ''),
            'courriel_acheteur' => $courrielAcheteur,
            'message' => (string) $request->request->get('message', ''),
        ];

        $montant = $this->montantEnCentimes($montantSaisi);

        if ($montant === null || $montant <= 0) {
            return $this->reafficher('erreur.montant_invalide', null, $valeurs);
        }

        if (!$this->coordonnees->emailEstValide($courrielAcheteur)) {
            return $this->reafficher('erreur.email_invalide', 'courriel_acheteur', $valeurs);
        }

        $code = $this->acheterUnBonCadeau->executer($montant, ['email' => $courrielAcheteur]);

        return $this->redirectToRoute('bon_cadeau', ['_locale' => $locale, 'code' => $code]);
    }

    private function montantEnCentimes(string $saisie): ?int
    {
        $normalise = str_replace(',', '.', trim($saisie));

        if (!is_numeric($normalise)) {
            return null;
        }

        return (int) round(((float) $normalise) * 100);
    }

    private function reafficher(string $erreur, ?string $champEnCause, array $valeurs): Response
    {
        return $this->render('bon-cadeau/acheter.html.twig', [
            'suggestions' => $this->suggestions->executer(),
            'code' => null,
            'vueDuCode' => null,
            'erreur' => $erreur,
            'champEnCause' => $champEnCause,
            'valeurs' => $valeurs,
        ]);
    }
}
