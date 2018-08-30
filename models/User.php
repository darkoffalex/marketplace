<?php

namespace app\models;

use app\models\base\UserBase;
use Yii;
use app\helpers\Constants;
use Imagine\Exception\NotSupportedException;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package app\models
 */
class User extends UserBase implements IdentityInterface
{
    /**
     * @var string password string
     */
    public $password;


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => [Constants::USR_STATUS_ENABLED]]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status_id' => [Constants::USR_STATUS_ENABLED]]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status_id' => Constants::STATUS_ENABLED,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        $baseLabels['password'] = Yii::t('app','Password');
        return $baseLabels;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['username', 'unique'];
        $rules[] = ['username', 'required'];

        $rules[] = ['password', 'required', 'on' => 'create'];
        $rules[] = ['password', 'string', 'min' => 6];

//        $rules[] = ['email', 'required', 'on' => 'register'];
//        $rules[] = ['email', 'email', 'on' => 'register'];

        return $rules;
    }

    /**
     * Получение аватара
     * @param int $width
     * @param int $height
     * @return null|string
     */
    public function getAvatar($width = 128, $height = 128)
    {
        return !empty($this->avatar_url) ? $this->avatar_url : Url::to('@web/frontend/img/profile_128.png');
    }

    /**
     * Является ли пользователь подтвержденным админом груп
     * @return bool
     */
    public function isApprovedGroupAdmin()
    {
        return Marketplace::find()->where(['user_id' => $this->id])->count() > 0;
    }
}
