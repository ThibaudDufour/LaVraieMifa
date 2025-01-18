<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714140438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drive DROP FOREIGN KEY FK_681DF58FA76ED395');
        $this->addSql('ALTER TABLE drive CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE drive ADD CONSTRAINT FK_681DF58FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drive DROP FOREIGN KEY FK_681DF58FA76ED395');
        $this->addSql('ALTER TABLE drive CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE drive ADD CONSTRAINT FK_681DF58FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
