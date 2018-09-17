<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payout_proposal`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `money_transaction`
 */
class m180917_142935_create_payout_proposal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('payout_proposal', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'transaction_id' => $this->integer(),
            'description' => $this->text(),
            'amount' => $this->integer()->defaultValue(0),
            'status_id' => $this->integer(),
            'discard_reason' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-payout_proposal-user_id',
            'payout_proposal',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-payout_proposal-user_id',
            'payout_proposal',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `transaction_id`
        $this->createIndex(
            'idx-payout_proposal-transaction_id',
            'payout_proposal',
            'transaction_id'
        );

        // add foreign key for table `money_transaction`
        $this->addForeignKey(
            'fk-payout_proposal-transaction_id',
            'payout_proposal',
            'transaction_id',
            'money_transaction',
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
            'fk-payout_proposal-user_id',
            'payout_proposal'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-payout_proposal-user_id',
            'payout_proposal'
        );

        // drops foreign key for table `money_transaction`
        $this->dropForeignKey(
            'fk-payout_proposal-transaction_id',
            'payout_proposal'
        );

        // drops index for column `transaction_id`
        $this->dropIndex(
            'idx-payout_proposal-transaction_id',
            'payout_proposal'
        );

        $this->dropTable('payout_proposal');
    }
}
