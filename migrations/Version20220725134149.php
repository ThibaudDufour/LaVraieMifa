<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220725134149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_feed_like DROP FOREIGN KEY FK_30D1F425243F98F4');
        $this->addSql('ALTER TABLE news_feed_like CHANGE newsfeed_id newsfeed_id INT NOT NULL');
        $this->addSql('ALTER TABLE news_feed_like ADD CONSTRAINT FK_30D1F425243F98F4 FOREIGN KEY (newsfeed_id) REFERENCES news_feed (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD score_pac_man INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_feed_like DROP FOREIGN KEY FK_30D1F425243F98F4');
        $this->addSql('ALTER TABLE news_feed_like CHANGE newsfeed_id newsfeed_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE news_feed_like ADD CONSTRAINT FK_30D1F425243F98F4 FOREIGN KEY (newsfeed_id) REFERENCES news_feed (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user DROP score_pac_man');
    }
}
