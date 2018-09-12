<?php

namespace app\models\base;

use Yii;

use app\models\User;
use app\models\MoneyTransaction;

/**
 * This is the base model class for table "money_account".
 *
 * @property int $id
 * @property int $user_id
 * @property int $account_type_id
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property User $user
 * @property MoneyTransaction[] $moneyTransactions
 * @property MoneyTransaction[] $moneyTransactions0
 */
class MoneyAccountBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'account_type_id', 'amount', 'created_by_id', 'updated_by_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'account_type_id' => Yii::t('app', 'Account Type ID'),
            'amount' => Yii::t('app', 'Amount'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
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
    public function getMoneyTransactions()
    {
        return $this->hasMany(MoneyTransaction::className(), ['from_account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyTransactions0()
    {
        return $this->hasMany(MoneyTransaction::className(), ['to_account_id' => 'id']);
    }
}
