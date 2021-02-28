<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use PlayOrPay\Domain\Event\EventReward;
use PlayOrPay\Domain\Event\RewardReason;

final class Version20200219194657 extends AbstractMigration
{
    const REWARDS = 'rewards';

    public function getDescription(): string
    {
        return 'Default achievements';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->getRewards() as $achievement) {
            $this->connection->insert(self::REWARDS, [
                'reason' => (int) (string) $achievement->getReason(),
                'value'  => $achievement->getValue(),
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->getRewards() as $achievement) {
            $this->connection->delete(self::REWARDS, [
                'reason' => (string) $achievement->getReason(),
            ]);
        }
    }

    /**
     * @return EventReward[]
     */
    public function getRewards(): array
    {
        return [
            new EventReward(new RewardReason(RewardReason::SHORT_GAME_BEATEN), 2),
            new EventReward(new RewardReason(RewardReason::MEDIUM_GAME_BEATEN), 4),
            new EventReward(new RewardReason(RewardReason::LONG_GAME_BEATEN), 6),
            new EventReward(new RewardReason(RewardReason::VERY_LONG_GAME_BEATEN), 8),
            new EventReward(new RewardReason(RewardReason::GAME_COMPLETED), 1),
            new EventReward(new RewardReason(RewardReason::BLAEO_GAMES), null),
            new EventReward(new RewardReason(RewardReason::ALL_PICKS_BEATEN), 1),
        ];
    }
}
