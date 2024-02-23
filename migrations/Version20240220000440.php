<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220000440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id BLOB NOT NULL --(DC2Type:ulid)
        , aggregate_id BLOB DEFAULT NULL --(DC2Type:ulid)
        , added DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , type VARCHAR(255) NOT NULL, data CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE hydrometer (id BLOB NOT NULL --(DC2Type:ulid)
        , name VARCHAR(255) DEFAULT NULL, added DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE hydrometer');
    }
}
