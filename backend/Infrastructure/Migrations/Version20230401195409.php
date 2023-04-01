<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230401195409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change reward value schema from integer to float.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `new_rewards` (
              `reason` INTEGER NOT NULL,
              `value` REAL DEFAULT NULL,
              PRIMARY KEY (`reason`)
            )
        ');
        $this->addSql('
            INSERT INTO new_rewards (`reason`, `value`)
            SELECT `reason`, CAST(`value` as REAL) FROM rewards
        ');
        $this->addSql('DROP TABLE rewards');
        $this->addSql('ALTER TABLE new_rewards RENAME TO rewards');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `old_rewards` (
              `reason` INTEGER NOT NULL,
              `value` INTEGER DEFAULT NULL,
              PRIMARY KEY (`reason`)
            )
        ');
        $this->addSql('
            INSERT INTO old_rewards (`reason`, `value`)
            SELECT `reason`, CAST(`value` as INTEGER) FROM rewards
        ');
        $this->addSql('DROP TABLE rewards');
        $this->addSql('ALTER TABLE old_rewards RENAME TO rewards');
    }
}
