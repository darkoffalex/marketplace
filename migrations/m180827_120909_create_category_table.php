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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('category');
    }
}
