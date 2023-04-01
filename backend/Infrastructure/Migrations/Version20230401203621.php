<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use DoctrineMigrations\Version20230401201552 as NewShortGameRewardValue;
use PlayOrPay\Domain\Event\RewardReason;

final class Version20230401203621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change short game reward values for active events.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `new_earned_rewards` (
              `uuid` char(36) NOT NULL,
              `participant_id` char(36) DEFAULT NULL,
              `pick_id` char(36) DEFAULT NULL,
              `reward_id` integer DEFAULT NULL,
              `value` REAL NOT NULL,
              PRIMARY KEY (`uuid`),
              CONSTRAINT `FK_E7A06B29D1C3019` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`uuid`),
              CONSTRAINT `FK_E7A06B2E466ACA1` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`reason`),
              CONSTRAINT `FK_E7A06B2F54A307A` FOREIGN KEY (`pick_id`) REFERENCES `event_picks` (`uuid`)
            )
        ');
        $this->addSql('
            INSERT INTO new_earned_rewards (`uuid`, `participant_id`, `pick_id`, `reward_id`, `value`)
            SELECT `uuid`, `participant_id`, `pick_id`, `reward_id`, CAST(`value` as REAL)
            FROM earned_rewards
        ');
        $this->addSql('DROP TABLE earned_rewards');
        $this->addSql('ALTER TABLE new_earned_rewards RENAME TO earned_rewards');

        $this->addSql(
            '
                UPDATE earned_rewards
                SET value = ?
                WHERE reward_id = ? AND participant_id IN (
                    SELECT participant.uuid FROM participants participant
                    LEFT JOIN events event ON event.uuid = participant.event_id
                    WHERE event.active_period_end_date > date("now")
                      AND date("now") > event.active_period_start_date
                )
            ',
            [
                NewShortGameRewardValue::NEW_VALUE,
                RewardReason::SHORT_GAME_BEATEN,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        // impossible. doesn't have the data.
    }
}
