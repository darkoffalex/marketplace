<?php

use yii\db\Migration;

/**
 * Handles the creation of table `monitored_group`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180925_183952_create_monitored_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('monitored_group', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'facebook_id' => $this->string(),
            'name' => $this->string()->notNull(),
            'privacy' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DISABLED),
            'sync_done_last_time' => $this->datetime(),
            'sync_in_progress' => $this->integer(),
            'sync_since' => $this->datetime(),
            'sync_to' => $this->datetime(),
            'parsing_errors_log' => $this->text(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-monitored_group-user_id',
            'monitored_group',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-monitored_group-user_id',
            'monitored_group',
            'user_id',
            'user',
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
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-monitored_group-user_id',
            'monitored_group'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-monitored_group-user_id',
            'monitored_group'
        );

        $this->dropTable('monitored_group');
    }
}
