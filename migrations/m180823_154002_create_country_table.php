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

        $this->insert('country',[
            'name' => 'Russia',
            'domain_alias' => 'russia',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 1,
            'flag_filename' => 'ru.svg',
            'clicks' => 0,
            'description' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('country',[
            'name' => 'Turkey',
            'domain_alias' => 'turkey',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 2,
            'flag_filename' => 'tr.svg',
            'clicks' => 0,
            'description' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('country',[
            'name' => 'Germany',
            'domain_alias' => 'germany',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 3,
            'flag_filename' => 'de.svg',
            'clicks' => 0,
            'description' => '',
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
        $this->dropTable('country');
    }
}
