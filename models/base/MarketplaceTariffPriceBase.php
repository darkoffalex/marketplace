<?php

namespace app\models\base;

use Yii;

use app\models\Marketplace;
use app\models\Tariff;
use app\models\Poster;

/**
 * This is the base model class for table "marketplace_tariff_price".
 *
 * @property int $id
 * @property int $tariff_id
 * @property int $marketplace_id
 * @property int $price
 * @property int $discounted_price
 *
 * @property Marketplace $marketplace
 * @property Tariff $tariff
 * @property Poster[] $posters
 */
class MarketplaceTariffPriceBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marketplace_tariff_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tariff_id', 'marketplace_id', 'price', 'discounted_price'], 'integer'],
            [['marketplace_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marketplace::className(), 'targetAttribute' => ['marketplace_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tariff_id' => Yii::t('app', 'Tariff ID'),
            'marketplace_id' => Yii::t('app', 'Marketplace ID'),
            'price' => Yii::t('app', 'Price'),
            'discounted_price' => Yii::t('app', 'Discounted Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplace()
    {
        return $this->hasOne(Marketplace::className(), ['id' => 'marketplace_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(Tariff::className(), ['id' => 'tariff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosters()
    {
        return $this->hasMany(Poster::className(), ['marketplace_tariff_id' => 'id']);
    }
}
