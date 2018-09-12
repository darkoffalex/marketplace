<?php

use yii\db\Migration;

/**
 * Handles adding web_payment_type to table `money_transaction`.
 * Has foreign keys to the tables:
 *
 * - `used_web_payment_type`
 */
class m180912_135909_add_web_payment_type_column_to_money_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('money_transaction', 'web_payment_type_id', $this->integer());

        // creates index for column `web_payment_type_id`
        $this->createIndex(
            'idx-money_transaction-web_payment_type_id',
            'money_transaction',
            'web_payment_type_id'
        );

        // add foreign key for table `used_web_payment_type`
        $this->addForeignKey(
            'fk-money_transaction-web_payment_type_id',
            'money_transaction',
            'web_payment_type_id',
            'used_web_payment_type',
            'id',
            'SET NULL',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `used_web_payment_type`
        $this->dropForeignKey(
            'fk-money_transaction-web_payment_type_id',
            'money_transaction'
        );

        // drops index for column `web_payment_type_id`
        $this->dropIndex(
            'idx-money_transaction-web_payment_type_id',
            'money_transaction'
        );

        $this->dropColumn('money_transaction', 'web_payment_type_id');
    }
}
