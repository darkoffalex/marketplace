<?php

use yii\db\Migration;

/**
 * Handles adding operation_key to table `used_web_payment_type`.
 */
class m180913_213709_add_operation_key_columns_to_used_web_payment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('used_web_payment_type', 'last_operation_key', $this->string());
        $this->addColumn('used_web_payment_type', 'system_type', $this->integer()->defaultValue(\app\helpers\Constants::PAYMENT_SYSTEM_YM));
        $this->addColumn('used_web_payment_type', 'recurring', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('used_web_payment_type', 'last_operation_key');
        $this->dropColumn('used_web_payment_type', 'system_type');
        $this->dropColumn('used_web_payment_type', 'recurring');
    }
}
