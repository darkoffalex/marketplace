<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money_transaction".
 */
class MoneyTransaction extends \app\models\base\MoneyTransactionBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    /**
     * Является ли транзация поступлением на счет
     * @param $accountId
     * @return bool
     */
    public function isIncomeFor($accountId)
    {
        return $this->to_account_id == $accountId;
    }

    /**
     * Является ли транзакция выводом со счета
     * @param $accountId
     * @return bool
     */
    public function isOutgoFor($accountId)
    {
        return $this->from_account_id == $accountId;
    }
}
