<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monitored_group_dictionary".
 */
class MonitoredGroupDictionary extends \app\models\base\MonitoredGroupDictionaryBase
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
