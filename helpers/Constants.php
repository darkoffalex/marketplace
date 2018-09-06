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
    const STATUS_DELETED = -1;

    //Статусы пользователей
    const USR_STATUS_ENABLED = 1;
    const USR_STATUS_DISABLED = 2;

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
            Constants::STATUS_DELETED => Yii::t('app','Deleted')
        ];

        return ArrayHelper::getValue($names,$statusId);
    }
}
