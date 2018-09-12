<?php

use yii\db\Migration;

/**
 * Handles the creation of table `used_web_payment_type`.
 * Has foreign keys to the tables:
 *
 * - `user_id`
 */
class m180912_134949_create_used_web_payment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('used_web_payment_type', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'code' => $this->string(),
            'cdd_pan_mask' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-used_web_payment_type-user_id',
            'used_web_payment_type',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-used_web_payment_type-user_id',
            'used_web_payment_type',
            'user_id',
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
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-used_web_payment_type-user_id',
            'used_web_payment_type'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-used_web_payment_type-user_id',
            'used_web_payment_type'
        );

        $this->dropTable('used_web_payment_type');
    }
}
