<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dictionary_notification`.
 * Has foreign keys to the tables:
 *
 * - `dictionary`
 * - `monitored_group_post`
 * - `monitored_group_post_comment`
 */
class m180925_205838_create_dictionary_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dictionary_notification', [
            'id' => $this->primaryKey(),
            'dictionary_id' => $this->integer(),
            'post_id' => $this->integer(),
            'comment_id' => $this->integer(),
            'word' => $this->string(),
            'pattern' => $this->string(),
            'excerpt' => $this->text(),
            'seen' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);

        // creates index for column `dictionary_id`
        $this->createIndex(
            'idx-dictionary_notification-dictionary_id',
            'dictionary_notification',
            'dictionary_id'
        );

        // add foreign key for table `dictionary`
        $this->addForeignKey(
            'fk-dictionary_notification-dictionary_id',
            'dictionary_notification',
            'dictionary_id',
            'dictionary',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `post_id`
        $this->createIndex(
            'idx-dictionary_notification-post_id',
            'dictionary_notification',
            'post_id',
            'NO ACTION'
        );

        // add foreign key for table `monitored_group_post`
        $this->addForeignKey(
            'fk-dictionary_notification-post_id',
            'dictionary_notification',
            'post_id',
            'monitored_group_post',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `comment_id`
        $this->createIndex(
            'idx-dictionary_notification-comment_id',
            'dictionary_notification',
            'comment_id'
        );

        // add foreign key for table `monitored_group_post_comment`
        $this->addForeignKey(
            'fk-dictionary_notification-comment_id',
            'dictionary_notification',
            'comment_id',
            'monitored_group_post_comment',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `dictionary`
        $this->dropForeignKey(
            'fk-dictionary_notification-dictionary_id',
            'dictionary_notification'
        );

        // drops index for column `dictionary_id`
        $this->dropIndex(
            'idx-dictionary_notification-dictionary_id',
            'dictionary_notification'
        );

        // drops foreign key for table `monitored_group_post`
        $this->dropForeignKey(
            'fk-dictionary_notification-post_id',
            'dictionary_notification'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-dictionary_notification-post_id',
            'dictionary_notification'
        );

        // drops foreign key for table `monitored_group_post_comment`
        $this->dropForeignKey(
            'fk-dictionary_notification-comment_id',
            'dictionary_notification'
        );

        // drops index for column `comment_id`
        $this->dropIndex(
            'idx-dictionary_notification-comment_id',
            'dictionary_notification'
        );

        $this->dropTable('dictionary_notification');
    }
}
