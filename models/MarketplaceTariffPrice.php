<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "marketplace_tariff_price".
 */
class MarketplaceTariffPrice extends \app\models\base\MarketplaceTariffPriceBase
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
