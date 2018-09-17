<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payout_proposal_image`.
 * Has foreign keys to the tables:
 *
 * - `payout_proposal`
 */
class m180917_145456_create_payout_proposal_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('payout_proposal_image', [
            'id' => $this->primaryKey(),
            'proposal_id' => $this->integer(),
            'priority' => $this->integer(),
            'title' => $this->string(),
            'description' => $this->string(),
            'filename' => $this->string(),
            'size' => $this->integer(),
            'crop_settings' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_ENABLED),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `proposal_id`
        $this->createIndex(
            'idx-payout_proposal_image-proposal_id',
            'payout_proposal_image',
            'proposal_id'
        );

        // add foreign key for table `payout_proposal`
        $this->addForeignKey(
            'fk-payout_proposal_image-proposal_id',
            'payout_proposal_image',
            'proposal_id',
            'payout_proposal',
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
        // drops foreign key for table `payout_proposal`
        $this->dropForeignKey(
            'fk-payout_proposal_image-proposal_id',
            'payout_proposal_image'
        );

        // drops index for column `proposal_id`
        $this->dropIndex(
            'idx-payout_proposal_image-proposal_id',
            'payout_proposal_image'
        );

        $this->dropTable('payout_proposal_image');
    }
}
