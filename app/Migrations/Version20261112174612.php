<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261112174612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add FKs on tbl_role_permissions (role_id -> tbl_roles, permission_id -> tbl_permissions).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE tbl_role_permissions
              ADD CONSTRAINT fk_role_permissions_role
                FOREIGN KEY (role_id) REFERENCES tbl_roles (id)
                ON UPDATE CASCADE ON DELETE CASCADE,
              ADD CONSTRAINT fk_role_permissions_permission
                FOREIGN KEY (permission_id) REFERENCES tbl_permissions (id)
                ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE tbl_role_permissions
              DROP FOREIGN KEY fk_role_permissions_role,
              DROP FOREIGN KEY fk_role_permissions_permission
        SQL);
    }
}