<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Ce qu'un client récupère lorsqu'il annule (SPEC-ADMIN-06, REQ-115).
 *
 * **Deux formules coexistent, et ce n'est pas une inadvertance.** Le client les
 * a confirmées séparément, après que l'écart chiffré lui a été montré
 * (`CR-07/Q11`, ambiguïté 1 du §6 de `CR-07`) :
 *
 * - **au-delà de 48 heures**, la commission s'applique à ce que le client a
 *   **versé** : il récupère tout au-delà de 7 jours, et 75 % entre 7 jours et
 *   48 heures ;
 * - **à 48 heures et moins**, elle s'applique au **prix total** puis se
 *   plafonne au versé : un client qui n'a versé que son acompte le perd, un
 *   client qui a soldé récupère la moitié du prix.
 *
 * Sur une sortie à 100 € avec 30 € d'acompte : 30 €, puis 22,50 €, puis 0 €.
 *
 * **Ne pas « harmoniser » ces deux formules.** Elles paraissent bancales et
 * elles sont voulues ; les unifier changerait de 15 € le sort de chaque
 * annulation tardive.
 *
 * Le cas du client absent au départ ne passe pas par ici : il ne récupère rien
 * quoi qu'il ait versé, et cela relève de `EnregistrerUneAbsence`.
 */
final class RetenueDannulation
{
    public const REMBOURSEMENT_INTEGRAL_AU_DELA_DE = '-7 days';
    public const BASCULE_DE_FORMULE_A = '-48 hours';

    public const PART_RENDUE_ENTRE_SEPT_JOURS_ET_QUARANTE_HUIT_HEURES = 75;
    public const COMMISSION_EN_DECA_DE_QUARANTE_HUIT_HEURES = 50;

    /**
     * @param int $verse        ce que le client a effectivement payé, en centimes
     * @param int $montantTotal le prix de la réservation, en centimes
     *
     * @return int ce qui lui est rendu, en centimes
     */
    public function rembourse(
        int $verse,
        int $montantTotal,
        DateTimeImmutable $depart,
        DateTimeImmutable $maintenant,
    ): int {
        if ($maintenant < $depart->modify(self::REMBOURSEMENT_INTEGRAL_AU_DELA_DE)) {
            return $verse;
        }

        if ($maintenant < $depart->modify(self::BASCULE_DE_FORMULE_A)) {
            return (int) round(
                $verse * self::PART_RENDUE_ENTRE_SEPT_JOURS_ET_QUARANTE_HUIT_HEURES / 100
            );
        }

        $commission = (int) round(
            $montantTotal * self::COMMISSION_EN_DECA_DE_QUARANTE_HUIT_HEURES / 100
        );

        return max(0, $verse - $commission);
    }
}
