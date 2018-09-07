<?php

namespace app\models\base;

use Yii;

use app\models\Country;
use app\models\User;
use app\models\MarketplaceKey;
use app\models\MarketplaceTariffPrice;
use app\models\Poster;

/**
 * This is the base model class for table "marketplace".
 *
 * @property int $id
 * @property int $user_id
 * @property int $country_id
 * @property string $geo
 * @property string $name
 * @property string $domain_alias
 * @property string $selling_rules
 * @property int $display_empty_categories
 * @property string $header_image_filename
 * @property string $header_image_crop_settings
 * @property string $pm_theme_description
 * @property string $admin_phone_wa
 * @property int $status_id
 * @property string $group_description
 * @property int $group_popularity
 * @property string $group_thematics
 * @property string $group_url
 * @property string $group_admin_profile
 * @property string $timezone
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $trusted
 *
 * @property Country $country
 * @property User $user
 * @property MarketplaceKey[] $marketplaceKeys
 * @property MarketplaceTariffPrice[] $marketplaceTariffPrices
 * @property Poster[] $posters
 */
class MarketplaceBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marketplace';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'country_id', 'display_empty_categories', 'status_id', 'group_popularity', 'created_by_id', 'updated_by_id', 'trusted'], 'integer'],
            [['name', 'domain_alias'], 'required'],
            [['selling_rules', 'pm_theme_description', 'group_description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['geo', 'name', 'domain_alias', 'header_image_filename', 'header_image_crop_settings', 'admin_phone_wa', 'group_thematics', 'group_url', 'group_admin_profile', 'timezone'], 'string', 'max' => 255],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'geo' => Yii::t('app', 'Geo'),
            'name' => Yii::t('app', 'Name'),
            'domain_alias' => Yii::t('app', 'Domain Alias'),
            'selling_rules' => Yii::t('app', 'Selling Rules'),
            'display_empty_categories' => Yii::t('app', 'Display Empty Categories'),
            'header_image_filename' => Yii::t('app', 'Header Image Filename'),
            'header_image_crop_settings' => Yii::t('app', 'Header Image Crop Settings'),
            'pm_theme_description' => Yii::t('app', 'Pm Theme Description'),
            'admin_phone_wa' => Yii::t('app', 'Admin Phone Wa'),
            'status_id' => Yii::t('app', 'Status ID'),
            'group_description' => Yii::t('app', 'Group Description'),
            'group_popularity' => Yii::t('app', 'Group Popularity'),
            'group_thematics' => Yii::t('app', 'Group Thematics'),
            'group_url' => Yii::t('app', 'Group Url'),
            'group_admin_profile' => Yii::t('app', 'Group Admin Profile'),
            'timezone' => Yii::t('app', 'Timezone'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'trusted' => Yii::t('app', 'Trusted'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaceKeys()
    {
        return $this->hasMany(MarketplaceKey::className(), ['marketplace_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaceTariffPrices()
    {
        return $this->hasMany(MarketplaceTariffPrice::className(), ['marketplace_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosters()
    {
        return $this->hasMany(Poster::className(), ['marketplace_id' => 'id']);
    }
}
