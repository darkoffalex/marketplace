<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dictionary_subscriber`.
 * Has foreign keys to the tables:
 *
 * - `dictionary`
 */
class m180925_190138_create_dictionary_subscriber_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dictionary_subscriber', [
            'id' => $this->primaryKey(),
            'dictionary_id' => $this->integer(),
            'facebook_id' => $this->string(),
            'name' => $this->string(),
            'avatar_url' => $this->string(),
            'excluded_groups' => $this->text(),
            'created_at' => $this->dateTime(),
        ]);

        // creates index for column `dictionary_id`
        $this->createIndex(
            'idx-dictionary_subscriber-dictionary_id',
            'dictionary_subscriber',
            'dictionary_id'
        );

        // add foreign key for table `dictionary`
        $this->addForeignKey(
            'fk-dictionary_subscriber-dictionary_id',
            'dictionary_subscriber',
            'dictionary_id',
            'dictionary',
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
        // drops foreign key for table `dictionary`
        $this->dropForeignKey(
            'fk-dictionary_subscriber-dictionary_id',
            'dictionary_subscriber'
        );

        // drops index for column `dictionary_id`
        $this->dropIndex(
            'idx-dictionary_subscriber-dictionary_id',
            'dictionary_subscriber'
        );

        $this->dropTable('dictionary_subscriber');
    }
}
