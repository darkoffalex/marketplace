<?php

namespace app\models\base;

use Yii;

use app\models\MoneyTransaction;
use app\models\User;

/**
 * This is the base model class for table "used_web_payment_type".
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $cdd_pan_mask
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property string $last_operation_key
 * @property int $system_type
 * @property int $recurring
 *
 * @property MoneyTransaction[] $moneyTransactions
 * @property User $user
 */
class UsedWebPaymentTypeBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'used_web_payment_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_by_id', 'updated_by_id', 'system_type', 'recurring'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'cdd_pan_mask', 'last_operation_key'], 'string', 'max' => 255],
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
            'code' => Yii::t('app', 'Code'),
            'cdd_pan_mask' => Yii::t('app', 'Cdd Pan Mask'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'last_operation_key' => Yii::t('app', 'Last Operation Key'),
            'system_type' => Yii::t('app', 'System Type'),
            'recurring' => Yii::t('app', 'Recurring'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyTransactions()
    {
        return $this->hasMany(MoneyTransaction::className(), ['web_payment_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
