<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * La prévision météo saisie par le gérant.
 *
 * Cette table ne figurait pas au MLD de J5. Elle est apparue en écrivant
 * SPEC-CANCEL-05, dont le cas de test exige que le message de rappel transporte
 * la prévision et les affaires à prévoir. Rien à écrire à la main ici : aucune
 * règle métier n'est portée par le schéma, seulement l'unicité d'une prévision
 * par journée.
 */
final class Version20260819070545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Prévision météo du jour, saisie par le gérant, portée par le message de rappel.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE prevision_meteo (date_prevision DATE NOT NULL, texte VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX uniq_prevision_meteo_date (date_prevision), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE prevision_meteo');
    }
}
