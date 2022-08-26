<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220826114323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed ADD author_nickname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD author_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed DROP user_nickname');
        $this->addSql('ALTER TABLE feed DROP user_picture');
        $this->addSql('ALTER TABLE feed RENAME COLUMN user_name TO author_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE feed ADD user_nickname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD user_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed DROP author_nickname');
        $this->addSql('ALTER TABLE feed DROP author_picture');
        $this->addSql('ALTER TABLE feed RENAME COLUMN author_name TO user_name');
    }
}
