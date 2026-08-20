<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * La table des paiements, née de `CR-06` et de l'acompte.
 *
 * Elle porte les deux transactions de `REQ-117` et l'historique des pointages
 * de `REQ-113`. Rien à écrire à la main : aucune règle métier n'est portée par
 * le schéma ici, le montant versé se calculant par somme des lignes non
 * annulées.
 */
final class Version20260820061313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table paiement : acompte et solde, canal en ligne ou au quai, pointages annulables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE paiement (type VARCHAR(20) NOT NULL, montant INT NOT NULL, canal VARCHAR(20) NOT NULL, date_paiement DATETIME NOT NULL, pointe_par VARCHAR(180) DEFAULT NULL, annule TINYINT NOT NULL, id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, INDEX idx_paiement_reservation (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EB83297E7');
        $this->addSql('DROP TABLE paiement');
    }
}
