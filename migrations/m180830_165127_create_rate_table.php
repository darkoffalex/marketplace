<?php

use yii\db\Migration;

/**
 * Handles the creation of table `rate`.
 * Has foreign keys to the tables:
 *
 * - `marketplace`
 */
class m180830_165127_create_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rate', [
            'id' => $this->primaryKey(),
            'marketplace_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'price' => $this->integer(),
            'single_payment' => $this->integer(),
            'days_count' => $this->integer(),
            'first_free_days' => $this->integer(),
            'admin_post_mode' => $this->integer(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DISABLED),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `marketplace_id`
        $this->createIndex(
            'idx-rate-marketplace_id',
            'rate',
            'marketplace_id'
        );

        // add foreign key for table `marketplace`
        $this->addForeignKey(
            'fk-rate-marketplace_id',
            'rate',
            'marketplace_id',
            'marketplace',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `marketplace`
        $this->dropForeignKey(
            'fk-rate-marketplace_id',
            'rate'
        );

        // drops index for column `marketplace_id`
        $this->dropIndex(
            'idx-rate-marketplace_id',
            'rate'
        );

        $this->dropTable('rate');
    }
}
