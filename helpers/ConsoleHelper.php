<?php
namespace app\helpers;

use Yii;

class ConsoleHelper
{
    /**
     * Начало процесса (генерирует ID и выводит информацию)
     * @return string
     */
    public static function processStart()
    {
        $processId = uniqid();
        $startedAt = date('Y-m-d H:i:s', time());
        echo "Process \"{$processId}\" started at {$startedAt}\n\n";
        return $processId;
    }

    /**
     * Сообщение о завершении процесса
     * @param $id
     */
    public static function processEnd($id)
    {
        $endedAt = date('Y-m-d H:i:s', time());
        echo "Process \"{$id}\" ended at {$endedAt}\n\n";
    }

    /**
     * Установка временной зоны
     * @param string $timezone
     */
    public static function setTimezone($timezone = 'UTC')
    {
        //Установка временной зоны PHP
        date_default_timezone_set($timezone);

        //Синхронизировать MySQL с временной зоной PHP
        $now = new \DateTime();
        $min = $now->getOffset() / 60;
        $sgn = ($min < 0 ? -1 : 1);
        $min = abs($min);
        $hrs = floor($min / 60);
        $min -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $min);
        Yii::$app->db->createCommand("SET time_zone='$offset';")->execute();
    }
}