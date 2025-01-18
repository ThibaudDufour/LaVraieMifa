<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202232423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member_profil DROP FOREIGN KEY FK_C20030A49D86650F');
        $this->addSql('ALTER TABLE member_profil ADD CONSTRAINT FK_C20030A49D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member_profil DROP FOREIGN KEY FK_C20030A49D86650F');
        $this->addSql('ALTER TABLE member_profil ADD CONSTRAINT FK_C20030A49D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
