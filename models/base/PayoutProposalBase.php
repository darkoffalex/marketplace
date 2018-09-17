<?php

namespace app\models\base;

use Yii;

use app\models\MoneyTransaction;
use app\models\User;
use app\models\PayoutProposalImage;

/**
 * This is the base model class for table "payout_proposal".
 *
 * @property int $id
 * @property int $user_id
 * @property int $transaction_id
 * @property string $description
 * @property int $amount
 * @property int $status_id
 * @property string $discard_reason
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property MoneyTransaction $transaction
 * @property User $user
 * @property PayoutProposalImage[] $payoutProposalImages
 */
class PayoutProposalBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payout_proposal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'transaction_id', 'amount', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['description', 'discard_reason'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => MoneyTransaction::className(), 'targetAttribute' => ['transaction_id' => 'id']],
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
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'description' => Yii::t('app', 'Description'),
            'amount' => Yii::t('app', 'Amount'),
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
    public function getTransaction()
    {
        return $this->hasOne(MoneyTransaction::className(), ['id' => 'transaction_id']);
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
    public function getPayoutProposalImages()
    {
        return $this->hasMany(PayoutProposalImage::className(), ['proposal_id' => 'id']);
    }
}
