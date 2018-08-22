<?php

namespace app\modules\admin\helpers;

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use app\helpers\Constants;

/**
 * Хелпер. Предназначен для разграничения доступа по ролям.
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\helpers
 */
class Access
{
    /**
     * Получить список ролей с доступом к админ-панели
     * @return array
     */
    public static function getRolesForAccess()
    {
        return [
            Constants::ROLE_ADMIN,
            Constants::ROLE_BOOKKEEPER
        ];
    }
    /**
     * Проверка доступа. Может ли пользователь совершать указанное действие
     * @param User|IdentityInterface $user
     * @param string $controllerID
     * @param null|string $actionID
     * @return bool
     */
    public static function has($user, $controllerID, $actionID = null)
    {
        //Если передано число (ID) найти по ID'у
        if(is_numeric($user)){
            $user = User::findOne((int)$user);
        }

        //Если пользователь не передан (пуст) - доступ закрыт, вернуть false
        if(empty($user) || !in_array($user->role_id,self::getRolesForAccess())){
            return false;
        }

        //Все методы включенные в этот массив доступны только для указанных ролей,
        //остальные будут доступны любому авторизованному пользователю
        $methods = [
            //раздел управления пользователями
            'users/*' => [Constants::ROLE_ADMIN],
        ];

        if(empty($actionID)){
            if(!empty($methods[$controllerID.'/*'])) {
                $roles = $methods[$controllerID.'/*'];
                return in_array($user->role_id,$roles);
            }
        }else{
            if(!empty($methods[$controllerID.'/'.$actionID])){
                $roles = $methods[$controllerID.'/'.$actionID];
                return in_array($user->role_id,$roles);
            }elseif(!empty($methods[$controllerID.'/*'])){
                $roles = $methods[$controllerID.'/*'];
                return in_array($user->role_id,$roles);
            }
        }

        return true;
    }

    /**
     * Проверка, какая роль значительнее. Например админ значительнее модератора, соответственно он должен
     * иметь возможность делать все то что может и модератор
     *
     * Проверка по этому принципу на данный момент не имплементирована в методе has, предпологается
     * что это будет сделано позднее
     *
     * @param $srcRole int
     * @param $dstRole int
     * @param bool $andEqual
     * @return bool
     */
    public static function higher($srcRole, $dstRole, $andEqual = false)
    {
        //Уровни допуска
        $levels = [
            Constants::ROLE_BOOKKEEPER => 2,
            Constants::ROLE_ADMIN => 3,
        ];

        //Получить уровни допуска для первой и второй роли, которые нужно сравнить
        $srcPower = ArrayHelper::getValue($levels,$srcRole,0);
        $dstPower = ArrayHelper::getValue($levels,$dstRole,0);

        //Сравнение в зависимости от необходимости использовать "больше или равно"
        return $andEqual ? ($srcPower >=  $dstPower) : $srcPower > $dstPower;
    }
}