<?php

use yii\db\Migration;

/**
 * Handles the creation of table `marketplace`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `country`
 */
class m180829_151122_create_marketplace_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('marketplace', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'country_id' => $this->integer(),
            'geo' => $this->string(),
            'name' => $this->string()->notNull(),
            'domain_alias' => $this->string()->notNull(),
            'selling_rules' => $this->text(),
            'display_empty_categories' => $this->integer()->defaultValue(0),
            'header_image_filename' => $this->string(),
            'header_image_crop_settings' => $this->string(),
            'pm_theme_description' => $this->text(),
            'admin_phone_wa' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::STATUS_DISABLED),

            'group_description' => $this->text(),
            'group_popularity' => $this->integer(),
            'group_thematics' => $this->string(),
            'group_url' => $this->string(),
            'group_admin_profile' => $this->string(),

            'timezone' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer()
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-marketplace-user_id',
            'marketplace',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-marketplace-user_id',
            'marketplace',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `country_id`
        $this->createIndex(
            'idx-marketplace-country_id',
            'marketplace',
            'country_id'
        );

        // add foreign key for table `country`
        $this->addForeignKey(
            'fk-marketplace-country_id',
            'marketplace',
            'country_id',
            'country',
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
            'fk-marketplace-user_id',
            'marketplace'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-marketplace-user_id',
            'marketplace'
        );

        // drops foreign key for table `country`
        $this->dropForeignKey(
            'fk-marketplace-country_id',
            'marketplace'
        );

        // drops index for column `country_id`
        $this->dropIndex(
            'idx-marketplace-country_id',
            'marketplace'
        );

        $this->dropTable('marketplace');
    }
}
