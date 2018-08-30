<?php

use yii\db\Migration;

/**
 * Handles the creation of table `marketplace_key`.
 * Has foreign keys to the tables:
 *
 * - `marketplace`
 * - `user`
 */
class m180830_170258_create_marketplace_key_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('marketplace_key', [
            'id' => $this->primaryKey(),
            'marketplace_id' => $this->integer(),
            'code' => $this->string(),
            'used_by_id' => $this->integer(),
            'used_at' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `marketplace_id`
        $this->createIndex(
            'idx-marketplace_key-marketplace_id',
            'marketplace_key',
            'marketplace_id'
        );

        // add foreign key for table `marketplace`
        $this->addForeignKey(
            'fk-marketplace_key-marketplace_id',
            'marketplace_key',
            'marketplace_id',
            'marketplace',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `used_by_id`
        $this->createIndex(
            'idx-marketplace_key-used_by_id',
            'marketplace_key',
            'used_by_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-marketplace_key-used_by_id',
            'marketplace_key',
            'used_by_id',
            'user',
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
            'fk-marketplace_key-marketplace_id',
            'marketplace_key'
        );

        // drops index for column `marketplace_id`
        $this->dropIndex(
            'idx-marketplace_key-marketplace_id',
            'marketplace_key'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-marketplace_key-used_by_id',
            'marketplace_key'
        );

        // drops index for column `used_by_id`
        $this->dropIndex(
            'idx-marketplace_key-used_by_id',
            'marketplace_key'
        );

        $this->dropTable('marketplace_key');
    }
}
