<?php

use yii\db\Migration;

/**
 * Handles adding main to table `tariff`.
 */
class m180918_151659_add_main_column_to_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tariff', 'is_main', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tariff', 'is_main');
    }
}
