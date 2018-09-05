<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poster".
 */
class Poster extends \app\models\base\PosterBase
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
