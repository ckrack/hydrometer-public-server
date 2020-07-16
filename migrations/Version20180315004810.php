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
class Version20180315004810 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE calibrations CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE name name VARCHAR(190) DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT NULL, CHANGE deleted deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE data_points CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE fermentation_id fermentation_id INT DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT NULL, CHANGE deleted deleted DATETIME DEFAULT NULL, CHANGE angle angle DOUBLE PRECISION DEFAULT NULL, CHANGE temperature temperature DOUBLE PRECISION DEFAULT NULL, CHANGE battery battery DOUBLE PRECISION DEFAULT NULL, CHANGE gravity gravity DOUBLE PRECISION DEFAULT NULL, CHANGE trubidity trubidity DOUBLE PRECISION DEFAULT NULL, CHANGE RSSI rssi DOUBLE PRECISION DEFAULT NULL, CHANGE `interval` `interval` INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fermentations CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE calibration_id calibration_id INT DEFAULT NULL, CHANGE name name VARCHAR(190) DEFAULT NULL, CHANGE begin begin DATETIME DEFAULT NULL, CHANGE end end DATETIME DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT NULL, CHANGE deleted deleted DATETIME DEFAULT NULL, CHANGE is_public is_public TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE hydrometers CHANGE user_id user_id INT DEFAULT NULL, CHANGE token_id token_id INT DEFAULT NULL, CHANGE esp_id esp_id VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(190) DEFAULT NULL, CHANGE metricGravity metric_gravity VARCHAR(190) DEFAULT NULL, CHANGE metricTemperature metric_temperature VARCHAR(190) DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE token CHANGE user_id user_id INT DEFAULT NULL, CHANGE type type VARCHAR(190) DEFAULT NULL, CHANGE value value VARCHAR(190) DEFAULT NULL, CHANGE was_used was_used INT DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE changed changed DATETIME DEFAULT NULL, CHANGE deleted deleted DATETIME DEFAULT NULL, CHANGE language language VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE calibrations CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE name name VARCHAR(190) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE changed changed DATETIME DEFAULT \'NULL\', CHANGE deleted deleted DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE data_points CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE fermentation_id fermentation_id INT DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT \'NULL\', CHANGE deleted deleted DATETIME DEFAULT \'NULL\', CHANGE angle angle DOUBLE PRECISION DEFAULT \'NULL\', CHANGE temperature temperature DOUBLE PRECISION DEFAULT \'NULL\', CHANGE battery battery DOUBLE PRECISION DEFAULT \'NULL\', CHANGE gravity gravity DOUBLE PRECISION DEFAULT \'NULL\', CHANGE trubidity trubidity DOUBLE PRECISION DEFAULT \'NULL\', CHANGE rssi RSSI DOUBLE PRECISION DEFAULT \'NULL\', CHANGE `interval` `interval` INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fermentations CHANGE hydrometer_id hydrometer_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE calibration_id calibration_id INT DEFAULT NULL, CHANGE name name VARCHAR(190) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE begin begin DATETIME DEFAULT \'NULL\', CHANGE end end DATETIME DEFAULT \'NULL\', CHANGE is_public is_public TINYINT(1) DEFAULT \'NULL\', CHANGE changed changed DATETIME DEFAULT \'NULL\', CHANGE deleted deleted DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE hydrometers CHANGE user_id user_id INT DEFAULT NULL, CHANGE token_id token_id INT DEFAULT NULL, CHANGE esp_id esp_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE name name VARCHAR(190) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE changed changed DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE token CHANGE user_id user_id INT DEFAULT NULL, CHANGE type type VARCHAR(190) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE value value VARCHAR(190) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE was_used was_used INT DEFAULT NULL, CHANGE changed changed DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users CHANGE language language VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE changed changed DATETIME DEFAULT \'NULL\', CHANGE deleted deleted DATETIME DEFAULT \'NULL\'');
    }
}
