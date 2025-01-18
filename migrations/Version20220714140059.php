<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714140059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drive DROP FOREIGN KEY FK_681DF58FA76ED395');
        $this->addSql('ALTER TABLE drive CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE drive ADD CONSTRAINT FK_681DF58FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE news_feed CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE news_feed_like ADD CONSTRAINT FK_30D1F425243F98F4 FOREIGN KEY (newsfeed_id) REFERENCES news_feed (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drive DROP FOREIGN KEY FK_681DF58FA76ED395');
        $this->addSql('ALTER TABLE drive CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE drive ADD CONSTRAINT FK_681DF58FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE news_feed CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE news_feed_like DROP FOREIGN KEY FK_30D1F425243F98F4');
    }
}
