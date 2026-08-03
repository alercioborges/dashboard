<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803193135 extends AbstractMigration
{
    public function isTransactional(): bool
    {
        return false;
    }

    public function preUp(Schema $schema): void
    {
        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
    }

    public function postUp(Schema $schema): void
    {
        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function preDown(Schema $schema): void
    {
        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
    }

    public function postDown(Schema $schema): void
    {
        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function getDescription(): string
    {
        return '<description>';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_password_resets (
              id INT AUTO_INCREMENT NOT NULL,
              user_id INT NOT NULL,
              token_hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              expires_at DATETIME NOT NULL,
              used_at DATETIME DEFAULT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
              UNIQUE INDEX uk_token_hash (token_hash),
              INDEX idx_user_id (user_id),
              INDEX idx_expires_at (expires_at),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tbl_password_resets
            ADD
              CONSTRAINT `fk_password_resets_user` FOREIGN KEY (user_id) REFERENCES tbl_users (id) ON
            UPDATE
              NO ACTION ON DELETE CASCADE
        SQL);
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_permissions (
              id INT AUTO_INCREMENT NOT NULL,
              slug VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`,
              UNIQUE INDEX slug (slug),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_role_permissions (
              role_id INT NOT NULL,
              permission_id INT NOT NULL,
              INDEX permission_id (permission_id),
              PRIMARY KEY (role_id, permission_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_roles (
              id INT AUTO_INCREMENT NOT NULL,
              name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              shortname VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
              updated_at DATETIME DEFAULT NULL,
              UNIQUE INDEX name (name),
              UNIQUE INDEX shortname (shortname),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_user_remember_tokens (
              id INT AUTO_INCREMENT NOT NULL,
              user_id INT NOT NULL,
              token_hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              expires_at DATETIME NOT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
              UNIQUE INDEX token_hash (token_hash),
              INDEX idx_user_id (user_id),
              INDEX idx_expires_at (expires_at),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tbl_user_remember_tokens
            ADD
              CONSTRAINT `fk_users_remember_tokens` FOREIGN KEY (user_id) REFERENCES tbl_users (id) ON
            UPDATE
              NO ACTION ON DELETE NO ACTION
        SQL);
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_users (
              id INT AUTO_INCREMENT NOT NULL,
              firstname VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              lastname VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              role_id INT NOT NULL,
              is_active TINYINT DEFAULT 1 NOT NULL,
              last_login DATETIME DEFAULT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
              updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              UNIQUE INDEX email (email),
              INDEX idx_role_id (role_id),
              INDEX idx_is_active (is_active),
              INDEX idx_created_at (created_at),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tbl_users
            ADD
              CONSTRAINT `fk_users_role` FOREIGN KEY (role_id) REFERENCES tbl_roles (id) ON
            UPDATE
              CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_password_resets`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_permissions`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_role_permissions`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_roles`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_user_remember_tokens`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL84Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL84Platform'."
        );

        $this->addSql('DROP TABLE `tbl_users`');
    }
}
