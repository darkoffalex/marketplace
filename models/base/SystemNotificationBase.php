<?php

namespace app\models\base;

use Yii;


/**
 * This is the base model class for table "system_notification".
 *
 * @property int $id
 * @property string $recipient_fb_id
 * @property string $recipient_email
 * @property string $message_fb
 * @property string $message_email
 * @property string $subject_email
 * @property int $sent
 * @property string $created_at
 */
class SystemNotificationBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_fb', 'message_email'], 'string'],
            [['sent'], 'integer'],
            [['created_at'], 'safe'],
            [['recipient_fb_id', 'recipient_email', 'subject_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'recipient_fb_id' => Yii::t('app', 'Recipient Fb ID'),
            'recipient_email' => Yii::t('app', 'Recipient Email'),
            'message_fb' => Yii::t('app', 'Message Fb'),
            'message_email' => Yii::t('app', 'Message Email'),
            'subject_email' => Yii::t('app', 'Subject Email'),
            'sent' => Yii::t('app', 'Sent'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }
}
