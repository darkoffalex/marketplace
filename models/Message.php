<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message".
 */
class Message extends \app\models\base\MessageBase
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
