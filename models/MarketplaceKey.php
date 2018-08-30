<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "marketplace_key".
 */
class MarketplaceKey extends \app\models\base\MarketplaceKeyBase
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
