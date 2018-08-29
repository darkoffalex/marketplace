<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cv".
 */
class Cv extends \app\models\base\CvBase
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
