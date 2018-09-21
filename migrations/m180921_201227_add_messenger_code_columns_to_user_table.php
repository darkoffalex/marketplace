<?php

use yii\db\Migration;

/**
 * Handles adding messenger_code to table `user`.
 */
class m180921_201227_add_messenger_code_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'fb_msg_sub_code', $this->string());
        $this->addColumn('user', 'fb_msg_uid', $this->string());
        $this->addColumn('user', 'fb_msg_types', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'fb_msg_sub_cod');
        $this->dropColumn('user', 'fb_msg_uid');
        $this->dropColumn('user', 'fb_msg_types');
    }
}
