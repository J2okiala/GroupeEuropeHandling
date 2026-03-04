<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217165016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, employeur_id INT NOT NULL, poste VARCHAR(255) NOT NULL, type_contrat VARCHAR(255) NOT NULL, description_poste LONGTEXT NOT NULL, modalite_travail VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL, INDEX IDX_132AD0D15D7C53EC (employeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D15D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D15D7C53EC');
        $this->addSql('DROP TABLE offre_emploi');
    }
}
