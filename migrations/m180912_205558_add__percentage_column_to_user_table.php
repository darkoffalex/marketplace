<?php

use yii\db\Migration;

/**
 * Handles adding _percentage to table `user`.
 */
class m180912_205558_add__percentage_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'ag_income_percentage', $this->integer()->defaultValue(75));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'ag_income_percentage');
    }
}
