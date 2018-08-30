<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rate".
 */
class Rate extends \app\models\base\RateBase
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
