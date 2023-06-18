<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614174539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD start_date DATE NOT NULL, ADD end_date DATE NOT NULL, ADD unit_price NUMERIC(5, 2) NOT NULL, ADD total_price NUMERIC(7, 2) NOT NULL, ADD poste LONGTEXT NOT NULL, ADD objet LONGTEXT NOT NULL, ADD discription_mission LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP start_date, DROP end_date, DROP unit_price, DROP total_price, DROP poste, DROP objet, DROP discription_mission');
    }
}
