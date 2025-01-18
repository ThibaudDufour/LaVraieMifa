<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202231500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member_profil (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, bio VARCHAR(255) DEFAULT NULL, spotify_url VARCHAR(5000) DEFAULT NULL, orientation VARCHAR(100) DEFAULT NULL, reseaux JSON DEFAULT NULL, UNIQUE INDEX UNIQ_C20030A49D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member_profil ADD CONSTRAINT FK_C20030A49D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member_profil DROP FOREIGN KEY FK_C20030A49D86650F');
        $this->addSql('DROP TABLE member_profil');
    }
}
