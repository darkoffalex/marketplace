<?php

use yii\db\Migration;

/**
 * Handles the creation of table `language`.
 */
class m180821_172356_create_language_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('language', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'self_name' => $this->string()->notNull(),
            'prefix' => $this->string(3)->notNull(),
            'is_default' => $this->integer(),
            'priority' => $this->integer(),
            'status_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer()
        ]);

        $this->insert('language',[
            'name' => 'Russian',
            'self_name' => 'Русский',
            'prefix' => 'ru',
            'is_default' => (int)true,
            'priority' => 1,
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('language',[
            'name' => 'English',
            'self_name' => 'English',
            'prefix' => 'en',
            'is_default' => (int)false,
            'priority' => 2,
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('language');
    }
}
