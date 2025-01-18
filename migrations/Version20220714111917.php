<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714111917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news_feed_like (id INT AUTO_INCREMENT NOT NULL, newsfeed_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_30D1F425243F98F4 (newsfeed_id), INDEX IDX_30D1F425A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news_feed_like ADD CONSTRAINT FK_30D1F425243F98F4 FOREIGN KEY (newsfeed_id) REFERENCES news_feed (id)');
        $this->addSql('ALTER TABLE news_feed_like ADD CONSTRAINT FK_30D1F425A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE news_feed_like');
    }
}
