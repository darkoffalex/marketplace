<?php

use yii\db\Migration;

/**
 * Handles adding approved to table `poster`.
 */
class m180907_151420_add_approved_columns_to_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('poster', 'approved_by_ga', $this->integer()->defaultValue(0));
        $this->addColumn('poster', 'approved_by_sa', $this->integer()->defaultValue(0));
        $this->addColumn('poster', 'published', $this->integer()->defaultValue(0));
        $this->addColumn('poster', 'refuse_reason', $this->text()->defaultValue(null));
        $this->addColumn('marketplace','trusted',$this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('marketplace', 'trusted');
        $this->dropColumn('poster', 'approved_by_ga');
        $this->dropColumn('poster', 'approved_by_sa');
        $this->dropColumn('poster', 'published');
        $this->dropColumn('poster', 'refuse_reason');
    }
}
