<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m180821_211715_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value_text' => $this->text(),
            'value_numeric' => $this->integer(),
        ]);

        $this->insert('settings',[
            'name' => 'fb_auth_client_id',
            'value_text' => '303970500407028',
        ]);

        $this->insert('settings',[
            'name' => 'fb_auth_app_secret',
            'value_text' => '25483980ab16864505fd22076f9876b5',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('settings');
    }
}
