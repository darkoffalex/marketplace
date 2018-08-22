<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 */
class Settings extends \app\models\base\SettingsBase
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
