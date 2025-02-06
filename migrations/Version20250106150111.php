<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106150111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, nationalite VARCHAR(50) DEFAULT NULL, date_naissance DATE DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, poste VARCHAR(100) DEFAULT NULL, date_disponibilite DATE DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, lettre_motivation VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6AB5B471FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidats_offres_emploi (candidat_id INT NOT NULL, offre_emploi_id INT NOT NULL, INDEX IDX_C77F03D08D0EB82 (candidat_id), INDEX IDX_C77F03D0B08996ED (offre_emploi_id), PRIMARY KEY(candidat_id, offre_emploi_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employeur (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, entreprise VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8747E1C7FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, employeur_id INT NOT NULL, poste VARCHAR(255) NOT NULL, type_contrat VARCHAR(255) NOT NULL, description_poste LONGTEXT NOT NULL, modalite_travail VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL, INDEX IDX_132AD0D15D7C53EC (employeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, civilite VARCHAR(10) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE candidat ADD CONSTRAINT FK_6AB5B471FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE candidats_offres_emploi ADD CONSTRAINT FK_C77F03D08D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidats_offres_emploi ADD CONSTRAINT FK_C77F03D0B08996ED FOREIGN KEY (offre_emploi_id) REFERENCES offre_emploi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur ADD CONSTRAINT FK_8747E1C7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D15D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76FB88E14F');
        $this->addSql('ALTER TABLE candidat DROP FOREIGN KEY FK_6AB5B471FB88E14F');
        $this->addSql('ALTER TABLE candidats_offres_emploi DROP FOREIGN KEY FK_C77F03D08D0EB82');
        $this->addSql('ALTER TABLE candidats_offres_emploi DROP FOREIGN KEY FK_C77F03D0B08996ED');
        $this->addSql('ALTER TABLE employeur DROP FOREIGN KEY FK_8747E1C7FB88E14F');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D15D7C53EC');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE candidats_offres_emploi');
        $this->addSql('DROP TABLE employeur');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
