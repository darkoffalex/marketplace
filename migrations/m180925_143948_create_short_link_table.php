<?php

use yii\db\Migration;

/**
 * Handles the creation of table `short_link`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180925_143948_create_short_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('short_link', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->defaultValue(0),
            'user_id' => $this->integer(),
            'phone' => $this->string(),
            'original_link' => $this->text(),
            'type_id' => $this->integer()->defaultValue(\app\helpers\Constants::SHORT_LINK_REGULAR),
            'message' => $this->text(),
            'status_id' => $this->integer(),
            'key' => $this->string(),
            'custom_key' => $this->integer()->defaultValue((int)false),
            'clicks' => $this->integer()->defaultValue(0),
            'title' => $this->string(),
            'description' => $this->string(),
            'image_file' => $this->string(),
            'site_name' => $this->string(),
            'created_at' => $this->dateTime(),
            'update_at' => $this->dateTime(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-short_link-user_id',
            'short_link',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-short_link-user_id',
            'short_link',
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
            'fk-short_link-user_id',
            'short_link'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-short_link-user_id',
            'short_link'
        );

        $this->dropTable('short_link');
    }
}
