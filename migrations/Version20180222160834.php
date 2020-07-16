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
final class Version20180222160834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS data_points (id INT AUTO_INCREMENT NOT NULL, hydrometer_id INT DEFAULT NULL, fermentation_id INT DEFAULT NULL, changed DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, angle DOUBLE PRECISION DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, battery DOUBLE PRECISION DEFAULT NULL, gravity DOUBLE PRECISION DEFAULT NULL, trubidity DOUBLE PRECISION DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_4744BF30783B1F49 (hydrometer_id), INDEX IDX_4744BF30E2D83DFA (fermentation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS fermentations (id INT AUTO_INCREMENT NOT NULL, hydrometer_id INT DEFAULT NULL, user_id INT DEFAULT NULL, calibration_id INT DEFAULT NULL, name VARCHAR(190) DEFAULT NULL, begin DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, is_public TINYINT(1) DEFAULT NULL, changed DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_A05B767A783B1F49 (hydrometer_id), INDEX IDX_A05B767AA76ED395 (user_id), INDEX IDX_A05B767A8DE210C5 (calibration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS hydrometers (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token_id INT DEFAULT NULL, esp_id VARCHAR(255) DEFAULT NULL, name VARCHAR(190) DEFAULT NULL, metricTemperature VARCHAR(190) DEFAULT NULL, metricGravity VARCHAR(190) DEFAULT NULL, changed DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_8234F3C1A76ED395 (user_id), UNIQUE INDEX UNIQ_8234F3C141DEE7B9 (token_id), UNIQUE INDEX id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(190) DEFAULT NULL, value VARCHAR(190) DEFAULT NULL, was_used INT DEFAULT NULL, changed DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_5F37A13BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(190) NOT NULL, username VARCHAR(190) NOT NULL, apiToken VARCHAR(64) DEFAULT NULL, timeZone VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, changed DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS calibrations (id INT AUTO_INCREMENT NOT NULL, hydrometer_id INT DEFAULT NULL, name VARCHAR(190) DEFAULT NULL, const1 DOUBLE PRECISION NOT NULL, const2 DOUBLE PRECISION NOT NULL, const3 DOUBLE PRECISION NOT NULL, changed DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_9F4C4C61783B1F49 (hydrometer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_points ADD CONSTRAINT FK_4744BF30783B1F49 FOREIGN KEY IF NOT EXISTS (hydrometer_id) REFERENCES hydrometers (id)');
        $this->addSql('ALTER TABLE data_points ADD CONSTRAINT FK_4744BF30E2D83DFA FOREIGN KEY IF NOT EXISTS (fermentation_id) REFERENCES fermentations (id)');
        $this->addSql('ALTER TABLE fermentations ADD CONSTRAINT FK_A05B767A783B1F49 FOREIGN KEY IF NOT EXISTS (hydrometer_id) REFERENCES hydrometers (id)');
        $this->addSql('ALTER TABLE fermentations ADD CONSTRAINT FK_A05B767AA76ED395 FOREIGN KEY IF NOT EXISTS (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE fermentations ADD CONSTRAINT FK_A05B767A8DE210C5 FOREIGN KEY IF NOT EXISTS (calibration_id) REFERENCES calibrations (id)');
        $this->addSql('ALTER TABLE hydrometers ADD CONSTRAINT FK_8234F3C1A76ED395 FOREIGN KEY IF NOT EXISTS (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE hydrometers ADD CONSTRAINT FK_8234F3C141DEE7B9 FOREIGN KEY IF NOT EXISTS (token_id) REFERENCES token (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY IF NOT EXISTS (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE calibrations ADD CONSTRAINT FK_9F4C4C61783B1F49 FOREIGN KEY IF NOT EXISTS (hydrometer_id) REFERENCES hydrometers (id)');
    }

    public function down(Schema $schema): void
    {
        // no down migration - this migration is initial
    }
}
