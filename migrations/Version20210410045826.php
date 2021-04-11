<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210410045826 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add Url table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE url_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE url (id INT NOT NULL, short_uri VARCHAR(50) NOT NULL, orig_url VARCHAR(255) NOT NULL, visits INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX short_uri_idx ON url (short_uri)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP SEQUENCE url_id_seq CASCADE');
        $this->addSql('DROP TABLE url');
    }
}
