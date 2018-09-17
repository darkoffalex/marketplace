<?php

namespace app\models\base;

use Yii;

use app\models\Cv;
use app\models\Marketplace;
use app\models\MarketplaceKey;
use app\models\MoneyAccount;
use app\models\PayoutProposal;
use app\models\Poster;
use app\models\UsedWebPaymentType;

/**
 * This is the base model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $name
 * @property string $avatar_url
 * @property string $avatar_filename
 * @property int $role_id
 * @property int $status_id
 * @property string $preferred_language
 * @property string $last_login_at
 * @property string $last_online_at
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property string $facebook_id
 * @property string $facebook_token
 * @property int $ag_income_percentage
 *
 * @property Cv[] $cvs
 * @property Marketplace[] $marketplaces
 * @property MarketplaceKey[] $marketplaceKeys
 * @property MoneyAccount[] $moneyAccounts
 * @property PayoutProposal[] $payoutProposals
 * @property Poster[] $posters
 * @property UsedWebPaymentType[] $usedWebPaymentTypes
 */
class UserBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['role_id', 'status_id', 'created_by_id', 'updated_by_id', 'ag_income_percentage'], 'integer'],
            [['last_login_at', 'last_online_at', 'created_at', 'updated_at'], 'safe'],
            [['facebook_token'], 'string'],
            [['username', 'password_hash', 'auth_key', 'name', 'avatar_url', 'avatar_filename', 'preferred_language', 'facebook_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'name' => Yii::t('app', 'Name'),
            'avatar_url' => Yii::t('app', 'Avatar Url'),
            'avatar_filename' => Yii::t('app', 'Avatar Filename'),
            'role_id' => Yii::t('app', 'Role ID'),
            'status_id' => Yii::t('app', 'Status ID'),
            'preferred_language' => Yii::t('app', 'Preferred Language'),
            'last_login_at' => Yii::t('app', 'Last Login At'),
            'last_online_at' => Yii::t('app', 'Last Online At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'facebook_token' => Yii::t('app', 'Facebook Token'),
            'ag_income_percentage' => Yii::t('app', 'Ag Income Percentage'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCvs()
    {
        return $this->hasMany(Cv::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaces()
    {
        return $this->hasMany(Marketplace::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaceKeys()
    {
        return $this->hasMany(MarketplaceKey::className(), ['used_by_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyAccounts()
    {
        return $this->hasMany(MoneyAccount::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayoutProposals()
    {
        return $this->hasMany(PayoutProposal::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosters()
    {
        return $this->hasMany(Poster::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsedWebPaymentTypes()
    {
        return $this->hasMany(UsedWebPaymentType::className(), ['user_id' => 'id']);
    }
}
