<?php

namespace app\models\base;

use Yii;

use app\models\Marketplace;

/**
 * This is the base model class for table "rate".
 *
 * @property int $id
 * @property int $marketplace_id
 * @property string $name
 * @property int $price
 * @property int $single_payment
 * @property int $days_count
 * @property int $first_free_days
 * @property int $admin_post_mode
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property Marketplace $marketplace
 */
class RateBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['marketplace_id', 'price', 'single_payment', 'days_count', 'first_free_days', 'admin_post_mode', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['marketplace_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marketplace::className(), 'targetAttribute' => ['marketplace_id' => 'id']],
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
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'single_payment' => Yii::t('app', 'Single Payment'),
            'days_count' => Yii::t('app', 'Days Count'),
            'first_free_days' => Yii::t('app', 'First Free Days'),
            'admin_post_mode' => Yii::t('app', 'Admin Post Mode'),
            'status_id' => Yii::t('app', 'Status ID'),
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
}
