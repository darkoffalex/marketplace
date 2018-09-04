<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "marketplace".
 */
class Marketplace extends \app\models\base\MarketplaceBase
{
    /**
     * @var UploadedFile
     */
    public $header_image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['domain_alias','unique'];
        $rules[] = [['header_image'], 'file', 'extensions' => ['jpg','png','gif', 'jpeg'], 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['header_image'] = Yii::t('app','Header image');
        return $labels;
    }
}
