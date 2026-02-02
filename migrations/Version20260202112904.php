<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202112904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chat_member (id INT AUTO_INCREMENT NOT NULL, left_at DATETIME DEFAULT NULL, chat_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1738CD591A9A7125 (chat_id), INDEX IDX_1738CD59A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE friend_request (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, responded_at DATETIME DEFAULT NULL, sender_user_id INT NOT NULL, receiver_user_id INT NOT NULL, INDEX IDX_F284D942A98155E (sender_user_id), INDEX IDX_F284D94DA57E237 (receiver_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, chat_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B6BD307F1A9A7125 (chat_id), INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, lat NUMERIC(10, 8) DEFAULT NULL, lng NUMERIC(11, 8) DEFAULT NULL, online TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_block (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, blocker_user_id INT NOT NULL, blocked_user_id INT NOT NULL, INDEX IDX_61D96C7AC1668098 (blocker_user_id), INDEX IDX_61D96C7A1EBCBB63 (blocked_user_id), UNIQUE INDEX unique_block (blocker_user_id, blocked_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_follow (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, follower_user_id INT NOT NULL, followed_user_id INT NOT NULL, INDEX IDX_D665F4D70FC2906 (follower_user_id), INDEX IDX_D665F4DAF2612FD (followed_user_id), UNIQUE INDEX unique_follow (follower_user_id, followed_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chat_member ADD CONSTRAINT FK_1738CD591A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE chat_member ADD CONSTRAINT FK_1738CD59A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D942A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94DA57E237 FOREIGN KEY (receiver_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_block ADD CONSTRAINT FK_61D96C7AC1668098 FOREIGN KEY (blocker_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_block ADD CONSTRAINT FK_61D96C7A1EBCBB63 FOREIGN KEY (blocked_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4D70FC2906 FOREIGN KEY (follower_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4DAF2612FD FOREIGN KEY (followed_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_member DROP FOREIGN KEY FK_1738CD591A9A7125');
        $this->addSql('ALTER TABLE chat_member DROP FOREIGN KEY FK_1738CD59A76ED395');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D942A98155E');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94DA57E237');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1A9A7125');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE user_block DROP FOREIGN KEY FK_61D96C7AC1668098');
        $this->addSql('ALTER TABLE user_block DROP FOREIGN KEY FK_61D96C7A1EBCBB63');
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_D665F4D70FC2906');
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_D665F4DAF2612FD');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE chat_member');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_block');
        $this->addSql('DROP TABLE user_follow');
    }
}
