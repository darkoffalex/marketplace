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
            'value_text' => 'EAAEUdaWy7vQBAP2qfm62OXLyZBD8zFA2Yo81ulgsNenhhTy2Ap2paPNgATZASbGHmqIUIu42y3QiZA6l4pBm3TK5GtgAV9ux2lnvWiNJCPYPIhRPKHlOpvpYAdjZCMWDj0js3eJ8iZAZC1QfZCFMkYDRjksiFITkWikJqkPjqfwPgZDZD',
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
