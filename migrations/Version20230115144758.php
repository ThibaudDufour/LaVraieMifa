<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230115144758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE score_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, snake INT NOT NULL, tetris INT NOT NULL, game2048 INT NOT NULL, flappy_bird INT NOT NULL, space_invaders INT NOT NULL, bubble_shooter INT NOT NULL, jurassic_park INT NOT NULL, doodle_jump INT NOT NULL, UNIQUE INDEX UNIQ_A78B573FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE score_user ADD CONSTRAINT FK_A78B573FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score_user DROP FOREIGN KEY FK_A78B573FA76ED395');
        $this->addSql('DROP TABLE score_user');
    }
}
