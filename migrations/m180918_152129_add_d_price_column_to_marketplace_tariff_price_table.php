<?php

use yii\db\Migration;

/**
 * Handles adding d_price to table `marketplace_tariff_price`.
 */
class m180918_152129_add_d_price_column_to_marketplace_tariff_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('marketplace_tariff_price', 'discounted_price', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('marketplace_tariff_price', 'discounted_price');
    }
}
