<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230311144918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__episode AS SELECT id, season_id, number, watched FROM episode');
        $this->addSql('DROP TABLE episode');
        $this->addSql('CREATE TABLE episode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, season_id INTEGER NOT NULL, number INTEGER NOT NULL, watched BOOLEAN DEFAULT NULL, CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO episode (id, season_id, number, watched) SELECT id, season_id, number, watched FROM __temp__episode');
        $this->addSql('DROP TABLE __temp__episode');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA4EC001D1 ON episode (season_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__episode AS SELECT id, season_id, watched, number FROM episode');
        $this->addSql('DROP TABLE episode');
        $this->addSql('CREATE TABLE episode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, season_id INTEGER NOT NULL, watched BOOLEAN NOT NULL, number INTEGER NOT NULL, CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO episode (id, season_id, watched, number) SELECT id, season_id, watched, number FROM __temp__episode');
        $this->addSql('DROP TABLE __temp__episode');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA4EC001D1 ON episode (season_id)');
    }
}
