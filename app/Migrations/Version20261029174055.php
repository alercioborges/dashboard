<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261029174055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates a table to track attempts for rate limiting (login/forgot password).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE tbl_rate_limit_attempts (
              id INT AUTO_INCREMENT NOT NULL,
              identifier VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              action VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              created_at DATETIME NOT NULL,
              INDEX idx_identifier_action_created (identifier, action, created_at),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `tbl_rate_limit_attempts`');
    }
}
