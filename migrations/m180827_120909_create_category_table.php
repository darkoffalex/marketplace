<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category`.
 */
class m180827_120909_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'parent_category_id' => $this->integer()->defaultValue(0),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer(),
            'priority' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        $this->insert('category',[
            'parent_category_id' => 0,
            'name' => 'Transport',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('category',[
            'parent_category_id' => 0,
            'name' => 'Realty',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('category',[
            'parent_category_id' => 0,
            'name' => 'Services',
            'status_id' => \app\helpers\Constants::STATUS_ENABLED,
            'priority' => 3,
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
        $this->dropTable('category');
    }
}
