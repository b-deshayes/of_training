<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219195256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_status_histories (id SERIAL NOT NULL, order_id INT DEFAULT NULL, status_id INT NOT NULL, change_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_25725AFE8D9F6D38 ON order_status_histories (order_id)');
        $this->addSql('CREATE INDEX IDX_25725AFE6BF700BD ON order_status_histories (status_id)');
        $this->addSql('COMMENT ON COLUMN order_status_histories.change_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_statuses (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA08FCA05E237E06 ON order_statuses (name)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, status_id INT NOT NULL, reference VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEEAEA34913 ON orders (reference)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6BF700BD ON orders (status_id)');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN orders.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE package_locations (id SERIAL NOT NULL, package_id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BD088771F44CABFF ON package_locations (package_id)');
        $this->addSql('COMMENT ON COLUMN package_locations.timestamp IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE packages (id SERIAL NOT NULL, order_id INT NOT NULL, tracking_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BB5C0A78D9F6D38 ON packages (order_id)');
        $this->addSql('ALTER TABLE order_status_histories ADD CONSTRAINT FK_25725AFE8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_status_histories ADD CONSTRAINT FK_25725AFE6BF700BD FOREIGN KEY (status_id) REFERENCES order_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6BF700BD FOREIGN KEY (status_id) REFERENCES order_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_locations ADD CONSTRAINT FK_BD088771F44CABFF FOREIGN KEY (package_id) REFERENCES packages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE packages ADD CONSTRAINT FK_9BB5C0A78D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_status_histories DROP CONSTRAINT FK_25725AFE8D9F6D38');
        $this->addSql('ALTER TABLE order_status_histories DROP CONSTRAINT FK_25725AFE6BF700BD');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6BF700BD');
        $this->addSql('ALTER TABLE package_locations DROP CONSTRAINT FK_BD088771F44CABFF');
        $this->addSql('ALTER TABLE packages DROP CONSTRAINT FK_9BB5C0A78D9F6D38');
        $this->addSql('DROP TABLE order_status_histories');
        $this->addSql('DROP TABLE order_statuses');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE package_locations');
        $this->addSql('DROP TABLE packages');
    }
}
