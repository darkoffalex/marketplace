<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poster_image".
 */
class PosterImage extends \app\models\base\PosterImageBase
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
