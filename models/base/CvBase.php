<?php

namespace app\models\base;

use Yii;

use app\models\Country;
use app\models\User;
use app\models\Marketplace;

/**
 * This is the base model class for table "cv".
 *
 * @property int $id
 * @property string $name
 * @property int $is_member
 * @property int $user_id
 * @property int $country_id
 * @property string $group_name
 * @property string $group_description
 * @property string $group_geo
 * @property int $group_popularity
 * @property string $group_thematics
 * @property string $group_url
 * @property string $group_admin_profile
 * @property string $email
 * @property string $phone
 * @property int $has_viber
 * @property int $has_whatsapp
 * @property string $timezone
 * @property string $comfortable_call_time
 * @property int $status_id
 * @property string $discard_reason
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property Country $country
 * @property User $user
 * @property Marketplace[] $marketplaces
 */
class CvBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_member', 'user_id', 'country_id', 'group_popularity', 'has_viber', 'has_whatsapp', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['group_description', 'discard_reason'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'group_name', 'group_geo', 'group_thematics', 'group_url', 'group_admin_profile', 'email', 'phone', 'timezone', 'comfortable_call_time'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'is_member' => Yii::t('app', 'Is Member'),
            'user_id' => Yii::t('app', 'User ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'group_name' => Yii::t('app', 'Group Name'),
            'group_description' => Yii::t('app', 'Group Description'),
            'group_geo' => Yii::t('app', 'Group Geo'),
            'group_popularity' => Yii::t('app', 'Group Popularity'),
            'group_thematics' => Yii::t('app', 'Group Thematics'),
            'group_url' => Yii::t('app', 'Group Url'),
            'group_admin_profile' => Yii::t('app', 'Group Admin Profile'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'has_viber' => Yii::t('app', 'Has Viber'),
            'has_whatsapp' => Yii::t('app', 'Has Whatsapp'),
            'timezone' => Yii::t('app', 'Timezone'),
            'comfortable_call_time' => Yii::t('app', 'Comfortable Call Time'),
            'status_id' => Yii::t('app', 'Status ID'),
            'discard_reason' => Yii::t('app', 'Discard Reason'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
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
    public function getMarketplaces()
    {
        return $this->hasMany(Marketplace::className(), ['cv_id' => 'id']);
    }
}
