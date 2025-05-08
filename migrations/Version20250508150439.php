<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508150439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transaction_split (id INT AUTO_INCREMENT NOT NULL, transaction_id INT NOT NULL, user_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, is_paid TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_10C64A512FC0CB0F (transaction_id), INDEX IDX_10C64A51A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction_split ADD CONSTRAINT FK_10C64A512FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE transaction_split ADD CONSTRAINT FK_10C64A51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction_split DROP FOREIGN KEY FK_10C64A512FC0CB0F');
        $this->addSql('ALTER TABLE transaction_split DROP FOREIGN KEY FK_10C64A51A76ED395');
        $this->addSql('DROP TABLE transaction_split');
    }
}
