<?php

use yii\db\Migration;

/**
 * Handles adding admin_post_time to table `poster`.
 */
class m180919_155419_add_admin_post_time_columns_to_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('poster', 'admin_post_time', $this->dateTime());
        $this->addColumn('poster', 'admin_post_time_approve_status', $this->integer()->defaultValue(\app\helpers\Constants::ADMIN_POST_TIME_AT_REVIEW));
        $this->addColumn('poster', 'admin_post_disapprove_reason', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('poster', 'admin_post_time');
        $this->dropColumn('poster', 'admin_post_time_approve_status');
        $this->dropColumn('poster', 'admin_post_disapprove_reason');
    }
}
