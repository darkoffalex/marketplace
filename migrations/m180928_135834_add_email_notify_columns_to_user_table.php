<?php

use yii\db\Migration;

/**
 * Handles adding email_notify to table `user`.
 */
class m180928_135834_add_email_notify_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'email_notify_enabled', $this->integer()->defaultValue(0));
        $this->addColumn('user', 'email_notify_types', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'email_notify_enabled');
        $this->dropColumn('user', 'email_notify_types');
    }
}
