<?php

namespace app\models\base;

use Yii;

use app\models\DictionaryNotification;
use app\models\DictionarySubscriber;

/**
 * This is the base model class for table "dictionary_notification_task".
 *
 * @property int $id
 * @property int $notification_id
 * @property int $subscriber_id
 * @property int $sent
 *
 * @property DictionaryNotification $notification
 * @property DictionarySubscriber $subscriber
 */
class DictionaryNotificationTaskBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary_notification_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'subscriber_id', 'sent'], 'integer'],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => DictionaryNotification::className(), 'targetAttribute' => ['notification_id' => 'id']],
            [['subscriber_id'], 'exist', 'skipOnError' => true, 'targetClass' => DictionarySubscriber::className(), 'targetAttribute' => ['subscriber_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'notification_id' => Yii::t('app', 'Notification ID'),
            'subscriber_id' => Yii::t('app', 'Subscriber ID'),
            'sent' => Yii::t('app', 'Sent'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(DictionaryNotification::className(), ['id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber()
    {
        return $this->hasOne(DictionarySubscriber::className(), ['id' => 'subscriber_id']);
    }
}
