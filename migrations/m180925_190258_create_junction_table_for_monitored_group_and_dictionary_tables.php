<?php

use yii\db\Migration;

/**
 * Handles the creation of table `monitored_group_dictionary`.
 * Has foreign keys to the tables:
 *
 * - `monitored_group`
 * - `dictionary`
 */
class m180925_190258_create_junction_table_for_monitored_group_and_dictionary_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('monitored_group_dictionary', [
            'monitored_group_id' => $this->integer(),
            'dictionary_id' => $this->integer(),
            'PRIMARY KEY(monitored_group_id, dictionary_id)',
        ]);

        // creates index for column `monitored_group_id`
        $this->createIndex(
            'idx-monitored_group_dictionary-monitored_group_id',
            'monitored_group_dictionary',
            'monitored_group_id'
        );

        // add foreign key for table `monitored_group`
        $this->addForeignKey(
            'fk-monitored_group_dictionary-monitored_group_id',
            'monitored_group_dictionary',
            'monitored_group_id',
            'monitored_group',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `dictionary_id`
        $this->createIndex(
            'idx-monitored_group_dictionary-dictionary_id',
            'monitored_group_dictionary',
            'dictionary_id'
        );

        // add foreign key for table `dictionary`
        $this->addForeignKey(
            'fk-monitored_group_dictionary-dictionary_id',
            'monitored_group_dictionary',
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
        // drops foreign key for table `monitored_group`
        $this->dropForeignKey(
            'fk-monitored_group_dictionary-monitored_group_id',
            'monitored_group_dictionary'
        );

        // drops index for column `monitored_group_id`
        $this->dropIndex(
            'idx-monitored_group_dictionary-monitored_group_id',
            'monitored_group_dictionary'
        );

        // drops foreign key for table `dictionary`
        $this->dropForeignKey(
            'fk-monitored_group_dictionary-dictionary_id',
            'monitored_group_dictionary'
        );

        // drops index for column `dictionary_id`
        $this->dropIndex(
            'idx-monitored_group_dictionary-dictionary_id',
            'monitored_group_dictionary'
        );

        $this->dropTable('monitored_group_dictionary');
    }
}
