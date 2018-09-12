<?php

use yii\db\Migration;

/**
 * Handles the creation of table `money_account`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180912_132119_create_money_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('money_account', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'account_type_id' => $this->integer()->defaultValue(\app\helpers\Constants::SYSTEM_INCOME_ACCOUNT),
            'amount' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-money_account-user_id',
            'money_account',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-money_account-user_id',
            'money_account',
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
            'fk-money_account-user_id',
            'money_account'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-money_account-user_id',
            'money_account'
        );

        $this->dropTable('money_account');
    }
}
