<?php

namespace app\models\base;

use Yii;

use app\models\PayoutProposal;

/**
 * This is the base model class for table "payout_proposal_image".
 *
 * @property int $id
 * @property int $proposal_id
 * @property int $priority
 * @property string $title
 * @property string $description
 * @property string $filename
 * @property int $size
 * @property string $crop_settings
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property PayoutProposal $proposal
 */
class PayoutProposalImageBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payout_proposal_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proposal_id', 'priority', 'size', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'description', 'filename', 'crop_settings'], 'string', 'max' => 255],
            [['proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayoutProposal::className(), 'targetAttribute' => ['proposal_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'proposal_id' => Yii::t('app', 'Proposal ID'),
            'priority' => Yii::t('app', 'Priority'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'filename' => Yii::t('app', 'Filename'),
            'size' => Yii::t('app', 'Size'),
            'crop_settings' => Yii::t('app', 'Crop Settings'),
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
    public function getProposal()
    {
        return $this->hasOne(PayoutProposal::className(), ['id' => 'proposal_id']);
    }
}
