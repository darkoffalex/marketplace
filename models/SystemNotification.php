<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_notification".
 */
class SystemNotification extends \app\models\base\SystemNotificationBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    /**
     * Создать уведомление
     * @param $message_fb
     * @param null|string $recipientFbId
     * @param null|string $email
     * @param null|string $message_email
     * @param null|string $subject_email
     */
    public static function CreateNotification($message_fb, $recipientFbId = null, $email = null, $message_email = null, $subject_email = 'Notification')
    {
        $notification = new SystemNotification();
        $notification -> recipient_email = $email;
        $notification -> recipient_fb_id = $recipientFbId;
        $notification -> message_fb = $message_fb;
        $notification -> message_email = !empty($message_email) ? $message_email : $message_fb;
        $notification -> subject_email = $subject_email;
        $notification -> sent = (int)false;
        $notification -> created_at = date('Y-m-d H:i:s',time());

        if(!empty($notification->recipient_email) || !empty($notification->recipient_fb_id)){
            $notification->save();
        }
    }
}
