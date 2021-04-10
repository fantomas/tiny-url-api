<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210410120405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add version for optimistic lock when updating the Url';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE url ADD version INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE url DROP version');
    }
}
