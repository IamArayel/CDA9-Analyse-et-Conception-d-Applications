<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\FuseauDexploitation;
use App\Domaine\Horloge;
use App\Domaine\Politique\OffreDeCreneaux;
use App\Domaine\TypeDeSortie;
use App\Domaine\VueDunMoisDeSaison;

/**
 * La frise des douze mois de l'écran « Les sorties » (README §2).
 *
 * Calculée depuis `OffreDeCreneaux`, jamais écrite en dur : un mois que la
 * saison des baleines traverse sans le couvrir en entier (juin, qui ouvre le
 * 15) est rendu comme partiel plutôt que rangé arbitrairement d'un côté.
 */
final class ConsulterLaFriseDeSaison
{
    public function __construct(
        private readonly OffreDeCreneaux $offre,
        private readonly Horloge $horloge,
    ) {
    }

    /** @return list<VueDunMoisDeSaison> */
    public function executer(): array
    {
        $annee = $this->horloge->maintenant()->format('Y');
        $mois = [];

        for ($numero = 1; $numero <= 12; ++$numero) {
            $premierJour = FuseauDexploitation::instant(sprintf('%s-%02d-01', $annee, $numero));
            $dernierJour = $premierJour->modify('last day of this month');

            $debutEnSaison = $this->offre->estEnSaisonDesBaleines($premierJour);
            $finEnSaison = $this->offre->estEnSaisonDesBaleines($dernierJour);

            $type = match (true) {
                $debutEnSaison && $finEnSaison => TypeDeSortie::BALEINES,
                !$debutEnSaison && !$finEnSaison => TypeDeSortie::DAUPHINS,
                default => VueDunMoisDeSaison::PARTIEL,
            };

            $mois[] = new VueDunMoisDeSaison($numero, $type);
        }

        return $mois;
    }
}
