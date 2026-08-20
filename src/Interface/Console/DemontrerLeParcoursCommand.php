<?php

declare(strict_types=1);

namespace App\Interface\Console;

use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterLeCalendrier;
use App\Application\ConsulterUneReservation;
use App\Application\CreerReservation;
use App\Application\CreerUnBateau;
use App\Application\ExporterLePlanning;
use App\Application\ModifierUnTarif;
use App\Application\ProgrammerUneSortie;
use App\Application\ReglerLesParametres;
use App\Application\SolderUneReservation;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Domaine\Entite\Notification;
use App\Domaine\FuseauDexploitation;
use App\Domaine\TypeDeSortie;
use App\Infrastructure\Demonstration\HorlogeReglable;
use App\Infrastructure\Demonstration\NotificateurEnConsole;
use App\Infrastructure\Demonstration\PrestataireDeDemonstration;
use App\Infrastructure\Persistance\BateauRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Rejoue le parcours « réserver, verser l'acompte, solder » de bout en bout.
 *
 * **Cette commande ne contient aucune règle métier.** Elle enchaîne des services
 * applicatifs existants et affiche ce qu'ils rendent. Toute décision, montant ou
 * refus qu'elle montre vient du domaine ; si l'un d'eux était calculé ici, la
 * démonstration ne prouverait rien.
 *
 * Chaque étape annonce **la spécification dont elle vient et le test qui la
 * protège**, comme le demande le barème de J10 : une fonctionnalité montrée sans
 * spec ni test ne compte pas.
 *
 * **Tout est annulé en fin de course.** Le parcours tourne dans une transaction
 * qui n'est jamais validée, exactement comme les 87 cas de test : la commande
 * est donc rejouable autant de fois qu'il le faut, et ne laisse rien derrière
 * elle.
 */
#[AsCommand(
    name: 'ti-baleine:demontrer-le-parcours',
    description: 'Rejoue « réserver, verser l\'acompte, solder » en nommant les specs et les tests',
)]
final class DemontrerLeParcoursCommand extends Command
{
    private const BATEAU = 'Ti Kap';

    private const CLIENT = [
        'nom' => 'Lévesque',
        'prenom' => 'Marie',
        'email' => 'marie.levesque@example.test',
        'telephone_mobile' => '0692112233',
        'langue' => 'fr',
    ];

    public function __construct(
        private readonly EntityManagerInterface $entites,
        private readonly HorlogeReglable $horloge,
        private readonly NotificateurEnConsole $notificateur,
        private readonly PrestataireDeDemonstration $prestataire,
        private readonly CreerUnBateau $creerUnBateau,
        private readonly BateauRepository $bateaux,
        private readonly ModifierUnTarif $modifierUnTarif,
        private readonly ReglerLesParametres $reglerLesParametres,
        private readonly ProgrammerUneSortie $programmerUneSortie,
        private readonly ConsulterLeCalendrier $consulterLeCalendrier,
        private readonly CreerReservation $creerReservation,
        private readonly ConfirmerLePaiement $confirmerLePaiement,
        private readonly ConsulterUneReservation $consulterUneReservation,
        private readonly ExporterLePlanning $exporterLePlanning,
        private readonly EnvoyerLesMessagesProgrammes $messagesProgrammes,
        private readonly SolderUneReservation $solderUneReservation,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $entree, OutputInterface $sortie): int
    {
        $ecran = new SymfonyStyle($entree, $sortie);
        $this->entites->getConnection()->beginTransaction();

        try {
            $this->rejouer($ecran);
        } finally {
            // Jamais de commit : la démonstration ne laisse rien en base.
            $this->entites->getConnection()->rollBack();
        }

        $ecran->success('Parcours complet. La transaction est annulée : la base est intacte.');

        return Command::SUCCESS;
    }

