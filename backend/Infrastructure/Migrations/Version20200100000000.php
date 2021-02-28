<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200100000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql("
            CREATE TABLE `comments` (
              `uuid` char(36) NOT NULL
            ,  `picker_id` char(36) DEFAULT NULL
            ,  `user_id` integer  DEFAULT NULL
            ,  `referenced_game_id` varchar(255) DEFAULT NULL
            ,  `text` longtext NOT NULL
            ,  `history` json NOT NULL
            ,  `created_at` datetime NOT NULL
            ,  `updated_at` datetime DEFAULT NULL
            ,  `game_reference_type` integer DEFAULT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_5F9E962A8874902` FOREIGN KEY (`picker_id`) REFERENCES `event_pickers` (`uuid`)
            ,  CONSTRAINT `FK_5F9E962AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`steam_id`)
            ,  CONSTRAINT `FK_5F9E962AE4A93D08` FOREIGN KEY (`referenced_game_id`) REFERENCES `games` (`complex_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `content_blocks` (
              `code` varchar(255) NOT NULL
            ,  `content` longtext NOT NULL
            ,  `created_at` datetime NOT NULL
            ,  `updated_at` datetime DEFAULT NULL
            ,  PRIMARY KEY (`code`)
            )
        ");

        $this->addSql("
            CREATE TABLE `domain_event_records` (
              `uuid` varchar(255) NOT NULL
            ,  `actor_id` integer  DEFAULT NULL
            ,  `name` varchar(255) NOT NULL
            ,  `payload` json NOT NULL
            ,  `created_at` datetime NOT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_8A5A010210DAF24A` FOREIGN KEY (`actor_id`) REFERENCES `users` (`steam_id`)
            );
        ");

        $this->addSql("
            CREATE TABLE `earned_rewards` (
              `uuid` char(36) NOT NULL
            ,  `participant_id` char(36) DEFAULT NULL
            ,  `pick_id` char(36) DEFAULT NULL
            ,  `reward_id` integer DEFAULT NULL
            ,  `value` integer NOT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_E7A06B29D1C3019` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`uuid`)
            ,  CONSTRAINT `FK_E7A06B2E466ACA1` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`reason`)
            ,  CONSTRAINT `FK_E7A06B2F54A307A` FOREIGN KEY (`pick_id`) REFERENCES `event_picks` (`uuid`)
            )
        ");

        $this->addSql("
            CREATE TABLE `event_pickers` (
              `uuid` char(36) NOT NULL
            ,  `participant_id` char(36) DEFAULT NULL
            ,  `user_id` integer  DEFAULT NULL
            ,  `type` integer NOT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  UNIQUE (`participant_id`,`type`)
            ,  CONSTRAINT `FK_7304B7CE9D1C3019` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`uuid`)
            ,  CONSTRAINT `FK_7304B7CEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`steam_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `event_picks` (
              `uuid` char(36) NOT NULL
            ,  `picker_id` char(36) DEFAULT NULL
            ,  `game_id` varchar(255) DEFAULT NULL
            ,  `type` integer NOT NULL
            ,  `status` integer NOT NULL
            ,  `played_status` integer NOT NULL
            ,  `playing_state_playtime` double DEFAULT NULL
            ,  `playing_state_achievements` integer DEFAULT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_3D51F9E48874902` FOREIGN KEY (`picker_id`) REFERENCES `event_pickers` (`uuid`)
            ,  CONSTRAINT `FK_3D51F9E4E48FD905` FOREIGN KEY (`game_id`) REFERENCES `games` (`complex_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `events` (
              `uuid` char(36) NOT NULL
            ,  `group_id` integer  DEFAULT NULL
            ,  `name` varchar(255) NOT NULL
            ,  `description` longtext NOT NULL
            ,  `created_at` datetime NOT NULL
            ,  `updated_at` datetime DEFAULT NULL
            ,  `active_period_start_date` datetime NOT NULL
            ,  `active_period_end_date` datetime NOT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_5387574AFE54D947` FOREIGN KEY (`group_id`) REFERENCES `steam_groups` (`id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `games` (
              `complex_id` varchar(255) NOT NULL
            ,  `name` varchar(255) NOT NULL
            ,  `achievements` integer DEFAULT NULL
            ,  `id_store_id` integer NOT NULL
            ,  `id_local_id` varchar(255) NOT NULL
            ,  PRIMARY KEY (`complex_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `participants` (
              `uuid` char(36) NOT NULL
            ,  `event_id` char(36) DEFAULT NULL
            ,  `user_id` integer  DEFAULT NULL
            ,  `active` integer NOT NULL
            ,  `group_wins` varchar(255) NOT NULL
            ,  `blaeo_games` varchar(255) NOT NULL
            ,  `extra_rules` longtext NOT NULL
            ,  PRIMARY KEY (`uuid`)
            ,  CONSTRAINT `FK_7169709271F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`uuid`)
            ,  CONSTRAINT `FK_71697092A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`steam_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `rewards` (
              `reason` integer NOT NULL
            ,  `value` integer DEFAULT NULL
            ,  PRIMARY KEY (`reason`)
            )
        ");

        $this->addSql("
            CREATE TABLE `roles` (
              `name` varchar(255) NOT NULL
            ,  `abilities` json NOT NULL
            ,  PRIMARY KEY (`name`)
            )
        ");

        $this->addSql("
            CREATE TABLE `steam_group_members` (
              `group_id` integer  NOT NULL
            ,  `user_steam_id` integer  NOT NULL
            ,  PRIMARY KEY (`group_id`,`user_steam_id`)
            ,  CONSTRAINT `FK_D04982DF1370AACA` FOREIGN KEY (`user_steam_id`) REFERENCES `users` (`steam_id`)
            ,  CONSTRAINT `FK_D04982DFFE54D947` FOREIGN KEY (`group_id`) REFERENCES `steam_groups` (`id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `steam_groups` (
              `id` integer  NOT NULL
            ,  `code` varchar(255) NOT NULL
            ,  `name` varchar(255) NOT NULL
            ,  `logo_url` varchar(255) NOT NULL
            ,  PRIMARY KEY (`id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `user_roles` (
              `user` integer  NOT NULL
            ,  `role` varchar(255) NOT NULL
            ,  PRIMARY KEY (`user`,`role`)
            ,  CONSTRAINT `FK_54FCD59F57698A6A` FOREIGN KEY (`role`) REFERENCES `roles` (`name`)
            ,  CONSTRAINT `FK_54FCD59F8D93D649` FOREIGN KEY (`user`) REFERENCES `users` (`steam_id`)
            )
        ");

        $this->addSql("
            CREATE TABLE `users` (
              `steam_id` integer  NOT NULL
            ,  `created_at` datetime NOT NULL
            ,  `updated_at` datetime DEFAULT NULL
            ,  `community_visibility_state` integer DEFAULT NULL
            ,  `profile_state` integer DEFAULT NULL
            ,  `profile_name` varchar(255) NOT NULL
            ,  `last_log_off` datetime DEFAULT NULL
            ,  `comment_permission` integer DEFAULT NULL
            ,  `profile_url` varchar(255) NOT NULL
            ,  `avatar` varchar(255) NOT NULL
            ,  `persona_state` integer DEFAULT NULL
            ,  `primary_clan_id` integer  DEFAULT NULL
            ,  `join_date` datetime DEFAULT NULL
            ,  `country_code` varchar(255) DEFAULT NULL
            ,  `blaeo_name` varchar(255) DEFAULT NULL
            ,  `active` integer NOT NULL
            ,  `extra_rules` longtext COLLATE BINARY
            ,  PRIMARY KEY (`steam_id`)
            )
        ");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE content_blocks');
        $this->addSql('DROP TABLE domain_event_records');
        $this->addSql('DROP TABLE earned_rewards');
        $this->addSql('DROP TABLE event_pickers');
        $this->addSql('DROP TABLE event_picks');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE rewards');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE steam_group_members');
        $this->addSql('DROP TABLE steam_groups');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE users');
    }
}
