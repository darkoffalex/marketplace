<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dictionary_notification_task`.
 * Has foreign keys to the tables:
 *
 * - `dictionary_notification`
 * - `dictionary_subscriber`
 */
class m180925_212031_create_dictionary_notification_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dictionary_notification_task', [
            'id' => $this->primaryKey(),
            'notification_id' => $this->integer(),
            'subscriber_id' => $this->integer(),
            'sent' => $this->integer()->defaultValue(0)
        ]);

        // creates index for column `notification_id`
        $this->createIndex(
            'idx-dictionary_notification_task-notification_id',
            'dictionary_notification_task',
            'notification_id'
        );

        // add foreign key for table `dictionary_notification`
        $this->addForeignKey(
            'fk-dictionary_notification_task-notification_id',
            'dictionary_notification_task',
            'notification_id',
            'dictionary_notification',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `subscriber_id`
        $this->createIndex(
            'idx-dictionary_notification_task-subscriber_id',
            'dictionary_notification_task',
            'subscriber_id'
        );

        // add foreign key for table `dictionary_subscriber`
        $this->addForeignKey(
            'fk-dictionary_notification_task-subscriber_id',
            'dictionary_notification_task',
            'subscriber_id',
            'dictionary_subscriber',
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
        // drops foreign key for table `dictionary_notification`
        $this->dropForeignKey(
            'fk-dictionary_notification_task-notification_id',
            'dictionary_notification_task'
        );

        // drops index for column `notification_id`
        $this->dropIndex(
            'idx-dictionary_notification_task-notification_id',
            'dictionary_notification_task'
        );

        // drops foreign key for table `dictionary_subscriber`
        $this->dropForeignKey(
            'fk-dictionary_notification_task-subscriber_id',
            'dictionary_notification_task'
        );

        // drops index for column `subscriber_id`
        $this->dropIndex(
            'idx-dictionary_notification_task-subscriber_id',
            'dictionary_notification_task'
        );

        $this->dropTable('dictionary_notification_task');
    }
}
