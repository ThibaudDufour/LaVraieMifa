<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220118132801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD score_snake INT DEFAULT 0 NOT NULL, CHANGE profil_photo_path profil_photo_path VARCHAR(255) DEFAULT \'-\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP score_snake, CHANGE profil_photo_path profil_photo_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'-\' COLLATE `utf8mb4_unicode_ci`');
    }
}
