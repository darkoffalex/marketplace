<?php

namespace app\models;

use app\helpers\Constants;
use app\helpers\Help;
use Yii;
use yii\helpers\ArrayHelper;

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
     * Получить активную цену (если есть скидочная - использовать ее)
     * @return int
     */
    public function getActivePrice()
    {
        return !empty($this->discounted_price) ? $this->discounted_price : $this->price;
    }

    /**
     * Получить наименование тарифа с деталями (цена, период)
     * @param bool $useComplexMarkup
     * @param bool $description
     * @param string $currencySign
     * @return mixed
     */
    public function getNameWithDetails($useComplexMarkup = false, $description = false, $currencySign = '₽')
    {
        $price = Help::toPrice($this->getActivePrice());
        $basePrice = Help::toPrice($this->price);

        $interval = $this->tariff->getIntervalName();

        if($useComplexMarkup){
            $priceLabel = $price != $basePrice ? "<strike>{$basePrice} {$currencySign}</strike> {$price} {$currencySign}" : "{$price} {$currencySign}";
        }else{
            $priceLabel = "{$price} {$currencySign}";
        }

        $descriptionText = !empty($description) ? "<br><small>{$this->tariff->description}</small>" : "";

        $templates = [
            Constants::TARIFF_SUB_TYPE_REGULAR => "{$this->tariff->name} [{$priceLabel} / $interval]{$descriptionText}",
            Constants::TARIFF_SUB_TYPE_ADMIN_POST => "{$this->tariff->name} [{$priceLabel}]{$descriptionText}"
        ];

        return ArrayHelper::getValue($templates,$this->tariff->special_type,Yii::t('app','Unknown tariff type'));
    }
}
