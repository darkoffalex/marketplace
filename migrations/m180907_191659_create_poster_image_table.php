<?php

use yii\db\Migration;

/**
 * Handles the creation of table `poster_image`.
 * Has foreign keys to the tables:
 *
 * - `poster`
 */
class m180907_191659_create_poster_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('poster_image', [
            'id' => $this->primaryKey(),
            'poster_id' => $this->integer(),
            'main_pic' => $this->integer()->defaultValue(0),
            'title' => $this->string(),
            'description' => $this->string(),
            'filename' => $this->string(),
            'crop_settings' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_TEMPORARY),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        // creates index for column `poster_id`
        $this->createIndex(
            'idx-poster_image-poster_id',
            'poster_image',
            'poster_id'
        );

        // add foreign key for table `poster`
        $this->addForeignKey(
            'fk-poster_image-poster_id',
            'poster_image',
            'poster_id',
            'poster',
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
        // drops foreign key for table `poster`
        $this->dropForeignKey(
            'fk-poster_image-poster_id',
            'poster_image'
        );

        // drops index for column `poster_id`
        $this->dropIndex(
            'idx-poster_image-poster_id',
            'poster_image'
        );

        $this->dropTable('poster_image');
    }
}
