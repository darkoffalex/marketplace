<?php

use yii\db\Migration;

/**
 * Handles the creation of table `monitored_group_post_comment`.
 * Has foreign keys to the tables:
 *
 * - `monitored_group_post`
 */
class m180925_185610_create_monitored_group_post_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('monitored_group_post_comment', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer(),
            'facebook_id' => $this->string(),
            'text' => $this->text(),
            'parent_id' => $this->integer(),
            'attachments_count' => $this->integer(),
            'reactions_count' => $this->integer(),
            'comments_count' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `post_id`
        $this->createIndex(
            'idx-monitored_group_post_comment-post_id',
            'monitored_group_post_comment',
            'post_id'
        );

        // add foreign key for table `monitored_group_post`
        $this->addForeignKey(
            'fk-monitored_group_post_comment-post_id',
            'monitored_group_post_comment',
            'post_id',
            'monitored_group_post',
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
        // drops foreign key for table `monitored_group_post`
        $this->dropForeignKey(
            'fk-monitored_group_post_comment-post_id',
            'monitored_group_post_comment'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-monitored_group_post_comment-post_id',
            'monitored_group_post_comment'
        );

        $this->dropTable('monitored_group_post_comment');
    }
}
