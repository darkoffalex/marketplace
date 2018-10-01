<?php

use yii\db\Migration;

/**
 * Handles the creation of table `system_notification`.
 */
class m180928_133336_create_system_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('system_notification', [
            'id' => $this->primaryKey(),
            'recipient_fb_id' => $this->string(),
            'recipient_email' => $this->string(),
            'message_fb' => $this->text(),
            'message_email' => $this->text(),
            'subject_email' => $this->string(),
            'sent' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('system_notification');
    }
}
