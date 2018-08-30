<?php

use yii\db\Migration;

/**
 * Handles adding facebook to table `user`.
 */
class m180830_155038_add_facebook_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'facebook_id', $this->string());
        $this->addColumn('user', 'facebook_token', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'facebook_id');
        $this->dropColumn('user', 'facebook_token');
    }
}
