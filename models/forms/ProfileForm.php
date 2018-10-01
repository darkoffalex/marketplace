<?php

namespace app\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

/**
 * Class ProfileForm
 * @package app\models\forms
 */
class ProfileForm extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var UploadedFile
     */
    public $avatar;

    /**
     * @var string
     */
    public $email;

    /**
     * @var array|null
     */
    public $fb_notification_types = [];

    /**
     * @var array|null
     */
    public $email_notification_types = [];

    /**
     * @var null|bool
     */
    public $email_notifications_enabled;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','email'],'string'],
            [['email'],'email'],
            [['fb_notification_types','email_notification_types'], 'string'],
            [['email_notifications_enabled'], 'integer'],
            [['name'],'required'],
            [['avatar'], 'file', 'extensions' => ['jpg','png','gif', 'jpeg'], 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app','Email'),
            'name' => Yii::t('app','Name'),
            'avatar' => Yii::t('app','Avatar'),
            'fb_notification_types' => Yii::t('app','Chat bot notification types'),
            'email_notification_types' => Yii::t('app','Email notification types'),
            'email_notifications_enabled' => Yii::t('app','Enable email notifications')
        ];
    }
}
