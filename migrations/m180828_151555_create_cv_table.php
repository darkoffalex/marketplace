<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cv`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `country`
 */
class m180828_151555_create_cv_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cv', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'is_member' => $this->integer(),
            'user_id' => $this->integer(),
            'country_id' => $this->integer(),

            'group_name' => $this->string(),
            'group_description' => $this->text(),
            'group_geo' => $this->string(),
            'group_popularity' => $this->integer(),
            'group_thematics' => $this->string(),
            'group_url' => $this->string(),
            'group_admin_profile' => $this->string(),

            'email' => $this->string(),
            'phone' => $this->string(),
            'has_viber' => $this->integer(),
            'has_whatsapp' => $this->integer(),
            'timezone' => $this->string(),
            'comfortable_call_time' => $this->string(),
            'status_id' => $this->integer()->defaultValue(\app\helpers\Constants::CV_STATUS_NEW),
            'discard_reason' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer()
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-cv-user_id',
            'cv',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-cv-user_id',
            'cv',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `country_id`
        $this->createIndex(
            'idx-cv-country_id',
            'cv',
            'country_id'
        );

        // add foreign key for table `country`
        $this->addForeignKey(
            'fk-cv-country_id',
            'cv',
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
            'fk-cv-user_id',
            'cv'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-cv-user_id',
            'cv'
        );

        // drops foreign key for table `country`
        $this->dropForeignKey(
            'fk-cv-country_id',
            'cv'
        );

        // drops index for column `country_id`
        $this->dropIndex(
            'idx-cv-country_id',
            'cv'
        );

        $this->dropTable('cv');
    }
}