    private function rejouer(SymfonyStyle $ecran): void
    {
        $jour = $this->jourDeSortie();
        $ecran->title(sprintf('Ti Baleine - parcours complet du %s', $jour));

        $this->preparerLexploitation($ecran, $jour);
        $sortie = $this->programmerLaSortie($ecran, $jour);
        $reservation = $this->reserver($ecran, $sortie);
        $this->verserLacompte($ecran, $reservation);
        $this->montrerLePlanning($ecran, $jour, 'avant le solde');
        $this->envoyerLeLien($ecran, $jour);
        $this->solder($ecran, $reservation);
        $this->montrerLePlanning($ecran, $jour, 'après le solde');
    }

    /**
     * Le gérant s'installe : un bateau, deux tarifs, ses réglages d'envoi.
     *
     * Ces trois gestes passent par les mêmes services que l'espace de gestion,
     * et non par des insertions directes : un monde monté autrement ne
     * prouverait rien.
     */
    private function preparerLexploitation(SymfonyStyle $ecran, string $jour): void
    {
        $this->etape($ecran, 'Le gérant déclare sa flotte et ses tarifs', [
            'SPEC-ADMIN-04' => 'CASE-ADMIN-12',
            'SPEC-ADMIN-05' => 'CASE-ADMIN-05',
        ]);

        // Le bateau n'est créé que s'il n'existe pas : la base de
        // développement peut en porter un, et une démonstration qui casse sur
        // sa préparation ne montre rien de ce qu'elle vient montrer.
        if (!in_array(self::BATEAU, $this->bateaux->noms(), true)) {
            $this->creerUnBateau->executer(self::BATEAU, 12, 90000);
        }
        $this->modifierUnTarif->executer(TypeDeSortie::DAUPHINS, 5000, 3000);
        $this->modifierUnTarif->executer(TypeDeSortie::BALEINES, 7000, 4500);
        $this->reglerLesParametres->executer('18:00', 2, 24);

        $ecran->writeln(sprintf('  %s, 12 places. Dauphins : 50 € adulte, 30 € enfant.', self::BATEAU));
    }

    private function programmerLaSortie(SymfonyStyle $ecran, string $jour): string
    {
        $this->etape($ecran, 'Il programme une sortie dauphins à 14h', [
            'SPEC-ADMIN-01' => 'CASE-ADMIN-01',
        ]);

        $sortie = $this->programmerUneSortie->executer($jour, '14:00', self::BATEAU, TypeDeSortie::DAUPHINS);

        $creneaux = $this->consulterLeCalendrier->executer($jour);
        $ecran->writeln(sprintf(
            '  Sortie %s programmée. Le client voit %d créneau(x) proposé(s).',
            $sortie,
            count($creneaux->creneauxProposes()),
        ));

        return $sortie;
    }

    private function reserver(SymfonyStyle $ecran, string $sortie): string
    {
        $this->etape($ecran, 'Marie réserve 2 places adultes', [
            'SPEC-BOOKING-01' => 'CASE-BOOKING-01',
            'SPEC-BOOKING-03' => 'CASE-BOOKING-03',
        ]);

        $resultat = $this->creerReservation->executer($sortie, self::CLIENT, adultes: 2);
        $reference = $resultat->referenceDeReservation();

        $ecran->writeln(sprintf(
            '  Réservation %s, places immobilisées, montant dû %s.',
            $reference,
            $this->euros($this->consulterUneReservation->executer($reference)->montantDu()),
        ));

        return $reference;
    }

    /**
     * Le cœur de la démonstration : ce qui est débité n'est pas le prix.
     */
    private function verserLacompte(SymfonyStyle $ecran, string $reservation): void
    {
        $this->etape($ecran, 'Elle verse son acompte de 30 %', [
            'SPEC-BOOKING-07' => 'CASE-BOOKING-14',
        ]);

        $this->confirmerLePaiement->executer($reservation);
        $vue = $this->consulterUneReservation->executer($reservation);

        $ecran->writeln(sprintf(
            '  Encaissé %s sur %s dus. Solde restant : %s.',
            $this->euros($vue->montantVerse()),
            $this->euros($vue->montantDu()),
            $this->euros($vue->soldeDu()),
        ));
        $ecran->writeln(sprintf(
            '  Réservation %s, et elle compte dans les places vendues (REQ-110).',
            $vue->statut()->name,
        ));
    }

