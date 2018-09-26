<?php

namespace app\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Хелпер. Содержит используемые системой константы. Менять занчения крайне не рекомендуется если система уже развернута
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\helpers
 */
class Constants
{
    //Общие статусы
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    const STATUS_TEMPORARY = -1;

    //Статусы пользователей
    const USR_STATUS_ENABLED = 1;
    const USR_STATUS_DISABLED = 2;

    //Статус транзакций и платежей
    const PAYMENT_STATUS_NEW = 1;
    const PAYMENT_STATUS_DONE = 2;
    const PAYMENT_STATUS_CANCELED = 3;

    //Типы транзакций и платежей
    const PAYMENT_WEB_INITIATED = 1;
    const PAYMENT_WEB_RECURRENT_INITIATED = 4;
    const PAYMENT_CASH_INITIATED = 2;
    const PAYMENT_INTERNAL_INITIATED = 3;

    //Роли пользователей
    const ROLE_ADMIN = 1;
    const ROLE_BOOKKEEPER = 2;
    const ROLE_USER = 3;

    //Состояние заявки
    const CV_STATUS_NEW = 1;
    const CV_STATUS_APPROVED = 2;
    const CV_STATUS_REJECTED = 3;

    //Тип периода (используется в тарифах)
    const PERIOD_DAYS = 1;
    const PERIOD_WEEKS = 2;
    const PERIOD_MONTHS = 3;

    //Спец-тип тарифа
    const TARIFF_SUB_TYPE_REGULAR = 0;
    const TARIFF_SUB_TYPE_ADMIN_POST = 1;
    const TARIFF_SUB_TYPE_COMPETITIONS = 2;

    //Типы счетов
    const SYSTEM_INCOME_ACCOUNT = 1;
    const SYSTEM_OUTGO_ACCOUNT = 2;
    const MANAGER_ACCOUNT = 3;
    const GROUP_ADMIN_ACCOUNT = 4;
    const MEMBER_ACCOUNT = 5;

    //Тип платежных систем
    const PAYMENT_SYSTEM_YM = 1;
    const PAYMENT_SYSTEM_PAYPAL = 2;

    //Типы проверки подтверждения статуса
    const ADMIN_POST_TIME_AT_REVIEW = 1;
    const ADMIN_POST_TIME_APPROVED = 2;
    const ADMIN_POST_TIME_DISAPPROVED = 3;

    //Типы сокращенных ссылок
    const SHORT_LINK_REGULAR = 1;
    const SHORT_LINK_WHATSAPP = 2;

    //Типы уведомлений
    const NOTIFY_NEW_MARKETPLACE = 1;
    const NOTIFY_NEW_ADVERTISEMENTS = 2;
    const NOTIFY_NEW_PAYOUTS = 5;
    const NOTIFY_MARKETPLACE_CONFIRMATION = 3;
    const NOTIFY_ADVERTISEMENTS_CONFIRMATION = 4;
    const NOTIFY_PAYOUTS_CONFIRMATION = 5;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Получить имя роли
     * @param $roleID
     * @return mixed
     */
    public static function GetRoleName($roleID)
    {
        $names = [
            Constants::ROLE_ADMIN => Yii::t('app','Administrator'),
            Constants::ROLE_BOOKKEEPER => Yii::t('app','Bookkeeper'),
            Constants::ROLE_USER => Yii::t('app','User')
        ];

        return ArrayHelper::getValue($names,$roleID);
    }

    /**
     * Получить имя статуса
     * @param $statusId
     * @return mixed
     */
    public static function GetStatusName($statusId)
    {
        $names = [
            Constants::STATUS_ENABLED => Yii::t('app','Enabled/Active'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
            Constants::STATUS_TEMPORARY => Yii::t('app','Temporary')
        ];

        return ArrayHelper::getValue($names,$statusId);
    }
}
