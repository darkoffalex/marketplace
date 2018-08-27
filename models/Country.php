<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country".
 */
class Country extends \app\models\base\CountryBase
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
