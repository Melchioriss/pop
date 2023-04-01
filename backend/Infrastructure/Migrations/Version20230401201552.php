<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use PlayOrPay\Domain\Event\RewardReason;

final class Version20230401201552 extends AbstractMigration
{
    private const OLD_VALUE = 2;
    public const NEW_VALUE = 2.5;

    public function getDescription(): string
    {
        return 'Change short game reward from 2 to 2.5.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE rewards SET value = ? WHERE reason = ?',
            [self::NEW_VALUE, RewardReason::SHORT_GAME_BEATEN]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'UPDATE rewards SET value = ? WHERE reason = ?',
            [self::OLD_VALUE, RewardReason::SHORT_GAME_BEATEN]
        );
    }
}
