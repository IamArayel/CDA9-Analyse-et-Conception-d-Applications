<?php

declare(strict_types=1);

namespace App\Interface\Web;

use App\Application\ConsulterUnDepart;
use App\Application\ConsulterUneReservation;
use App\Application\CreerReservation;
use App\Domaine\Horloge;
use App\Domaine\Politique\Coordonnees;
use App\Domaine\ResultatDeReservation;
use App\Domaine\StatutDeReservation;
use App\Domaine\VueDeReservation;
use App\Domaine\VueDuDepart;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    public const CLE_SESSION_REFERENCE = 'reservation_en_cours';
    public const CLE_SESSION_SORTIE = 'reservation_en_cours_sortie';

    public function __construct(
        private readonly ConsulterUnDepart $depart,
        private readonly CreerReservation $creerReservation,
        private readonly ConsulterUneReservation $consulterReservation,
        private readonly Horloge $horloge,
    ) {
    }

    #[Route(
        '/{_locale}/reserver/{sortie}',
        name: 'reservation_formulaire',
        requirements: ['_locale' => 'fr|en', 'sortie' => '\d+'],
        methods: ['GET', 'POST'],
    )]
    public function formulaire(Request $request, string $sortie): Response
    {
        $vueDuDepart = $this->departOu404($sortie);

        if ($request->isMethod('POST')) {
            return $this->traiterLaSoumission($request, $sortie, $vueDuDepart);
        }

        $enCours = $this->reservationEnCours($request, $sortie);

        if ($enCours !== null && $enCours->statut() === StatutDeReservation::CONFIRMEE) {
            return $this->redirectToRoute('reservation_confirmation', [
                '_locale' => $request->getLocale(),
                'reference' => $request->getSession()->get(self::CLE_SESSION_REFERENCE),
            ]);
        }

        return $this->render('reservation/formulaire.html.twig', [
            'depart' => $vueDuDepart,
            'reservation' => $enCours,
            'reference' => $request->getSession()->get(self::CLE_SESSION_REFERENCE),
            'erreur' => null,
            'champEnCause' => null,
            'valeurs' => [],
        ]);
    }

    private function traiterLaSoumission(Request $request, string $sortie, VueDuDepart $vueDuDepart): Response
    {
        $locale = $request->getLocale();
        $adultes = (int) $request->request->get('adultes', 0);
        $enfants = (int) $request->request->get('enfants', 0);
        $client = [
            'nom' => (string) $request->request->get('nom', ''),
            'prenom' => (string) $request->request->get('prenom', ''),
            'email' => (string) $request->request->get('email', ''),
            'telephone_mobile' => (string) $request->request->get('telephone_mobile', ''),
            'langue' => $locale,
        ];

        $resultat = $this->creerReservation->executer($sortie, $client, $adultes, $enfants);

        if ($resultat->estAcceptee()) {
            $request->getSession()->set(self::CLE_SESSION_REFERENCE, $resultat->referenceDeReservation());
            $request->getSession()->set(self::CLE_SESSION_SORTIE, $sortie);

            return $this->redirectToRoute('reservation_formulaire', ['_locale' => $locale, 'sortie' => $sortie]);
        }

        if ($this->refusTenantAuCreneau($resultat)) {
            $this->addFlash('erreur', $this->cleDeMessage($resultat));

            return $this->redirectToRoute('calendrier', ['_locale' => $locale]);
        }

        return $this->render('reservation/formulaire.html.twig', [
            'depart' => $vueDuDepart,
            'reservation' => null,
            'erreur' => $this->cleDeMessage($resultat),
            'champEnCause' => $resultat->champEnCause(),
            'valeurs' => $client + ['adultes' => $adultes, 'enfants' => $enfants],
        ]);
    }

    #[Route(
        '/{_locale}/reservation/{reference}',
        name: 'reservation_confirmation',
        requirements: ['_locale' => 'fr|en', 'reference' => '\d+'],
    )]
    public function confirmation(string $reference): Response
    {
        return $this->render('reservation/confirmation.html.twig', [
            'reservation' => $this->reservationOu404($reference),
            'reference' => $reference,
        ]);
    }

    #[Route(
        '/{_locale}/reservation/{reference}.ics',
        name: 'reservation_agenda',
        requirements: ['_locale' => 'fr|en', 'reference' => '\d+'],
    )]
    public function agenda(string $reference): Response
    {
        $reservation = $this->reservationOu404($reference);
        $depart = $reservation->depart();
        $fin = $depart->modify('+2 hours');

        $lignes = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Ti Baleine//Reservation//FR',
            'BEGIN:VEVENT',
            sprintf('UID:reservation-%s@ti-baleine.re', $reference),
            sprintf('DTSTART:%s', $depart->format('Ymd\THis\Z')),
            sprintf('DTEND:%s', $fin->format('Ymd\THis\Z')),
            'SUMMARY:Sortie en mer Ti Baleine',
            'LOCATION:Port de Saint-Gilles, ponton n° 3, 97434 Saint-Gilles-les-Bains',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return new Response(
            implode("\r\n", $lignes),
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => sprintf('attachment; filename="reservation-%s.ics"', $reference),
            ],
        );
    }

    private function departOu404(string $sortie): VueDuDepart
    {
        try {
            return $this->depart->executer($sortie);
        } catch (InvalidArgumentException) {
            throw $this->createNotFoundException();
        }
    }

    private function reservationOu404(string $reference): VueDeReservation
    {
        try {
            return $this->consulterReservation->executer($reference);
        } catch (InvalidArgumentException) {
            throw $this->createNotFoundException();
        }
    }

    /** La réservation en session, si elle tient encore ses places pour cette sortie. */
    private function reservationEnCours(Request $request, string $sortie): ?VueDeReservation
    {
        $session = $request->getSession();
        $reference = $session->get(self::CLE_SESSION_REFERENCE);

        if ($reference === null || $session->get(self::CLE_SESSION_SORTIE) !== $sortie) {
            return null;
        }

        try {
            $reservation = $this->consulterReservation->executer((string) $reference);
        } catch (InvalidArgumentException) {
            $session->remove(self::CLE_SESSION_REFERENCE);
            $session->remove(self::CLE_SESSION_SORTIE);

            return null;
        }

        $encoreValide = $reservation->statut() === StatutDeReservation::CONFIRMEE
            || ($reservation->statut() === StatutDeReservation::EN_ATTENTE_DE_PAIEMENT
                && $this->horloge->maintenant() < $reservation->expireLe());

        if (!$encoreValide) {
            $session->remove(self::CLE_SESSION_REFERENCE);
            $session->remove(self::CLE_SESSION_SORTIE);

            return null;
        }

        return $reservation;
    }

    private function refusTenantAuCreneau(ResultatDeReservation $resultat): bool
    {
        return in_array($resultat->motifDuRefus(), [
            ResultatDeReservation::MOTIF_PLACES_INSUFFISANTES,
            ResultatDeReservation::MOTIF_CRENEAU_FERME,
            ResultatDeReservation::MOTIF_CRENEAU_ANNULE,
            ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE,
        ], true);
    }

    private function cleDeMessage(ResultatDeReservation $resultat): string
    {
        return match ($resultat->motifDuRefus()) {
            ResultatDeReservation::MOTIF_PLACES_INSUFFISANTES => 'erreur.place_prise',
            ResultatDeReservation::MOTIF_CRENEAU_FERME => 'erreur.creneau_ferme',
            ResultatDeReservation::MOTIF_CRENEAU_ANNULE => 'erreur.creneau_annule',
            ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE => 'erreur.bateau_deja_engage',
            ResultatDeReservation::MOTIF_COMPOSITION_INVALIDE => 'erreur.adulte_requis',
            ResultatDeReservation::MOTIF_COORDONNEES_INVALIDES => $resultat->champEnCause() === Coordonnees::CHAMP_EMAIL
                ? 'erreur.email_invalide'
                : 'erreur.mobile_invalide',
            default => 'erreur.creneau_ferme',
        };
    }
}
