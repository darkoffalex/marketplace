<?php

use yii\db\Migration;
use app\helpers\Constants;

/**
 * Handles the creation of table `tariff`.
 */
class m180906_143726_create_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tariff', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'image_filename' => $this->string(),
            'image_crop_settings' => $this->string(),
            'base_price' => $this->integer(),
            'discounted_price' => $this->integer(),
            'period_unit_type' => $this->integer(Constants::PERIOD_DAYS),
            'period_amount' => $this->integer()->defaultValue(0),
            'period_free_amount' => $this->integer()->defaultValue(0),
            'subscription' => $this->integer()->defaultValue(0),
            'special_type' => $this->integer()->defaultValue(Constants::TARIFF_SUB_TYPE_REGULAR),
            'show_on_page' => $this->integer()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer(),
        ]);

        $this->insert('tariff',[
            'name' => 'Business',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'base_price' => 200000,
            'discounted_price' => 150000,
            'period_unit_type' => Constants::PERIOD_MONTHS,
            'period_amount' => 1,
            'period_free_amount' => 0,
            'subscription' => (int)true,
            'special_type' => Constants::TARIFF_SUB_TYPE_REGULAR,
            'show_on_page' => (int)true,
            'created_at' => date('Y-m-d H:i:s',time()),
            'updated_at' => date('Y-m-d H:i:s',time()),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('tariff',[
            'name' => 'Premium',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'base_price' => 300000,
            'discounted_price' => 250000,
            'period_unit_type' => Constants::PERIOD_WEEKS,
            'period_amount' => 6,
            'period_free_amount' => 0,
            'subscription' => (int)true,
            'special_type' => Constants::TARIFF_SUB_TYPE_REGULAR,
            'show_on_page' => (int)true,
            'created_at' => date('Y-m-d H:i:s',time()),
            'updated_at' => date('Y-m-d H:i:s',time()),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);

        $this->insert('tariff',[
            'name' => 'Admin\'s Post',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'base_price' => 500000,
            'discounted_price' => 400000,
            'period_unit_type' => Constants::PERIOD_DAYS,
            'period_amount' => 1,
            'period_free_amount' => 0,
            'subscription' => (int)false,
            'special_type' => Constants::TARIFF_SUB_TYPE_ADMIN_POST,
            'show_on_page' => (int)true,
            'created_at' => date('Y-m-d H:i:s',time()),
            'updated_at' => date('Y-m-d H:i:s',time()),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tariff');
    }
}
