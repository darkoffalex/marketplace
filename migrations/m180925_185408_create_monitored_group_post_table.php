<?php

use yii\db\Migration;

/**
 * Handles the creation of table `monitored_group_post`.
 * Has foreign keys to the tables:
 *
 * - `monitored_group`
 */
class m180925_185408_create_monitored_group_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('monitored_group_post', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(),
            'facebook_id' => $this->string(),
            'text' => $this->text(),
            'attachments_count' => $this->integer(),
            'reactions_count' => $this->integer(),
            'comments_count' => $this->integer(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `group_id`
        $this->createIndex(
            'idx-monitored_group_post-group_id',
            'monitored_group_post',
            'group_id'
        );

        // add foreign key for table `monitored_group`
        $this->addForeignKey(
            'fk-monitored_group_post-group_id',
            'monitored_group_post',
            'group_id',
            'monitored_group',
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
        // drops foreign key for table `monitored_group`
        $this->dropForeignKey(
            'fk-monitored_group_post-group_id',
            'monitored_group_post'
        );

        // drops index for column `group_id`
        $this->dropIndex(
            'idx-monitored_group_post-group_id',
            'monitored_group_post'
        );

        $this->dropTable('monitored_group_post');
    }
}
