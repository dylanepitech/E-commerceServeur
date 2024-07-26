<?php
 
declare(strict_types=1);
 
namespace DoctrineMigrations;
 
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Types\Types;
 
/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20240725150545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the user table with all required fields.';
    }
 
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL, 
                email VARCHAR(180) NOT NULL, 
                roles JSON NOT NULL, 
                password VARCHAR(255) NOT NULL, 
                firstname VARCHAR(255) NOT NULL, 
                lastname VARCHAR(255) NOT NULL, 
                token VARCHAR(255) DEFAULT NULL, 
                is_verified TINYINT(1) NOT NULL, 
                picture LONGTEXT DEFAULT NULL, 
                created_at DATETIME(0) NOT NULL, 
                updated_at DATETIME(0) NOT NULL, 
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }
 
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
    }
}