<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dictionary`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180925_185822_create_dictionary_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dictionary', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'words' => $this->text(),
            'key' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DISABLED),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-dictionary-user_id',
            'dictionary',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-dictionary-user_id',
            'dictionary',
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
            'fk-dictionary-user_id',
            'dictionary'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-dictionary-user_id',
            'dictionary'
        );

        $this->dropTable('dictionary');
    }
}
