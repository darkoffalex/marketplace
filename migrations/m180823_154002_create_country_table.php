<?php

use yii\db\Migration;

/**
 * Handles the creation of table `country`.
 */
class m180823_154002_create_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('country', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'domain_alias' => $this->string()->notNull(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DISABLED),
            'priority' => $this->integer(),
            'flag_filename' => $this->string(),
            'clicks' => $this->integer(),
            'description' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('country');
    }
}
