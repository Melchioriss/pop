<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200430125913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change extraRules column type';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        // do nothing because it's already done in Version20200100000000 by converting queries of actual database to SQLite
    }

    public function down(Schema $schema): void
    {
    }
}
