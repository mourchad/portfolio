<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260907191953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(80) NOT NULL, original_name VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, size INT NOT NULL, kind VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6A2CA10C3C0BE965 (filename), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(180) DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, is_read TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE path_step (id INT AUTO_INCREMENT NOT NULL, year VARCHAR(12) NOT NULL, date_label VARCHAR(80) NOT NULL, title VARCHAR(180) NOT NULL, type VARCHAR(40) NOT NULL, place VARCHAR(160) DEFAULT NULL, icon VARCHAR(60) DEFAULT NULL, description LONGTEXT DEFAULT NULL, tags JSON NOT NULL, featured TINYINT DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, about_badge VARCHAR(200) NOT NULL, hero_lead LONGTEXT NOT NULL, typed_roles JSON NOT NULL, photo_path VARCHAR(255) DEFAULT NULL, cv_path VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, phone1 VARCHAR(40) DEFAULT NULL, phone2 VARCHAR(40) DEFAULT NULL, address VARCHAR(200) DEFAULT NULL, footer_bio LONGTEXT DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, stat_mention VARCHAR(40) DEFAULT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(80) NOT NULL, technologies JSON NOT NULL, image VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, icon VARCHAR(60) DEFAULT NULL, featured TINYINT DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, skill_group VARCHAR(20) NOT NULL, percent INT DEFAULT 0 NOT NULL, icon VARCHAR(60) DEFAULT NULL, description LONGTEXT DEFAULT NULL, tags JSON NOT NULL, items JSON NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE social_link (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(60) NOT NULL, url VARCHAR(255) NOT NULL, icon VARCHAR(60) NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE path_step');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE social_link');
        $this->addSql('DROP TABLE `user`');
    }
}
