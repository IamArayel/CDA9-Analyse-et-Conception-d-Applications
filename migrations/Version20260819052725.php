<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Schéma initial, conforme à docs/mcd-mld.md §6 et §7.
 *
 * **Trois éléments de cette migration ne viennent pas du diff Doctrine et ont
 * été écrits à la main.** Le diff ne les invente pas, et sans eux deux règles
 * métier disparaîtraient du schéma sans qu'aucun test de niveau domaine ne s'en
 * aperçoive :
 *
 * 1. la colonne générée `sortie.creneau_baleines` et son index unique, qui
 *    portent la règle du naturaliste unique ;
 * 2. la contrainte CHECK de non-cumul d'un bon cadeau et d'un avoir sur une
 *    même réservation ;
 * 3. les CHECK de positivité sur les capacités, les forfaits, les tarifs et les
 *    bornes de participants.
 *
 * Ils supposent MySQL 8.0.16 ou plus pour que les CHECK soient réellement
 * appliqués, ce qui a été vérifié avant la première migration (ADR-002).
 */
final class Version20260819052725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schéma initial : douze tables, la règle du naturaliste en colonne générée, '
            .'le non-cumul des codes et les bornes de validité en contraintes CHECK.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE avoir (code VARCHAR(32) NOT NULL, montant INT NOT NULL, date_emission DATETIME NOT NULL, date_expiration DATETIME NOT NULL, statut VARCHAR(20) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_avoir_code (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bateau (nom VARCHAR(60) NOT NULL, capacite SMALLINT NOT NULL, forfait_privatisation INT DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_bateau_nom (nom), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bon_cadeau (code VARCHAR(32) NOT NULL, montant INT NOT NULL, date_achat DATETIME NOT NULL, date_expiration DATETIME NOT NULL, statut VARCHAR(20) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_bon_cadeau_code (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE choix_annulation (type VARCHAR(20) NOT NULL, date_enregistrement DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, avoir_id INT DEFAULT NULL, INDEX IDX_A4FDB37C36D46DB (avoir_id), UNIQUE INDEX uniq_choix_annulation_reservation (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE creneau (date_creneau DATE NOT NULL, heure_depart TIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_creneau_date_heure (date_creneau, heure_depart), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gerant (email VARCHAR(180) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_gerant_email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE jour_fermeture (date_fermeture DATE NOT NULL, recurrent_annuel TINYINT NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_jour_fermeture_date (date_fermeture), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (type VARCHAR(30) NOT NULL, canal VARCHAR(10) NOT NULL, date_envoi DATETIME NOT NULL, statut VARCHAR(10) NOT NULL, id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, INDEX IDX_BF5476CAB83297E7 (reservation_id), INDEX idx_notification_reservation_type (reservation_id, type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parametre (heure_ouverture VARCHAR(5) NOT NULL, heure_fermeture VARCHAR(5) NOT NULL, delai_rappel_heures SMALLINT NOT NULL, heure_alerte VARCHAR(5) NOT NULL, delai_confirmation_heures SMALLINT NOT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (nom_client VARCHAR(80) NOT NULL, prenom_client VARCHAR(80) NOT NULL, email VARCHAR(180) NOT NULL, telephone_mobile VARCHAR(20) NOT NULL, nombre_adultes SMALLINT NOT NULL, nombre_enfants SMALLINT NOT NULL, montant INT NOT NULL, langue VARCHAR(2) NOT NULL, statut VARCHAR(30) NOT NULL, date_creation DATETIME NOT NULL, expire_le DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, sortie_id INT NOT NULL, bon_cadeau_id INT DEFAULT NULL, avoir_id INT DEFAULT NULL, INDEX IDX_42C84955CC72D953 (sortie_id), UNIQUE INDEX uniq_reservation_bon_cadeau (bon_cadeau_id), UNIQUE INDEX uniq_reservation_avoir (avoir_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sortie (type_sortie VARCHAR(20) NOT NULL, formule VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, date_alerte DATETIME DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, creneau_id INT NOT NULL, bateau_id INT NOT NULL, INDEX IDX_3C3FD3F27D0729A9 (creneau_id), INDEX IDX_3C3FD3F2A9706509 (bateau_id), UNIQUE INDEX uniq_sortie_creneau_bateau (creneau_id, bateau_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tarif (type_sortie VARCHAR(20) NOT NULL, prix_adulte INT NOT NULL, prix_enfant INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_tarif_type_sortie (type_sortie), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE choix_annulation ADD CONSTRAINT FK_A4FDB37B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE choix_annulation ADD CONSTRAINT FK_A4FDB37C36D46DB FOREIGN KEY (avoir_id) REFERENCES avoir (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955CC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554E7C433F FOREIGN KEY (bon_cadeau_id) REFERENCES bon_cadeau (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C36D46DB FOREIGN KEY (avoir_id) REFERENCES avoir (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F27D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneau (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2A9706509 FOREIGN KEY (bateau_id) REFERENCES bateau (id)');

        $this->ecrireLaRegleDuNaturaliste();
        $this->ecrireLeNonCumulDesCodes();
        $this->ecrireLesBornesDeValidite();
    }

    /**
     * SPEC-BOOKING-03 AC-6, un seul naturaliste par créneau.
     *
     * La colonne vaut l'identifiant du créneau pour une sortie baleines, et NULL
     * sinon. MySQL n'applique pas l'unicité aux NULL : l'index laisse donc
     * passer autant de sorties dauphins qu'on veut sur un créneau, et refuse la
     * seconde sortie baleines. Deux réservations simultanées ne peuvent pas la
     * contourner, ce qu'un contrôle applicatif ne garantirait pas.
     *
     * La colonne est ajoutée après la création de la table pour ne pas dépendre
     * de l'ordre de déclaration des colonnes qu'elle référence.
     */
    private function ecrireLaRegleDuNaturaliste(): void
    {
        $this->addSql(
            'ALTER TABLE sortie ADD creneau_baleines INT '
            ."GENERATED ALWAYS AS (CASE WHEN type_sortie = 'BALEINES' THEN creneau_id END) STORED"
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_sortie_creneau_baleines ON sortie (creneau_baleines)');
    }

    /**
     * SPEC-BOOKING-09 AC-8 et SPEC-BOOKING-10 AC-5, non-cumul des codes.
     *
     * Doublée d'une vérification applicative, qui sert seulement à produire un
     * message clair au client. C'est cette contrainte-ci qui rend le cumul
     * impossible.
     */
    private function ecrireLeNonCumulDesCodes(): void
    {
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT chk_reservation_non_cumul '
            .'CHECK (bon_cadeau_id IS NULL OR avoir_id IS NULL)'
        );
    }

    /**
     * Bornes que le schéma refuse d'enfreindre.
     *
     * Le refus d'un tarif nul est une décision d'équipe : le client n'a jamais
     * prévu de sortie gratuite. Et une réservation compte au moins un adulte,
     * ce qui interdit le mineur non accompagné.
     */
    private function ecrireLesBornesDeValidite(): void
    {
        $this->addSql('ALTER TABLE bateau ADD CONSTRAINT chk_bateau_capacite CHECK (capacite > 0)');
        $this->addSql(
            'ALTER TABLE bateau ADD CONSTRAINT chk_bateau_forfait '
            .'CHECK (forfait_privatisation IS NULL OR forfait_privatisation > 0)'
        );
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT chk_tarif_prix_adulte CHECK (prix_adulte > 0)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT chk_tarif_prix_enfant CHECK (prix_enfant > 0)');
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT chk_reservation_participants '
            .'CHECK (nombre_adultes >= 1 AND nombre_enfants >= 0)'
        );
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT chk_reservation_montant CHECK (montant >= 0)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT chk_reservation_montant');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT chk_reservation_participants');
        $this->addSql('ALTER TABLE tarif DROP CONSTRAINT chk_tarif_prix_enfant');
        $this->addSql('ALTER TABLE tarif DROP CONSTRAINT chk_tarif_prix_adulte');
        $this->addSql('ALTER TABLE bateau DROP CONSTRAINT chk_bateau_forfait');
        $this->addSql('ALTER TABLE bateau DROP CONSTRAINT chk_bateau_capacite');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT chk_reservation_non_cumul');
        $this->addSql('DROP INDEX uniq_sortie_creneau_baleines ON sortie');
        $this->addSql('ALTER TABLE sortie DROP creneau_baleines');

        $this->addSql('ALTER TABLE choix_annulation DROP FOREIGN KEY FK_A4FDB37B83297E7');
        $this->addSql('ALTER TABLE choix_annulation DROP FOREIGN KEY FK_A4FDB37C36D46DB');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB83297E7');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955CC72D953');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554E7C433F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C36D46DB');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F27D0729A9');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2A9706509');
        $this->addSql('DROP TABLE avoir');
        $this->addSql('DROP TABLE bateau');
        $this->addSql('DROP TABLE bon_cadeau');
        $this->addSql('DROP TABLE choix_annulation');
        $this->addSql('DROP TABLE creneau');
        $this->addSql('DROP TABLE gerant');
        $this->addSql('DROP TABLE jour_fermeture');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('DROP TABLE tarif');
    }
}
