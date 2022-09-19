<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220919102452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE feed_id_seq CASCADE');
        $this->addSql('DROP TABLE feed');
        $this->addSql('ALTER TABLE "user" ADD google_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD github_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE feed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE feed (id INT NOT NULL, user_id INT NOT NULL, author_name VARCHAR(255) NOT NULL, post_id INT NOT NULL, post_filename VARCHAR(255) NOT NULL, post_description TEXT DEFAULT NULL, post_created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, author_id INT NOT NULL, author_nickname VARCHAR(255) DEFAULT NULL, author_picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN feed.post_created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "user" DROP google_id');
        $this->addSql('ALTER TABLE "user" DROP github_id');
    }
}
