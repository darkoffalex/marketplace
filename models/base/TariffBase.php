<?php

namespace app\models\base;

use Yii;

use app\models\MarketplaceTariffPrice;

/**
 * This is the base model class for table "tariff".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image_filename
 * @property string $image_crop_settings
 * @property int $base_price
 * @property int $discounted_price
 * @property int $period_unit_type
 * @property int $period_amount
 * @property int $period_free_amount
 * @property int $subscription
 * @property int $special_type
 * @property int $show_on_page
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $is_main
 *
 * @property MarketplaceTariffPrice[] $marketplaceTariffPrices
 */
class TariffBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['base_price', 'discounted_price', 'period_unit_type', 'period_amount', 'period_free_amount', 'subscription', 'special_type', 'show_on_page', 'created_by_id', 'updated_by_id', 'is_main'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'image_filename', 'image_crop_settings'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'image_filename' => Yii::t('app', 'Image Filename'),
            'image_crop_settings' => Yii::t('app', 'Image Crop Settings'),
            'base_price' => Yii::t('app', 'Base Price'),
            'discounted_price' => Yii::t('app', 'Discounted Price'),
            'period_unit_type' => Yii::t('app', 'Period Unit Type'),
            'period_amount' => Yii::t('app', 'Period Amount'),
            'period_free_amount' => Yii::t('app', 'Period Free Amount'),
            'subscription' => Yii::t('app', 'Subscription'),
            'special_type' => Yii::t('app', 'Special Type'),
            'show_on_page' => Yii::t('app', 'Show On Page'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'is_main' => Yii::t('app', 'Is Main'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaceTariffPrices()
    {
        return $this->hasMany(MarketplaceTariffPrice::className(), ['tariff_id' => 'id']);
    }
}
