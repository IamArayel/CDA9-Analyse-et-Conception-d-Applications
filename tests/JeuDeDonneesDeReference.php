<?php

declare(strict_types=1);

namespace App\Tests;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Le jeu de données de référence de docs/strategie-de-test.md §7, construit à
 * un seul endroit.
 *
 * Tous les tests partent de lui. Un chiffre inattendu dans un test signale donc
 * une régression, pas un jeu de données différent.
 */
final class JeuDeDonneesDeReference
{
    /**
     * Le fuseau du lieu d'exploitation n'a jamais été explicité par le client
     * (specs/booking.md, « Ce qui n'est pas défini » de SPEC-BOOKING-04). Les
     * cas de test sont tous écrits en heure locale ; cette constante est le
     * seul endroit à changer le jour où le client le précisera.
     */
    public const FUSEAU_DEXPLOITATION = 'UTC';

    /** L'année de l'exercice. Les dates pivots du §7 s'y rapportent. */
    public const ANNEE = 2026;

    public const TI_KAP = 'Ti Kap';
    public const TI_KAP_CAPACITE = 12;
    public const TI_KAP_FORFAIT_PRIVATISATION = 60000;

    public const LE_GRAND_BLEU = 'Le Grand Bleu';
    public const LE_GRAND_BLEU_CAPACITE = 24;
    public const LE_GRAND_BLEU_FORFAIT_PRIVATISATION = 110000;

    public const SORTIE_BALEINES = 'BALEINES';
    public const SORTIE_DAUPHINS = 'DAUPHINS';

    public const BALEINES_PRIX_ADULTE = 6500;
    public const BALEINES_PRIX_ENFANT = 4000;
    public const DAUPHINS_PRIX_ADULTE = 5000;
    public const DAUPHINS_PRIX_ENFANT = 3000;

    /** Les trois créneaux d'une journée d'exploitation. */
    public const CRENEAU_MATIN = '07:00';
    public const CRENEAU_MILIEU_DE_MATINEE = '10:00';
    public const CRENEAU_APRES_MIDI = '14:00';

    /** Date pivot en saison : sortie baleines possible. */
    public const JOUR_EN_SAISON = '2026-07-20';

    /** Date pivot hors saison : dauphins seulement. */
    public const JOUR_HORS_SAISON = '2026-12-01';

    /** Jours de fermeture, cf. SPEC-ADMIN-04. */
    public const JOURS_DE_FERMETURE = ['2026-12-25', '2027-01-01'];

    /** Seuil de maintien d'une sortie ouverte à la vente, cf. REQ-002. */
    public const SEUIL_DE_MAINTIEN = 6;

    /** Durée d'immobilisation des places, en minutes, cf. ADR-003. */
    public const DUREE_DIMMOBILISATION_EN_MINUTES = 15;

    /** Paramètres par défaut de l'espace de gestion, cf. SPEC-CANCEL-06 AC-9. */
    public const HEURE_DENVOI_DE_LALERTE = '18:00';
    public const DELAI_DE_CONFIRMATION_EN_HEURES = 2;

    /**
     * Le compte unique de l'espace de gestion. Le mot de passe respecte la
     * règle de complexité de SPEC-ADMIN-01, et n'est jamais stocké en clair :
     * le monde de test n'en met en base que le condensat.
     */
    public const EMAIL_DU_GERANT = 'gerant@ti-baleine.test';
    public const MOT_DE_PASSE_DU_GERANT = 'Abc1!def';

    /**
     * Les clients de référence. Le §7 ne les nommait pas ; ils sont ajoutés ici
     * pour que deux tests qui parlent du « premier client » parlent du même.
     */
    public const CLIENT_MARIE = [
        'nom' => 'Dupont',
        'prenom' => 'Marie',
        'email' => 'marie.dupont@example.test',
        'telephone_mobile' => '0692000001',
        'langue' => 'fr',
    ];

    public const CLIENT_JOHN = [
        'nom' => 'Smith',
        'prenom' => 'John',
        'email' => 'john.smith@example.test',
        'telephone_mobile' => '0692000002',
        'langue' => 'en',
    ];

    public const CLIENT_KARIM = [
        'nom' => 'Benali',
        'prenom' => 'Karim',
        'email' => 'karim.benali@example.test',
        'telephone_mobile' => '0692000003',
        'langue' => 'fr',
    ];

    /**
     * Le prix d'une sortie dauphins, en centimes, adultes et enfants confondus.
     *
     * Le calcul appartient au domaine (SPEC-BOOKING-06) ; il est répété ici
     * pour que les montants attendus d'un cas se lisent en clair, « le prix de
     * deux adultes », plutôt qu'en nombre nu.
     */
    public static function prixDauphins(int $adultes, int $enfants = 0): int
    {
        return $adultes * self::DAUPHINS_PRIX_ADULTE + $enfants * self::DAUPHINS_PRIX_ENFANT;
    }

    /** Le prix d'une sortie baleines, en centimes. */
    public static function prixBaleines(int $adultes, int $enfants = 0): int
    {
        return $adultes * self::BALEINES_PRIX_ADULTE + $enfants * self::BALEINES_PRIX_ENFANT;
    }

    /**
     * Un instant du fuseau d'exploitation, écrit comme les cas de test
     * l'écrivent : « 2026-07-19 18:00 ».
     */
    public static function instant(string $dateEtHeureLocales): DateTimeImmutable
    {
        return new DateTimeImmutable(
            $dateEtHeureLocales,
            new DateTimeZone(self::FUSEAU_DEXPLOITATION),
        );
    }

    /** Un montant en centimes, à partir d'un montant en euros. */
    public static function euros(int|float $euros): int
    {
        return (int) round($euros * 100);
    }
}
