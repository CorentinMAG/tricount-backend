<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231170352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_token DROP FOREIGN KEY FK_BEAB6C24A76ED395');
        $this->addSql('DROP INDEX IDX_BEAB6C24A76ED395 ON password_token');
        $this->addSql('ALTER TABLE password_token CHANGE user_id admin_id INT NOT NULL');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BEAB6C24642B8210 ON password_token (admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_token DROP FOREIGN KEY FK_BEAB6C24642B8210');
        $this->addSql('DROP INDEX IDX_BEAB6C24642B8210 ON password_token');
        $this->addSql('ALTER TABLE password_token CHANGE admin_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BEAB6C24A76ED395 ON password_token (user_id)');
    }
}
