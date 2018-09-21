<?php

use yii\db\Migration;

/**
 * Handles adding stats to table `user`.
 */
class m180921_202717_add_stats_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'total_agr_income', $this->integer());
        $this->addColumn('user', 'total_mgr_income', $this->integer());
        $this->addColumn('user', 'total_usr_outgo', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'total_agr_income');
        $this->dropColumn('user', 'total_mgr_income');
        $this->dropColumn('user', 'total_usr_outgo');
    }
}
