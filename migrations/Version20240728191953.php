<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240729123456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cart table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (
            id INT AUTO_INCREMENT NOT NULL, 
            id_user INT NOT NULL, 
            id_products JSON NOT NULL, 
            date_start DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            date_validation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cart');
    }
}
