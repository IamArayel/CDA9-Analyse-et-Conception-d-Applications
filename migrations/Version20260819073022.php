<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * L'adresse du bénéficiaire d'un bon cadeau ou d'un avoir.
 *
 * Absente du MLD de J5 : ni l'un ni l'autre ne portait de donnée personnelle,
 * ce qui rendait la purge de SPEC-NFR-04 sans objet sur eux. Or c'est
 * précisément cette adresse que l'exception des trois mois doit épargner tant
 * que le code est vivant. `docs/mcd-mld.md` est à compléter.
 */
final class Version20260819073022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adresse du bénéficiaire sur bon_cadeau et avoir, seule donnée personnelle qu\'un code porte.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avoir ADD email_beneficiaire VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE bon_cadeau ADD email_beneficiaire VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avoir DROP email_beneficiaire');
        $this->addSql('ALTER TABLE bon_cadeau DROP email_beneficiaire');
    }
}
