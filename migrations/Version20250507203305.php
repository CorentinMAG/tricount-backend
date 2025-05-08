<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507203305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uri VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(50) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BEAB6C245F37A13B (token), INDEX IDX_BEAB6C24A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, type_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, tricount_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_723705D133B92F39 (label_id), INDEX IDX_723705D1C54C8C93 (type_id), INDEX IDX_723705D17E3C61F9 (owner_id), INDEX IDX_723705D142A1724 (tricount_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricount (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uri VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, join_uri VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_5ACF6CEC33B92F39 (label_id), INDEX IDX_5ACF6CEC38248176 (currency_id), INDEX IDX_5ACF6CEC7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricount_users (tricount_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_ECFA5DF642A1724 (tricount_id), INDEX IDX_ECFA5DF6A76ED395 (user_id), PRIMARY KEY(tricount_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricount_label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uri VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', avatar_name VARCHAR(255) DEFAULT NULL, avatar_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, country VARCHAR(2) DEFAULT \'FR\' NOT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, gravatar VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D133B92F39 FOREIGN KEY (label_id) REFERENCES transaction_label (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C54C8C93 FOREIGN KEY (type_id) REFERENCES transaction_type (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D142A1724 FOREIGN KEY (tricount_id) REFERENCES tricount (id)');
        $this->addSql('ALTER TABLE tricount ADD CONSTRAINT FK_5ACF6CEC33B92F39 FOREIGN KEY (label_id) REFERENCES tricount_label (id)');
        $this->addSql('ALTER TABLE tricount ADD CONSTRAINT FK_5ACF6CEC38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE tricount ADD CONSTRAINT FK_5ACF6CEC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tricount_users ADD CONSTRAINT FK_ECFA5DF642A1724 FOREIGN KEY (tricount_id) REFERENCES tricount (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tricount_users ADD CONSTRAINT FK_ECFA5DF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_token DROP FOREIGN KEY FK_BEAB6C24A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D133B92F39');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1C54C8C93');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D17E3C61F9');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D142A1724');
        $this->addSql('ALTER TABLE tricount DROP FOREIGN KEY FK_5ACF6CEC33B92F39');
        $this->addSql('ALTER TABLE tricount DROP FOREIGN KEY FK_5ACF6CEC38248176');
        $this->addSql('ALTER TABLE tricount DROP FOREIGN KEY FK_5ACF6CEC7E3C61F9');
        $this->addSql('ALTER TABLE tricount_users DROP FOREIGN KEY FK_ECFA5DF642A1724');
        $this->addSql('ALTER TABLE tricount_users DROP FOREIGN KEY FK_ECFA5DF6A76ED395');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE password_token');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_label');
        $this->addSql('DROP TABLE transaction_type');
        $this->addSql('DROP TABLE tricount');
        $this->addSql('DROP TABLE tricount_users');
        $this->addSql('DROP TABLE tricount_label');
        $this->addSql('DROP TABLE user');
    }
}
