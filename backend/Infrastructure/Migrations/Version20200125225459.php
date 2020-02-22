<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use PlayOrPay\Domain\Role\Ability;
use PlayOrPay\Domain\Role\RoleName;

final class Version20200125225459 extends AbstractMigration
{
    const TABLE = 'roles';

    public function getDescription(): string
    {
        return 'Default roles';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->getRoles() as $name => $abilities) {
            $abilities = json_encode($abilities);
            $this->connection->insert(self::TABLE, compact('name', 'abilities'));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (array_keys($this->getRoles()) as $role) {
            $this->connection->delete(self::TABLE, $role);
        }
    }

    private function getRoles(): array
    {
        return [
            RoleName::ADMIN => [Ability::ANYTHING],
            RoleName::USER  => [],
        ];
    }
}
