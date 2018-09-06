<?php

namespace app\models;

use app\helpers\Help;
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
     * @var array настройки тарифов
     */
    public $tariffs = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['domain_alias','unique'];
        $rules[] = ['tariffs', 'safe'];
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

    /**
     * Получить настройку цены конкретного тарифа у маркетплейса
     * @param $tariffId
     * @param bool $create
     * @return MarketplaceTariffPrice
     */
    public function getTariffPrice($tariffId, $create = false)
    {
        foreach ($this->marketplaceTariffPrices as $price){
            if($price->tariff_id == $tariffId){
                return $price;
            }
        }

        if($create){
            $price = new MarketplaceTariffPrice();
            $price->tariff_id = $tariffId;
            $price->marketplace_id = $this->id;
            return $price;
        }

        return null;
    }

    /**
     * Синхронизировать тарифы
     * @param $tariffs
     */
    public function syncTariffs($tariffs)
    {
        if(empty($tariffs)){
            return;
        }

        foreach ($tariffs as $id => $settings) {
            if(!empty($settings['enabled'])){
                $t = $this->getTariffPrice($id,true);
                $t->price = Help::toCents($settings['price']);
                $t->save();
            }else{
                $t = $this->getTariffPrice($id,false);
                if(!empty($t)){
                    $t->delete();
                }
            }
        }
    }
}
