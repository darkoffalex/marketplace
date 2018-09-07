<?php

namespace app\models;

use app\helpers\Constants;
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

    /**
     * Имеет ли объявление новые изменения
     * @return bool
     */
    public function hasNewChanges()
    {
        $editable = [
            $this->title,
            $this->description,
            $this->phone,
            $this->whats_app
        ];

        $approved = [
            $this->title_approved,
            $this->description_approved,
            $this->phone_approved,
            $this->whats_app_approved
        ];

        return $editable != $approved || $this->getPosterImages()->where(['status_id' => Constants::STATUS_TEMPORARY])->count() > 0;
    }
}
