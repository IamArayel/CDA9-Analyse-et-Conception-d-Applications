<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

/**
 * La complexité exigée du mot de passe du gérant (SPEC-ADMIN-01 AC-2).
 *
 * Huit caractères au moins, une majuscule, une minuscule, un chiffre, un
 * caractère spécial. Chaque condition manquante suffit à refuser, et la borne
 * de longueur est inclusive : huit caractères exactement passent.
 */
final class ComplexiteDuMotDePasse
{
    public const LONGUEUR_MINIMALE = 8;

    public function estConforme(string $motDePasse): bool
    {
        if (mb_strlen($motDePasse) < self::LONGUEUR_MINIMALE) {
            return false;
        }

        return $this->contientUneMajuscule($motDePasse)
            && $this->contientUneMinuscule($motDePasse)
            && $this->contientUnChiffre($motDePasse)
            && $this->contientUnCaractereSpecial($motDePasse);
    }

    private function contientUneMajuscule(string $motDePasse): bool
    {
        return preg_match('/\p{Lu}/u', $motDePasse) === 1;
    }

    private function contientUneMinuscule(string $motDePasse): bool
    {
        return preg_match('/\p{Ll}/u', $motDePasse) === 1;
    }

    private function contientUnChiffre(string $motDePasse): bool
    {
        return preg_match('/\d/', $motDePasse) === 1;
    }

    private function contientUnCaractereSpecial(string $motDePasse): bool
    {
        return preg_match('/[^\p{L}\d]/u', $motDePasse) === 1;
    }
}
