<?php

namespace app\helpers;

use app\models\forms\SettingsForm;
use yii\swiftmailer\Mailer as SwiftMailer;

/**
 * Хелпер. Отправка email'ов
 *
 * @copyright 	2015 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\helpers
 */
class MailSender
{
    /**
     * @var null|SwiftMailer;
     */
    public $mailer = null;

    /**
     * @var self
     */
    protected static $_instance = null;

    /**
     * Получить экземпляр
     * @return MailSender
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Констроуктор
     * Создать мейлер с необходимой конфигурацией (в зависимости он настроек)
     */
    private function __construct()
    {
        if(SettingsForm::getInstance()->smtp_enabled){
            $this->mailer = new SwiftMailer([
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => SettingsForm::getInstance()->smtp_host,
                    'username' => SettingsForm::getInstance()->smtp_login,
                    'password' => SettingsForm::getInstance()->smtp_password,
                    'port' => SettingsForm::getInstance()->smtp_port,
                    'encryption' => SettingsForm::getInstance()->smtp_encryption,
                ]
            ]);
        }else{
            $this->mailer = new SwiftMailer([
                'useFileTransport' => false,
            ]);
        }
    }

    /**
     * Синглтон - нельзя копировать объекты данного класса
     */
    private function __clone()
    {
    }
}