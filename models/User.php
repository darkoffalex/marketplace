<?php

namespace app\models;

use app\components\FacebookBotMessage;
use app\helpers\Help;
use app\models\base\UserBase;
use app\models\forms\SettingsForm;
use Carbon\Carbon;
use pimax\FbBotApp;
use Yii;
use app\helpers\Constants;
use Imagine\Exception\NotSupportedException;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package app\models
 * @property MoneyTransaction[] $groupAdminIncomeTransactions
 * @property MoneyAccount[] $moneyAccountsGroupAdmin
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
        $baseLabels['role_id'] = Yii::t('app','Role');
        $baseLabels['status_id'] = Yii::t('app','Status');
        $baseLabels['is_group_admin'] = Yii::t('app','Approved group admin');
        $baseLabels['is_member'] = Yii::t('app','Approved member');
        $baseLabels['total_agr_income'] = Yii::t('app','Total income');
        $baseLabels['average_agr_day_income'] = Yii::t('app','Average day income');
        $baseLabels['group_members'] = Yii::t('app','Group members');
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
     * Является ли пользователь подтвержденным админом групп (есть ли у него хоть один маркетплейс)
     * @return bool
     */
    public function isApprovedGroupAdmin()
    {
        return Marketplace::find()->where(['user_id' => $this->id])->count() > 0;
    }

    /**
     * Является ли пользователь подтвержденным участником (привязан ли хоть один маркетплейс)
     * @return bool
     */
    public function isApprovedMember()
    {
        return Marketplace::find()->alias('mp')->joinWith('marketplaceKeys mpk')->where(['mpk.used_by_id' => $this->id])->count() > 0;
    }

    /**
     * Есть ли у пользователя доступ (через ключ) к конкретному маркетплейсу
     * @param $id
     * @return bool
     */
    public function hasAccessToMarketplace($id)
    {
        if(empty($this->marketplaceKeys)){
            return false;
        }

        foreach ($this->marketplaceKeys as $key){
            if($key->marketplace_id == $id){
                return true;
            }
        }

        return false;
    }

    /**
     * Получить счет нужного типа пользователя
     * @param int $type
     * @return MoneyAccount
     */
    public function getMoneyAccount($type = Constants::MEMBER_ACCOUNT){

        $allowedTypes = [
            Constants::MEMBER_ACCOUNT,
            Constants::GROUP_ADMIN_ACCOUNT
        ];

        if(in_array($this->role_id,[Constants::ROLE_BOOKKEEPER,Constants::ROLE_ADMIN])){
            $allowedTypes[] = Constants::MANAGER_ACCOUNT;
        }

        if(!in_array($type,$allowedTypes)){
            return null;
        }

        /* @var $account MoneyAccount */
        $account = MoneyAccount::find()->where(['user_id' => $this->id, 'account_type_id' => $type])->one();

        if(empty($account)){
            $account = new MoneyAccount();
            $account -> user_id = $this->id;
            $account -> account_type_id = $type;
            $account -> amount = 0;
            $account -> created_at = date('Y-m-d H:i:s', time());
            $account -> updated_at = date('Y-m-d H:i:s', time());
            $account -> created_by_id = 0;
            $account -> updated_by_id = 0;
            $account -> save();
        }

        return $account;
    }

    /**
     * Получить доход админа группы
     * @param bool $format
     * @return mixed|string
     */
    public function getGroupAdminIncome($format = false)
    {
        return $format ? Help::toPrice($this->total_agr_income) : $this->total_agr_income;
    }

    /**
     * Получить сумму всех невыплаченных выплат этому админу группы
     * @param bool $format
     * @return mixed|string
     */
    public function getUndonePayoutsSum($format = false)
    {
        $sum = $this->getPayoutProposals()->andWhere('status_id != :done',['done' => Constants::PAYMENT_STATUS_DONE])->sum('amount');
        return $format ? Help::toPrice($sum) : $sum;
    }

    /**
     * Получить кол-во рекламодателей однажды оплативших свои объявления
     * @return int|string
     */
    public function getPaidAdvertisersCount()
    {
        return User::find()->alias('u')
            ->joinWith(['posters p','posters.marketplace mp'])
            ->andWhere('p.paid_at IS NOT NULL')
            ->andWhere(['mp.user_id' => $this->id])
            ->distinct()
            ->count();
    }

    /**
     * Получить средний доход админа группы за день
     * @param bool $format
     * @return int|string
     */
    public function getGroupAdminDayIncome($format = false)
    {
        $secondsSinceRegistration = time() - Carbon::parse($this->created_at)->getTimestamp();
        $daysSinceRegistration = max(1,$secondsSinceRegistration / 86400);
        $daysIncome = (int)($this->getGroupAdminIncome() / $daysSinceRegistration);
        return $format ? Help::toPrice($daysIncome) : $daysIncome;
    }

    /**
     * Получить все счета с типом Constants::GROUP_ADMIN_ACCOUNT
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyAccountsGroupAdmin()
    {
        return $this->hasMany(MoneyAccount::class, ['user_id' => 'id'])->andWhere(['account_type_id' => Constants::GROUP_ADMIN_ACCOUNT]);
    }

    /**
     * Получить суммарное кол-во участников в в группах маркетплейсов
     * @return mixed
     */
    public function getTotalGroupMembers()
    {
        return $this->getMarketplaces()->sum('marketplace.group_popularity');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Включены ли уведомления конкретного типа для рассылки по email
     * @param $type
     * @return bool
     */
    public function isEmailNotificationOn($type)
    {
        if(!$this->isEmailNotificationEnabled()){
            return false;
        }

        $types = !empty($this->email_notify_types) ? explode(',',$this->email_notify_types) : null;
        return in_array($type,$types);
    }

    /**
     * Включены ли уведомления конкретного типа для рассылки по чат-ботам
     * @param $type
     * @return bool
     */
    public function isFbNotificationsOn($type)
    {
        if(!$this->isFbNotificationsEnabled()){
            return false;
        }

        $types = !empty($this->fb_msg_types) ? explode(',',$this->fb_msg_types) : null;
        return in_array($type,$types);
    }

    /**
     * Включены ли уведомления по чат-боту
     * @return bool
     */
    public function isFbNotificationsEnabled()
    {
        return !empty($this->fb_msg_uid);
    }

    /**
     * Включены ли уведомления по email
     * @return bool
     */
    public function isEmailNotificationEnabled()
    {
        return !empty($this->email_notify_enabled) && !empty($this->email);
    }

    /**
     * Создать уведомление о подтверждении маркетплейса
     * @param Cv $cv
     */
    public function notifyMarketplaceConfirmation(&$cv)
    {
        $text_fb = Yii::t('app',SettingsForm::getInstance()->notification_template_marketplace_confirmation_fb,[
            'group_name' => $cv->group_name,
            'request_id' => $cv->id,
            'status' => $cv->status_id == Constants::CV_STATUS_APPROVED ? Yii::t('app','Approved') : Yii::t('app','Rejected'),
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $text_email = Yii::t('app',SettingsForm::getInstance()->notification_template_marketplace_confirmation_email,[
            'group_name' => $cv->group_name,
            'request_id' => $cv->id,
            'status' => $cv->status_id == Constants::CV_STATUS_APPROVED ? Yii::t('app','Approved') : Yii::t('app','Rejected'),
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $subject_email = Yii::t('app','Notification',[],!empty($this->preferred_language) ? $this->preferred_language : null);

        SystemNotification::CreateNotification(
            $text_fb,
            $this->isFbNotificationsOn(Constants::NOTIFY_MARKETPLACE_CONFIRMATION) ? $this->fb_msg_uid : null,
            $this->isFbNotificationsOn(Constants::NOTIFY_MARKETPLACE_CONFIRMATION) ? $this->email : null,
            $text_email,
            $subject_email);
    }

    /**
     * Создать уведомление о подтверждении объявления
     * @param Poster $advertisement
     */
    public function notifyAdvertisementConfirmation(&$advertisement)
    {
        $text_fb = Yii::t('app',SettingsForm::getInstance()->notification_template_advertisement_confirmation_fb,[
            'id' => $advertisement->id,
            'name' => $advertisement->title,
            'status' => $advertisement->isApprovedByAll() ? Yii::t('app','Approved') : Yii::t('app','Rejected'),
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $text_email = Yii::t('app',SettingsForm::getInstance()->notification_template_advertisement_confirmation_email,[
            'id' => $advertisement->id,
            'name' => $advertisement->title,
            'status' => $advertisement->isApprovedByAll() ? Yii::t('app','Approved') : Yii::t('app','Rejected'),
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $subject_email = Yii::t('app','Notification',[],!empty($this->preferred_language) ? $this->preferred_language : null);

        SystemNotification::CreateNotification(
            $text_fb,
            $this->isFbNotificationsOn(Constants::NOTIFY_ADVERTISEMENTS_CONFIRMATION) ? $this->fb_msg_uid : null,
            $this->isFbNotificationsOn(Constants::NOTIFY_ADVERTISEMENTS_CONFIRMATION) ? $this->email : null,
            $text_email,
            $subject_email);
    }

    /**
     * Создать уведомление о новом объявлении
     * @param Poster $advertisement
     * @param bool $notifyAdmins
     */
    public function notifyNewAdvertisement(&$advertisement, $notifyAdmins = false)
    {
        if($notifyAdmins){
            /* @var $admins User[] */
            $admins = self::find()
                ->where(['role_id' => [Constants::ROLE_ADMIN,Constants::ROLE_BOOKKEEPER]])
                ->andWhere('(fb_msg_sub_code IS NOT NULL) OR (email_notify_enabled = 1 AND email IS NOT NULL)')
                ->all();

            foreach ($admins as $user){
                $user->notifyNewAdvertisement($advertisement,false);
            }
        }

        $text_fb = Yii::t('app',SettingsForm::getInstance()->notification_template_new_advertisement_fb,[
            'id' => $advertisement->id,
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $text_email = Yii::t('app',SettingsForm::getInstance()->notification_template_new_advertisement_email,[
            'id' => $advertisement->id,
        ],!empty($this->preferred_language) ? $this->preferred_language : null);

        $subject_email = Yii::t('app','Notification',[],!empty($this->preferred_language) ? $this->preferred_language : null);

        SystemNotification::CreateNotification(
            $text_fb,
            $this->isFbNotificationsOn(Constants::NOTIFY_NEW_ADVERTISEMENTS) ? $this->fb_msg_uid : null,
            $this->isFbNotificationsOn(Constants::NOTIFY_NEW_ADVERTISEMENTS) ? $this->email : null,
            $text_email,
            $subject_email);
    }
}
