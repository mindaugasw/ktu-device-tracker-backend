<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320192033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE usage_history (timestamp DATETIME NOT NULL, user VARCHAR(64) NOT NULL, device VARCHAR(64) NOT NULL, INDEX IDX_793E06548D93D649 (user), INDEX IDX_793E065492FB68E (device), PRIMARY KEY(user, device, timestamp)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (unique_id VARCHAR(64) NOT NULL, last_user VARCHAR(64) DEFAULT NULL, name VARCHAR(255) NOT NULL, sim_card TINYINT(1) NOT NULL, os VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_92FB68E1BB81215 (last_user), PRIMARY KEY(unique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (qr_code VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, office VARCHAR(255) NOT NULL, floor INT NOT NULL, PRIMARY KEY(qr_code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usage_history ADD CONSTRAINT FK_793E06548D93D649 FOREIGN KEY (user) REFERENCES user (qr_code)');
        $this->addSql('ALTER TABLE usage_history ADD CONSTRAINT FK_793E065492FB68E FOREIGN KEY (device) REFERENCES device (unique_id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E1BB81215 FOREIGN KEY (last_user) REFERENCES user (qr_code)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE usage_history DROP FOREIGN KEY FK_793E065492FB68E');
        $this->addSql('ALTER TABLE usage_history DROP FOREIGN KEY FK_793E06548D93D649');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E1BB81215');
        $this->addSql('DROP TABLE usage_history');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE user');
    }
}
