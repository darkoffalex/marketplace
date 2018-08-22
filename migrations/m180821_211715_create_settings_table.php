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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('settings');
    }
}
