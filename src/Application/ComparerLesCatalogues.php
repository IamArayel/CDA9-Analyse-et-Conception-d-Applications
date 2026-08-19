<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\RapportDeTraduction;
use Symfony\Component\Yaml\Yaml;

/**
 * Comparer les catalogues de traduction, clé par clé (SPEC-NFR-02 AC-1).
 *
 * **C'est un garde-fou dans la durée, plus qu'un contrôle ponctuel.** Un contenu
 * ajouté après la livraison sans sa traduction fait échouer le test qui appelle
 * ce service, et n'atteint donc jamais un client en français dans une page
 * anglaise.
 *
 * Il ne juge pas la qualité de la traduction, qui relève d'une relecture
 * humaine, ni les libellés saisis par le gérant, comme un nom de bateau,
 * conservés tels quels dans les deux langues.
 */
final class ComparerLesCatalogues
{
    /** Les trois messages automatiques doivent exister dans chaque langue. */
    private const GABARITS_REQUIS = [
        'message.rappel.objet',
        'message.rappel.corps',
        'message.alerte_meteo.objet',
        'message.alerte_meteo.corps',
        'message.confirmation_annulation.objet',
        'message.confirmation_annulation.corps',
    ];

    public function __construct(private readonly string $dossierDesTraductions)
    {
    }

    /** @param list<string> $langues */
    public function executer(array $langues): RapportDeTraduction
    {
        $catalogues = [];

        foreach ($langues as $langue) {
            $catalogues[$langue] = $this->aplatir($this->charger($langue));
        }

        return new RapportDeTraduction(
            $this->clesManquantes($catalogues),
            $this->valeursVides($catalogues),
            $this->gabaritsManquants($catalogues),
        );
    }

    /** @return array<string, mixed> */
    private function charger(string $langue): array
    {
        $chemin = sprintf('%s/messages.%s.yaml', $this->dossierDesTraductions, $langue);

        return is_file($chemin) ? (array) Yaml::parseFile($chemin) : [];
    }

    /**
     * « message.rappel.objet » plutôt qu'un tableau imbriqué : la comparaison se
     * fait sur des clés complètes, pas sur des niveaux.
     *
     * @param array<string, mixed> $catalogue
     *
     * @return array<string, string>
     */
    private function aplatir(array $catalogue, string $prefixe = ''): array
    {
        $plat = [];

        foreach ($catalogue as $cle => $valeur) {
            $complete = $prefixe === '' ? (string) $cle : $prefixe.'.'.$cle;

            if (is_array($valeur)) {
                $plat = [...$plat, ...$this->aplatir($valeur, $complete)];

                continue;
            }

            $plat[$complete] = (string) $valeur;
        }

        return $plat;
    }

    /**
     * @param array<string, array<string, string>> $catalogues
     *
     * @return list<string>
     */
    private function clesManquantes(array $catalogues): array
    {
        $toutes = [];

        foreach ($catalogues as $catalogue) {
            $toutes = [...$toutes, ...array_keys($catalogue)];
        }

        $toutes = array_unique($toutes);
        $manquantes = [];

        foreach ($catalogues as $langue => $catalogue) {
            foreach ($toutes as $cle) {
                if (!array_key_exists($cle, $catalogue)) {
                    $manquantes[] = sprintf('%s (%s)', $cle, $langue);
                }
            }
        }

        sort($manquantes);

        return $manquantes;
    }

    /**
     * @param array<string, array<string, string>> $catalogues
     *
     * @return list<string>
     */
    private function valeursVides(array $catalogues): array
    {
        $vides = [];

        foreach ($catalogues as $langue => $catalogue) {
            foreach ($catalogue as $cle => $valeur) {
                if (trim($valeur) === '') {
                    $vides[] = sprintf('%s (%s)', $cle, $langue);
                }
            }
        }

        sort($vides);

        return $vides;
    }

    /**
     * @param array<string, array<string, string>> $catalogues
     *
     * @return list<string>
     */
    private function gabaritsManquants(array $catalogues): array
    {
        $manquants = [];

        foreach ($catalogues as $langue => $catalogue) {
            foreach (self::GABARITS_REQUIS as $gabarit) {
                if (!array_key_exists($gabarit, $catalogue)) {
                    $manquants[] = sprintf('%s (%s)', $gabarit, $langue);
                }
            }
        }

        sort($manquants);

        return $manquants;
    }
}
