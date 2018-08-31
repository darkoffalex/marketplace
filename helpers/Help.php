<?php

namespace app\helpers;

use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

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

    /**
     * Транслитерация названий для "дружелюбных" сылок
     * @param $str
     * @param array $options
     * @return string
     * @author Sean Murphy <sean@iamseanmurphy.com>
     * @copyright Copyright 2012 Sean Murphy. All rights reserved.
     * @license http://creativecommons.org/publicdomain/zero/1.0/
     */
    public static function slug($str, $options = array()) {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian - Lithuanian
            'Ā' => 'A', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Ū' => 'u', 'Ė' => 'E', 'Į' => 'I', 'Ų' => 'U',
            'ā' => 'a', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'ū' => 'u', 'ė' => 'e', 'į' => 'i', 'ų' => 'u',
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    /**
     * Список временных зон
     * @param bool $translate
     * @return array
     */
    public static function getTimeZoneArray($translate = true)
    {
        $baseTimeZones = \DateTimeZone::listAbbreviations();
        $result = [];

        foreach ($baseTimeZones as $baseTimeZoneName => $baseTimeZone){
            if(is_array($baseTimeZone)){
                foreach ($baseTimeZone as $timeZone){
                    $result[$timeZone['offset']] = ("[".self::floatToTime($timeZone['offset']/3600))."] ". ($translate ? \Yii::t('app',$timeZone['timezone_id']) : $timeZone['timezone_id']);
                }
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * Привести дробное число к временному формату (вместо 1.5 будет 1:30)
     * @param $float
     * @return string
     */
    public static function floatToTime($float)
    {
        $int = (int)$float;
        $remainder = abs($float - $int);
        return ($float > 0 ? '+' : '-').abs($int).':'.str_pad((int)($remainder*60), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Конвертирование из копеек в читаемую цену
     * @param $cents
     * @return string
     */
    public static function toPrice($cents)
    {
        return number_format(($cents / 100),2,'.','');
    }


    /**
     * Конвертирование из читаемой цены в копейки (значение для хранения в базе)
     * @param $price
     * @return int
     */
    public static function toCents($price)
    {
        if(is_numeric($price)) {
            $clean = floatval(str_replace(',','.',$price));
            return (int)($clean * 100);
        }
        return 0;
    }
}