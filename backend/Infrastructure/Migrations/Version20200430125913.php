<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200430125913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Change extraRules column type';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users CHANGE steam_id steam_id BIGINT UNSIGNED NOT NULL, CHANGE extra_rules extra_rules LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE participants CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL, CHANGE extra_rules extra_rules LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE participants CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL, CHANGE extra_rules extra_rules VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE steam_id steam_id BIGINT UNSIGNED NOT NULL, CHANGE extra_rules extra_rules VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
