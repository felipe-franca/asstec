<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416201327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(50) NOT NULL, number INT NOT NULL, district VARCHAR(30) NOT NULL, uf VARCHAR(2) NOT NULL, postal_code VARCHAR(8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_user (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_5C0F152BE7927C74 (email), UNIQUE INDEX UNIQ_5C0F152BF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, responsable_id INT DEFAULT NULL, client_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ticket_number VARCHAR(255) NOT NULL, closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', service_start DATETIME DEFAULT NULL, service_end DATETIME DEFAULT NULL, reason LONGTEXT NOT NULL, observation LONGTEXT DEFAULT NULL, solution LONGTEXT DEFAULT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_54469DF453C59D72 (responsable_id), INDEX IDX_54469DF419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_phone (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, phone_id INT DEFAULT NULL, client_id INT DEFAULT NULL, INDEX IDX_A68D6C85A76ED395 (user_id), INDEX IDX_A68D6C853B7323CB (phone_id), INDEX IDX_A68D6C8519EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_user ADD CONSTRAINT FK_5C0F152BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF453C59D72 FOREIGN KEY (responsable_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF419EB6921 FOREIGN KEY (client_id) REFERENCES client_user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE user_phone ADD CONSTRAINT FK_A68D6C85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_phone ADD CONSTRAINT FK_A68D6C853B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id)');
        $this->addSql('ALTER TABLE user_phone ADD CONSTRAINT FK_A68D6C8519EB6921 FOREIGN KEY (client_id) REFERENCES client_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client_user DROP FOREIGN KEY FK_5C0F152BF5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF419EB6921');
        $this->addSql('ALTER TABLE user_phone DROP FOREIGN KEY FK_A68D6C8519EB6921');
        $this->addSql('ALTER TABLE user_phone DROP FOREIGN KEY FK_A68D6C853B7323CB');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF453C59D72');
        $this->addSql('ALTER TABLE user_phone DROP FOREIGN KEY FK_A68D6C85A76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE client_user');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_phone');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
