<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class <className> extends AbstractMigration
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
<up>
    }

    public function down(Schema $schema): void
    {
<down>
    }
}
