<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220521155504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7E8585C877153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_customer (newsletter_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_5C3B486322DB1917 (newsletter_id), INDEX IDX_5C3B48639395C3F3 (customer_id), PRIMARY KEY(newsletter_id, customer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE newsletter_customer ADD CONSTRAINT FK_5C3B486322DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_customer ADD CONSTRAINT FK_5C3B48639395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE newsletter_customer DROP FOREIGN KEY FK_5C3B486322DB1917');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE newsletter_customer');
    }
}
