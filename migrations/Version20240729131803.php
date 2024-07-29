<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729131803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE code_promotion (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, value INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expire_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D5805E99D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_complements (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, zip_code VARCHAR(6) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, phone VARCHAR(10) DEFAULT NULL, INDEX IDX_935956359D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE code_promotion ADD CONSTRAINT FK_6D5805E99D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_complements ADD CONSTRAINT FK_935956359D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE cart');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_products JSON NOT NULL, date_start DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_validation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE code_promotion DROP FOREIGN KEY FK_6D5805E99D86650F');
        $this->addSql('ALTER TABLE user_complements DROP FOREIGN KEY FK_935956359D86650F');
        $this->addSql('DROP TABLE code_promotion');
        $this->addSql('DROP TABLE user_complements');
    }
}
