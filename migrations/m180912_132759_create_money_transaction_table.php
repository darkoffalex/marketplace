<?php

use yii\db\Migration;

/**
 * Handles the creation of table `money_transaction`.
 * Has foreign keys to the tables:
 *
 * - `money_account`
 * - `money_account`
 */
class m180912_132759_create_money_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('money_transaction', [
            'id' => $this->primaryKey(),
            'payment_side_id' => $this->string(),
            'from_account_id' => $this->integer(),
            'to_account_id' => $this->integer(),
            'amount' => $this->integer(),
            'note' => $this->text(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::PAYMENT_STATUS_NEW),
            'type_id' => $this->integer()->defaultValue(\app\helpers\Constants::PAYMENT_INTERNAL_INITIATED),
            'description' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `from_account_id`
        $this->createIndex(
            'idx-money_transaction-from_account_id',
            'money_transaction',
            'from_account_id'
        );

        // add foreign key for table `money_account`
        $this->addForeignKey(
            'fk-money_transaction-from_account_id',
            'money_transaction',
            'from_account_id',
            'money_account',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `to_account_id`
        $this->createIndex(
            'idx-money_transaction-to_account_id',
            'money_transaction',
            'to_account_id'
        );

        // add foreign key for table `money_account`
        $this->addForeignKey(
            'fk-money_transaction-to_account_id',
            'money_transaction',
            'to_account_id',
            'money_account',
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
        // drops foreign key for table `money_account`
        $this->dropForeignKey(
            'fk-money_transaction-from_account_id',
            'money_transaction'
        );

        // drops index for column `from_account_id`
        $this->dropIndex(
            'idx-money_transaction-from_account_id',
            'money_transaction'
        );

        // drops foreign key for table `money_account`
        $this->dropForeignKey(
            'fk-money_transaction-to_account_id',
            'money_transaction'
        );

        // drops index for column `to_account_id`
        $this->dropIndex(
            'idx-money_transaction-to_account_id',
            'money_transaction'
        );

        $this->dropTable('money_transaction');
    }
}
