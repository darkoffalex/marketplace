<?php

namespace app\helpers;

use yii\helpers\ArrayHelper;

/**
 * Хелпер. Содержит различные вспомогалетльные методы.
 *
 * @copyright 	2015 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\helpers
 */
class Help
{
    /**
     * Ипсользуется для debug'а переменных
     * @param $var
     * @param bool|true $devOnly
     */
    public static function debug($var, $devOnly = true)
    {
        $devIps = [
            '127.0.0.1',
            '::1',
            '78.56.14.109',
            '78.31.184.83'
        ];

        if(($devOnly && in_array($_SERVER["REMOTE_ADDR"],$devIps)) || !$devOnly)
        {
            ob_start();
            print_r($var);
            $out = ob_get_clean();

            echo "<pre>";
            echo htmlentities($out);
            echo "</pre>";
        }
    }

    /**
     * Генерация случайной строки заданной длины
     * @param int $length
     * @param bool|false $numbersOnly
     * @return string
     */
    public static function randomString($length = 10,$numbersOnly = false) {

        $charactersNr = '0123456789';
        $charactersChar = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = $numbersOnly ? $charactersNr : $charactersNr.$charactersChar;

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Конвертирует строку-дату из одного формата в другой
     * @param $dateStr
     * @param string $format
     * @param string $sourceFormat
     * @return string|null;
     */
    public static function dateReformat($dateStr,$format = 'd.m.Y H:i:s',$sourceFormat = 'Y-m-d H:i:s')
    {
        $dt = \DateTime::createFromFormat($sourceFormat,$dateStr);
        return !empty($dt) ? $dt->format($format) : null;
    }

    /**
     * Склонение числительных (0 элементов, 1 элемент, 3 элемента и т.д.)
     * @param $n
     * @param $variants
     * @return string
     */
    public static function pluralLabels($n, $variants)
    {
        $none = ArrayHelper::getValue($variants,0,'элементов');
        $one = ArrayHelper::getValue($variants,1,'элемент');
        $few = ArrayHelper::getValue($variants,2,'элемента');
        $many = ArrayHelper::getValue($variants,3,'элементов');
        $other = ArrayHelper::getValue($variants,4,'элементов');

        if ($n == 0)
            return $n.' '.$none;
        if ($n == 1)
            return $n.' '.$one;
        if ($n % 100 > 10 && $n % 100 < 20)
            return $n.' '.$many;
        switch ($n % 10)
        {
            case 0: return $n.' '.$many;
            case 1: return $n.' '.$one;
            case 2: return $n.' '.$few;
            case 3: return $n.' '.$few;
            case 4: return $n.' '.$few;
            case 5: return $n.' '.$many;
            case 6: return $n.' '.$many;
            case 7: return $n.' '.$many;
            case 8: return $n.' '.$many;
            case 9: return $n.' '.$many;
        }

        return $n.' '.$other;
    }

    /**
     * Получение IP адреса (в том числе за прокси, если возможно)
     * @return mixed
     */
    public static function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}