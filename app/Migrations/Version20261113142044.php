<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261113142044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add collum is_active (bool) on tbl_roles.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE tbl_roles
              ADD is_active TINYINT(1) DEFAULT 1 NOT NULL AFTER description,
              ADD INDEX idx_roles_is_active (is_active)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE tbl_roles
              DROP INDEX idx_roles_is_active,
              DROP COLUMN is_active
        SQL);
    }
}
