<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613174431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD freelanceprofile_id INT NOT NULL, ADD content LONGTEXT NOT NULL, ADD create_date DATE NOT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9784E662 FOREIGN KEY (freelanceprofile_id) REFERENCES freelancer_profile (id)');
        $this->addSql('CREATE INDEX IDX_9474526C9784E662 ON comment (freelanceprofile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9784E662');
        $this->addSql('DROP INDEX IDX_9474526C9784E662 ON comment');
        $this->addSql('ALTER TABLE comment DROP freelanceprofile_id, DROP content, DROP create_date, DROP user_id');
    }
}
