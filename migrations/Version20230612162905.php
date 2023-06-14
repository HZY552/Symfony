<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612162905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE freelancer_profile ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE freelancer_profile ADD CONSTRAINT FK_83BAC59A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83BAC59A76ED395 ON freelancer_profile (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE freelancer_profile DROP FOREIGN KEY FK_83BAC59A76ED395');
        $this->addSql('DROP INDEX UNIQ_83BAC59A76ED395 ON freelancer_profile');
        $this->addSql('ALTER TABLE freelancer_profile DROP user_id');
    }
}
