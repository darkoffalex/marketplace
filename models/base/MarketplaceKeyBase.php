<?php

namespace app\models\base;

use Yii;

use app\models\Marketplace;
use app\models\User;

/**
 * This is the base model class for table "marketplace_key".
 *
 * @property int $id
 * @property int $marketplace_id
 * @property string $code
 * @property int $used_by_id
 * @property string $used_at
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property Marketplace $marketplace
 * @property User $usedBy
 */
class MarketplaceKeyBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marketplace_key';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['marketplace_id', 'used_by_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['used_at', 'created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['marketplace_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marketplace::className(), 'targetAttribute' => ['marketplace_id' => 'id']],
            [['used_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['used_by_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'marketplace_id' => Yii::t('app', 'Marketplace ID'),
            'code' => Yii::t('app', 'Code'),
            'used_by_id' => Yii::t('app', 'Used By ID'),
            'used_at' => Yii::t('app', 'Used At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
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
    public function getUsedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'used_by_id']);
    }
}
