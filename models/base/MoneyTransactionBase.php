<?php

namespace app\models\base;

use Yii;

use app\models\MoneyAccount;
use app\models\UsedWebPaymentType;
use app\models\PayoutProposal;

/**
 * This is the base model class for table "money_transaction".
 *
 * @property int $id
 * @property string $payment_side_id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property int $amount
 * @property string $note
 * @property int $status_id
 * @property int $type_id
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $web_payment_type_id
 *
 * @property MoneyAccount $fromAccount
 * @property MoneyAccount $toAccount
 * @property UsedWebPaymentType $webPaymentType
 * @property PayoutProposal[] $payoutProposals
 */
class MoneyTransactionBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_transaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_account_id', 'to_account_id', 'amount', 'status_id', 'type_id', 'created_by_id', 'updated_by_id', 'web_payment_type_id'], 'integer'],
            [['note', 'description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['payment_side_id'], 'string', 'max' => 255],
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => MoneyAccount::className(), 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => MoneyAccount::className(), 'targetAttribute' => ['to_account_id' => 'id']],
            [['web_payment_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UsedWebPaymentType::className(), 'targetAttribute' => ['web_payment_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payment_side_id' => Yii::t('app', 'Payment Side ID'),
            'from_account_id' => Yii::t('app', 'From Account ID'),
            'to_account_id' => Yii::t('app', 'To Account ID'),
            'amount' => Yii::t('app', 'Amount'),
            'note' => Yii::t('app', 'Note'),
            'status_id' => Yii::t('app', 'Status ID'),
            'type_id' => Yii::t('app', 'Type ID'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'web_payment_type_id' => Yii::t('app', 'Web Payment Type ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromAccount()
    {
        return $this->hasOne(MoneyAccount::className(), ['id' => 'from_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToAccount()
    {
        return $this->hasOne(MoneyAccount::className(), ['id' => 'to_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebPaymentType()
    {
        return $this->hasOne(UsedWebPaymentType::className(), ['id' => 'web_payment_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayoutProposals()
    {
        return $this->hasMany(PayoutProposal::className(), ['transaction_id' => 'id']);
    }
}
