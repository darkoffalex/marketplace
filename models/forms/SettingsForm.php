<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Settings;
use yii\helpers\ArrayHelper;

/**
 * Class SettingsForm
 * @package app\models\forms
 */
class SettingsForm extends Model
{
    /**
     * @var self экземпляр данного объекта
     */
    protected static $_instance;

    /**Параметры приложения facebook для входа**/
    public $fb_auth_client_id;                     //Facebook client-id приложения авторизации
    public $fb_auth_app_secret;                    //Facebook app-secret приложения авторизации
    public $fb_app_admin_token;                    //Facebook token админа приложения

    /**Параметры приложения facebook для чат-бота (мессенджер)**/
    public $fb_messenger_hook_verify_token;        //Facebook маркер проверки (при настройке чат-бота)
    public $fb_messenger_client_id;                //Facebook client-id приложения чат-бота
    public $fb_messenger_app_secret;               //Facebook app-secret приложения чат-бота
    public $fb_messenger_page_monitoring_id;       //Facebook ID страницы для уведомлений по стоп-словам
    public $fb_messenger_page_monitoring_token;    //Facebook маркер страницы для уведомлений по стоп-словам
    public $fb_messenger_page_notifications_id;    //Facebook ID страницы для уведомлений от системы
    public $fb_messenger_page_notifications_token; //Facebook маркер страницы для уведомлений от системы

    /**Email'ы для отправки и для получения уведомлений (для админа)**/
    public $email_for_sending;
    public $email_for_notifications;

    /**Параметры SMTP**/
    public $smtp_enabled;
    public $smtp_host;
    public $smtp_login;
    public $smtp_password;
    public $smtp_port;
    public $smtp_encryption;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['fb_auth_client_id','fb_auth_app_secret','fb_app_admin_token'],'string'],
            [['fb_messenger_hook_verify_token','fb_messenger_client_id','fb_messenger_app_secret','fb_messenger_page_monitoring_id','fb_messenger_page_monitoring_token','fb_messenger_page_notifications_id','fb_messenger_page_notifications_token'], 'string'],
            [['email_for_sending','email_for_notifications'], 'string'],
            [['email_for_sending','email_for_notifications'], 'email'],
            [['smtp_enabled', 'smtp_port'], 'integer'],
            [['smtp_encryption','smtp_login','smtp_password','smtp_host'],'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fb_auth_client_id' => Yii::t('app','Facebook client ID (authorization & personal group parsing)'),
            'fb_auth_app_secret' => Yii::t('app','Facebook app secret (authorization & personal group parsing)'),
            'fb_app_admin_token' => Yii::t('app','Facebook application admin\'s (owner\'s) token'),

            'fb_messenger_hook_verify_token' => Yii::t('app','Messenger app web-hook verify token'),
            'fb_messenger_client_id' => Yii::t('app','Messenger app client ID'),
            'fb_messenger_app_secret' => Yii::t('app','Messenger app secret key'),
            'fb_messenger_page_monitoring_id' => Yii::t('app','Monitoring notifications page ID'),
            'fb_messenger_page_monitoring_token' => Yii::t('app','Monitoring notifications page token'),
            'fb_messenger_page_notifications_id' => Yii::t('app','System notifications page ID'),
            'fb_messenger_page_notifications_token' => Yii::t('app','System notifications page token'),

            'email_for_sending' => Yii::t('app','Sending email (send from)'),
            'email_for_notifications' => Yii::t('app','Admin notifications email'),

            'smtp_enabled' => Yii::t('app','Use SMTP'),
            'smtp_host' => Yii::t('app','SMTP host'),
            'smtp_login' => Yii::t('app','SMTP login'),
            'smtp_password' => Yii::t('app','SMTP password'),
            'smtp_port' => Yii::t('app','SMTP port'),
            'smtp_encryption' => Yii::t('app','SMTP encription'),
        ];
    }

    /**
     * CommonSettingsForm constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->refresh();
        parent::__construct($config);
    }

    /**
     * Синглтон - нельзя копировать объекты данного класса
     */
    public function __clone(){}

    /**
     * Синглтон - получение экземпляра
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Получение значений из базы
     */
    public function refresh()
    {
        //Наименование - правило (общий спсиок всех полей)
        $validFields = [];

        //Пройтись по всем правилам
        foreach ($this->rules() as $ruleConfig)
        {
            //Поля
            $fields = ArrayHelper::getValue($ruleConfig,0);
            $fields = is_array($fields) ? $fields : [$fields];

            //Правило
            $rule = ArrayHelper::getValue($ruleConfig,1);

            //Если правило для строки или для int'а - добавить поля к общему списку
            if($rule == 'string' || $rule == 'integer'){
                foreach ($fields as $fieldName){
                    $validFields[$fieldName] = $rule;
                }
            }
        }

        //Получить все значения
        /* @var $settings Settings[] */
        $settings = Settings::find()->where(['name' => array_keys($validFields)])->all();

        //Инициализировать поля значениями из базы
        foreach ($settings as $setting){
            $name = $setting->name;
            $this->$name = $validFields[$name] == 'string' ? $setting->value_text : $setting->value_numeric;
        }
    }

    /**
     * Сохранение значений в базу
     * @param bool $validate
     */
    public function save($validate = true)
    {
        if(($validate && $this->validate()) || !$validate)
        {
            foreach ($this->rules() as $ruleConfig)
            {
                //Все поля данного правила
                $fields = ArrayHelper::getValue($ruleConfig,0);
                $fields = is_array($fields) ? $fields : [$fields];

                //Правило
                $rule = ArrayHelper::getValue($ruleConfig,1);

                //Если правило для строки или для int'а - обновить поле
                if($rule == 'string' || $rule == 'integer'){
                    foreach ($fields as $fieldName){
                        /* @var $settings Settings[] */
                        $setting = Settings::find()->where(['name' => $fieldName])->one();
                        if(empty($setting)){
                            $setting = new Settings();
                            $setting -> name = $fieldName;
                        }

                        if($rule == 'integer'){
                            $setting->value_numeric = $this->$fieldName;
                        }else{
                            $setting->value_text = $this->$fieldName;
                        }

                        $setting->save();
                    }
                }
            }
        }
    }


}