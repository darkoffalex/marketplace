<?php

namespace app\models;

use app\helpers\Help;
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

    /**
     * Получить наименование тарифа с деталями (цена, период)
     * @param string $currencySign
     * @return string
     */
    public function getNameWithDetails($currencySign = '₽')
    {
        $price = Help::toPrice($this->price);
        $interval = $this->tariff->getIntervalName();
        return "{$this->tariff->name} [{$price} {$currencySign} / $interval]";
    }
}
