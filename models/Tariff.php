<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "tariff".
 */
class Tariff extends \app\models\base\TariffBase
{
    /**
     * @var UploadedFile
     */
    public $image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['image'], 'file', 'extensions' => ['jpg','png','gif', 'jpeg'], 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10];
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