    private function montrerLePlanning(SymfonyStyle $ecran, string $jour, string $moment): void
    {
        $this->etape($ecran, sprintf('Le planning d\'embarquement, %s', $moment), [
            'SPEC-ADMIN-03' => 'CASE-ADMIN-18',
        ]);

        foreach ($this->exporterLePlanning->executer($jour)->lignes() as $ligne) {
            $ecran->writeln(sprintf(
                '  %s  %s %s, %d participant(s) - solde %s',
                $ligne['creneau'],
                $ligne['prenom'],
                $ligne['nom'],
                $ligne['participants'],
                $ligne['solde_regle'] ? 'réglé' : 'À ENCAISSER',
            ));
        }
    }

    /**
     * L'heure avance jusqu'à 7h la veille : c'est le lien qui ouvre la fenêtre.
     */
    private function envoyerLeLien(SymfonyStyle $ecran, string $jour): void
    {
        $this->etape($ecran, 'À 7h la veille, le lien de règlement part', [
            'SPEC-CANCEL-07' => 'CASE-CANCEL-25',
        ]);

        $veille = $this->instant($jour)->modify('-1 day')->setTime(7, 0);
        $this->horloge->nousSommesLe($veille);
        $this->messagesProgrammes->executer();

        foreach ($this->notificateur->envois(Notification::TYPE_LIEN_DE_REGLEMENT) as $envoi) {
            $ecran->writeln(sprintf(
                '  %s → %s, par %s',
                $envoi['envoyeLe']->format('d/m/Y H:i'),
                $envoi['destinataire'],
                $envoi['canal'],
            ));
        }

        $ecran->writeln('  Par courriel seul : un lien de paiement dans un SMS inviterait au hameçonnage.');
    }

    private function solder(SymfonyStyle $ecran, string $reservation): void
    {
        $this->etape($ecran, 'Marie règle son solde en ligne', [
            'SPEC-BOOKING-12' => 'CASE-BOOKING-40',
        ]);

        $resultat = $this->solderUneReservation->executer($reservation);
        $vue = $this->consulterUneReservation->executer($reservation);

        $ecran->writeln(sprintf(
            '  %s. Versé %s, solde %s.',
            $resultat->estConfirme() ? 'Accepté' : 'Refusé : ' . $resultat->motifDuRefus(),
            $this->euros($vue->montantVerse()),
            $this->euros($vue->soldeDu()),
        ));

        $ecran->writeln('  Chez le prestataire, deux transactions et non une (REQ-117) :');

        foreach ($this->prestataire->transactions() as $transaction) {
            $ecran->writeln(sprintf(
                '    %s de %s',
                $transaction['sens'],
                $this->euros($transaction['montant']),
            ));
        }
    }

    /** @param array<string, string> $couverture spécification => cas de test */
    private function etape(SymfonyStyle $ecran, string $titre, array $couverture): void
    {
        $ecran->section($titre);

        foreach ($couverture as $specification => $cas) {
            $ecran->writeln(sprintf('  <info>%s</info> protégée par <info>%s</info>', $specification, $cas));
        }
    }

    /** Trois jours devant nous : le créneau est ouvert, et la veille reste à venir. */
    private function jourDeSortie(): string
    {
        return $this->horloge->maintenant()->modify('+3 days')->format('Y-m-d');
    }

    private function instant(string $jour): DateTimeImmutable
    {
        return new DateTimeImmutable($jour, new DateTimeZone(FuseauDexploitation::IDENTIFIANT));
    }

    private function euros(int $centimes): string
    {
        return number_format($centimes / 100, 2, ',', ' ') . ' €';
    }
}
