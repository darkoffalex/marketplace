<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money_account".
 */
class MoneyAccount extends \app\models\base\MoneyAccountBase
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
}
