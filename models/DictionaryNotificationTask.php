<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictionary_notification_task".
 */
class DictionaryNotificationTask extends \app\models\base\DictionaryNotificationTaskBase
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
