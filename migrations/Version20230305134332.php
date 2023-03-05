<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230305134332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE alias (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    source VARCHAR(255) NOT NULL,
    domain VARCHAR(255) NOT NULL,
    destinations CLOB NOT NULL /*(DC2Type:json)*/,
    enabled BOOLEAN DEFAULT 1 NOT NULL,
    created_at DATETIME NOT NULL /*(DC2Type:datetime_immutable)*/,
    updated_at DATETIME DEFAULT NULL /*(DC2Type:datetime_immutable)*/
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE alias');
    }
}
