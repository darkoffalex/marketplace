<?php

use yii\db\Migration;

/**
 * Handles the creation of table `poster`.
 * Has foreign keys to the tables:
 *
 * - `marketplace`
 * - `category`
 * - `rate`
 * - `user`
 */
class m180905_195613_create_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('poster', [
            'id' => $this->primaryKey(),
            'marketplace_id' => $this->integer(),
            'category_id' => $this->integer(),
            'rate_id' => $this->integer(),
            'user_id' => $this->integer(),
            'country_id' => $this->integer(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DELETED),

            'title' => $this->string(),
            'description' => $this->text(),
            'phone' => $this->string(),
            'whats_app' => $this->string(),

            'title_approved' => $this->string(),
            'description_approved' => $this->text(),
            'phone_approved' => $this->string(),
            'whats_app_approved' => $this->string(),

            'admin_post_text' => $this->text(),
            'admin_post_image_filename' => $this->string(),

            'paid_at' => $this->dateTime(),
            'free_period_expired' => $this->integer(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer()
        ]);

        // creates index for column `marketplace_id`
        $this->createIndex(
            'idx-poster-marketplace_id',
            'poster',
            'marketplace_id'
        );

        // add foreign key for table `marketplace`
        $this->addForeignKey(
            'fk-poster-marketplace_id',
            'poster',
            'marketplace_id',
            'marketplace',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-poster-category_id',
            'poster',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-poster-category_id',
            'poster',
            'category_id',
            'category',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `rate_id`
        $this->createIndex(
            'idx-poster-rate_id',
            'poster',
            'rate_id'
        );

        // add foreign key for table `rate`
        $this->addForeignKey(
            'fk-poster-rate_id',
            'poster',
            'rate_id',
            'rate',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-poster-user_id',
            'poster',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-poster-user_id',
            'poster',
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
        // drops foreign key for table `marketplace`
        $this->dropForeignKey(
            'fk-poster-marketplace_id',
            'poster'
        );

        // drops index for column `marketplace_id`
        $this->dropIndex(
            'idx-poster-marketplace_id',
            'poster'
        );

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-poster-category_id',
            'poster'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-poster-category_id',
            'poster'
        );

        // drops foreign key for table `rate`
        $this->dropForeignKey(
            'fk-poster-rate_id',
            'poster'
        );

        // drops index for column `rate_id`
        $this->dropIndex(
            'idx-poster-rate_id',
            'poster'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-poster-user_id',
            'poster'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-poster-user_id',
            'poster'
        );

        $this->dropTable('poster');
    }
}
