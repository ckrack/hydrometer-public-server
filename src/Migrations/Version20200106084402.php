<?php

declare(strict_types=1);

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106084402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_points CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, CHANGE deleted deleted_at DATETIME DEFAULT NULL, DROP changed');
        $this->addSql('ALTER TABLE fermentations CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, CHANGE deleted deleted_at DATETIME DEFAULT NULL, DROP changed');
        $this->addSql('ALTER TABLE hydrometers CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, DROP changed');
        $this->addSql('ALTER TABLE token CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, DROP changed');
        $this->addSql('ALTER TABLE users CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, CHANGE deleted deleted_at DATETIME DEFAULT NULL, DROP changed');
        $this->addSql('ALTER TABLE calibrations CHANGE created created_at DATETIME DEFAULT NULL, CHANGE updated updated_at DATETIME DEFAULT NULL, CHANGE deleted deleted_at DATETIME DEFAULT NULL, DROP changed');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE calibrations ADD changed DATETIME DEFAULT NULL, CHANGE deleted_at deleted DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_points ADD changed DATETIME DEFAULT NULL, CHANGE deleted_at deleted DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE fermentations ADD changed DATETIME DEFAULT NULL, CHANGE deleted_at deleted DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE hydrometers ADD changed DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE token ADD changed DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD changed DATETIME DEFAULT NULL, CHANGE deleted_at deleted DATETIME DEFAULT NULL, CHANGE created_at created DATETIME DEFAULT NULL, CHANGE updated_at updated DATETIME DEFAULT NULL');
    }
}
