<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261005142947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed initial of roles (readonly/admin) and permissions default.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO tbl_roles (name, shortname, description) VALUES
            ('Somente Leitura', 'readonly', 'Apenas vizualixação das informações.'),
            ('Administrador', 'admin', 'Todos os acesso.')
        SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO tbl_permissions (slug, description) VALUES
            ('users.view',   'Visualizar usuários'),
            ('users.create', 'Criar usuários'),
            ('users.edit',   'Editar usuários'),
            ('users.delete', 'Excluir usuários'),
            ('role.view',    'Visualizar papéis de usuários'),
            ('role.create',  'Criar papéis de usuários'),
            ('role.edit',    'Editar papéis de usuários'),
            ('role.delete',  'Excluir papéis de usuários'),
            ('role.assignment', 'Atribuição de papéis para usuários'),
            ('role.assign', 'Atribuir papéi para usuário')
        SQL);
        // complete com o restante das permissions que estavam em Setup::initialDataInsert()

        $this->addSql(<<<'SQL'
            INSERT INTO tbl_role_permissions (role_id, permission_id)
            SELECT r.id, p.id
            FROM tbl_roles r
            CROSS JOIN tbl_permissions p
            WHERE r.shortname = 'admin'
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE rp FROM tbl_role_permissions rp
            INNER JOIN tbl_roles r ON r.id = rp.role_id
            WHERE r.shortname IN ('admin', 'readonly')
        SQL);

        $this->addSql("DELETE FROM tbl_permissions");
        $this->addSql("DELETE FROM tbl_roles WHERE shortname IN ('admin', 'readonly')");
    }
}
