<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200100000000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE roles (name VARCHAR(255) NOT NULL, abilities JSON NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (complex_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, achievements INT DEFAULT NULL, id_store_id INT NOT NULL, id_local_id VARCHAR(255) NOT NULL, PRIMARY KEY(complex_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (steam_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', community_visibility_state INT DEFAULT NULL, profile_state INT DEFAULT NULL, profile_name VARCHAR(255) NOT NULL, last_log_off DATETIME DEFAULT NULL, comment_permission INT DEFAULT NULL, profile_url VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, persona_state INT DEFAULT NULL, primary_clan_id BIGINT UNSIGNED DEFAULT NULL, join_date DATETIME DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, blaeo_name VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, extra_rules VARCHAR(255) DEFAULT NULL, PRIMARY KEY(steam_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (user BIGINT UNSIGNED NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_54FCD59F8D93D649 (user), INDEX IDX_54FCD59F57698A6A (role), PRIMARY KEY(user, role)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', event_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', user_id BIGINT UNSIGNED DEFAULT NULL, active TINYINT(1) NOT NULL, group_wins VARCHAR(255) NOT NULL, blaeo_games VARCHAR(255) NOT NULL, extra_rules VARCHAR(255) NOT NULL, INDEX IDX_7169709271F7E88B (event_id), INDEX IDX_71697092A76ED395 (user_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', group_id BIGINT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', active_period_start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', active_period_end_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5387574AFE54D947 (group_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE steam_groups (id BIGINT UNSIGNED NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, logo_url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE steam_group_members (group_id BIGINT UNSIGNED NOT NULL, user_steam_id BIGINT UNSIGNED NOT NULL, INDEX IDX_D04982DFFE54D947 (group_id), INDEX IDX_D04982DF1370AACA (user_steam_id), PRIMARY KEY(group_id, user_steam_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE earned_rewards (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', participant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', pick_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', reward_id INT DEFAULT NULL, value INT NOT NULL, INDEX IDX_E7A06B29D1C3019 (participant_id), INDEX IDX_E7A06B2F54A307A (pick_id), INDEX IDX_E7A06B2E466ACA1 (reward_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_blocks (code VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_picks (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', picker_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', game_id VARCHAR(255) DEFAULT NULL, type INT NOT NULL, status INT NOT NULL, played_status INT NOT NULL, playing_state_playtime DOUBLE PRECISION DEFAULT NULL, playing_state_achievements INT DEFAULT NULL, INDEX IDX_3D51F9E48874902 (picker_id), INDEX IDX_3D51F9E4E48FD905 (game_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', picker_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', user_id BIGINT UNSIGNED DEFAULT NULL, referenced_game_id VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, history JSON NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_reference_type INT DEFAULT NULL, INDEX IDX_5F9E962A8874902 (picker_id), INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962AE4A93D08 (referenced_game_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rewards (reason INT NOT NULL, value INT DEFAULT NULL, PRIMARY KEY(reason)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_pickers (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', participant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', user_id BIGINT UNSIGNED DEFAULT NULL, type INT NOT NULL, INDEX IDX_7304B7CE9D1C3019 (participant_id), INDEX IDX_7304B7CEA76ED395 (user_id), UNIQUE INDEX UNIQ_7304B7CE9D1C30198CDE5729 (participant_id, type), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE domain_event_records (uuid VARCHAR(255) NOT NULL, actor_id BIGINT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, payload JSON NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8A5A010210DAF24A (actor_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F8D93D649 FOREIGN KEY (user) REFERENCES users (steam_id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F57698A6A FOREIGN KEY (role) REFERENCES roles (name)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_7169709271F7E88B FOREIGN KEY (event_id) REFERENCES events (uuid)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_71697092A76ED395 FOREIGN KEY (user_id) REFERENCES users (steam_id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AFE54D947 FOREIGN KEY (group_id) REFERENCES steam_groups (id)');
        $this->addSql('ALTER TABLE steam_group_members ADD CONSTRAINT FK_D04982DFFE54D947 FOREIGN KEY (group_id) REFERENCES steam_groups (id)');
        $this->addSql('ALTER TABLE steam_group_members ADD CONSTRAINT FK_D04982DF1370AACA FOREIGN KEY (user_steam_id) REFERENCES users (steam_id)');
        $this->addSql('ALTER TABLE earned_rewards ADD CONSTRAINT FK_E7A06B29D1C3019 FOREIGN KEY (participant_id) REFERENCES participants (uuid)');
        $this->addSql('ALTER TABLE earned_rewards ADD CONSTRAINT FK_E7A06B2F54A307A FOREIGN KEY (pick_id) REFERENCES event_picks (uuid)');
        $this->addSql('ALTER TABLE earned_rewards ADD CONSTRAINT FK_E7A06B2E466ACA1 FOREIGN KEY (reward_id) REFERENCES rewards (reason)');
        $this->addSql('ALTER TABLE event_picks ADD CONSTRAINT FK_3D51F9E48874902 FOREIGN KEY (picker_id) REFERENCES event_pickers (uuid)');
        $this->addSql('ALTER TABLE event_picks ADD CONSTRAINT FK_3D51F9E4E48FD905 FOREIGN KEY (game_id) REFERENCES games (complex_id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A8874902 FOREIGN KEY (picker_id) REFERENCES event_pickers (uuid)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (steam_id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AE4A93D08 FOREIGN KEY (referenced_game_id) REFERENCES games (complex_id)');
        $this->addSql('ALTER TABLE event_pickers ADD CONSTRAINT FK_7304B7CE9D1C3019 FOREIGN KEY (participant_id) REFERENCES participants (uuid)');
        $this->addSql('ALTER TABLE event_pickers ADD CONSTRAINT FK_7304B7CEA76ED395 FOREIGN KEY (user_id) REFERENCES users (steam_id)');
        $this->addSql('ALTER TABLE domain_event_records ADD CONSTRAINT FK_8A5A010210DAF24A FOREIGN KEY (actor_id) REFERENCES users (steam_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F57698A6A');
        $this->addSql('ALTER TABLE event_picks DROP FOREIGN KEY FK_3D51F9E4E48FD905');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AE4A93D08');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F8D93D649');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_71697092A76ED395');
        $this->addSql('ALTER TABLE steam_group_members DROP FOREIGN KEY FK_D04982DF1370AACA');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE event_pickers DROP FOREIGN KEY FK_7304B7CEA76ED395');
        $this->addSql('ALTER TABLE domain_event_records DROP FOREIGN KEY FK_8A5A010210DAF24A');
        $this->addSql('ALTER TABLE earned_rewards DROP FOREIGN KEY FK_E7A06B29D1C3019');
        $this->addSql('ALTER TABLE event_pickers DROP FOREIGN KEY FK_7304B7CE9D1C3019');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_7169709271F7E88B');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AFE54D947');
        $this->addSql('ALTER TABLE steam_group_members DROP FOREIGN KEY FK_D04982DFFE54D947');
        $this->addSql('ALTER TABLE earned_rewards DROP FOREIGN KEY FK_E7A06B2F54A307A');
        $this->addSql('ALTER TABLE earned_rewards DROP FOREIGN KEY FK_E7A06B2E466ACA1');
        $this->addSql('ALTER TABLE event_picks DROP FOREIGN KEY FK_3D51F9E48874902');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A8874902');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE steam_groups');
        $this->addSql('DROP TABLE steam_group_members');
        $this->addSql('DROP TABLE earned_rewards');
        $this->addSql('DROP TABLE content_blocks');
        $this->addSql('DROP TABLE event_picks');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE rewards');
        $this->addSql('DROP TABLE event_pickers');
        $this->addSql('DROP TABLE domain_event_records');
    }
}
