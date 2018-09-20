<?php

namespace app\models;

use app\helpers\Help;
use Yii;
use yii\helpers\Url;
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
     * Получить ссылку на основную страницу маркеплейса
     * @param bool $scheme
     * @return string
     */
    public function getLink($scheme = false)
    {
        return Url::to([
            '/marketplace/index',
            'subSubSubDomain' => $this->domain_alias,
            'subSubDomain' => $this->country->domain_alias
        ], $scheme);
    }

    /**
     * Получить ссылку на категорию внутри маркетплейса
     * @param $category
     * @param bool $scheme
     * @return null|string
     */
    public function getCategoryLink(&$category, $scheme = false)
    {
        /* @var $category Category */
        if(empty($category->id)){
            return null;
        }

        return Url::to([
            '/marketplace/category',
            'subSubSubDomain' => $this->domain_alias,
            'subSubDomain' => $this->country->domain_alias,
            'id' => $category->id,
            'title' => Help::slug( Yii::t('app',$category->name))
        ], $scheme);
    }
}
