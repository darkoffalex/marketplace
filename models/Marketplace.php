<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "marketplace".
 */
class Marketplace extends \app\models\base\MarketplaceBase
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
