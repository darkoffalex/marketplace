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
    public $notification_types = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','email'],'string'],
            [['email'],'email'],
            [['notification_types'], 'string'],
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
            'notification_types' => Yii::t('app','Notification types')
        ];
    }
}
