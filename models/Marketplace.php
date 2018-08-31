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
        $rules[] = ['domain_alias','unique'];
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
