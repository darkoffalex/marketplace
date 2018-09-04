<?php
namespace app\helpers;

use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;

class FileLoad
{
    /* Ф А Й Л Ы  И  З А Г Р У З К А */

    /**
     * Загрузить файл принадлежащий объекту (очистить старый, если необходимо)
     * @param $object ActiveRecord
     * @param $fileAttribute
     * @param $filenameAttribute
     * @param bool $clearOld
     * @param string $directory
     */
    public static function loadAndClearOld(&$object, $fileAttribute, $filenameAttribute, $clearOld = true, $directory = '@webroot/upload/images/')
    {
        //Очистить старый файл если необходимо
        if($clearOld){
            if(self::hasFile($object,$filenameAttribute,$directory)){
                self::deleteFile($object,$filenameAttribute,$directory);
            }
        }

        //Получить загруженный файл
        /* @var $fileObject UploadedFile */
        $fileObject = $object->$fileAttribute;

        //Если файл не пуст
        if(!empty($fileObject)){

            //Уникальное название
            $name = uniqid().'.'.$fileObject->extension;

            //Создать директорию на случай отсутствия
            FileHelper::createDirectory(\Yii::getAlias($directory));

            //Загрузить файл
            $fileObject->saveAs(\Yii::getAlias($directory.$name));

            //Указать новое имя файла
            $object->$filenameAttribute = $name;

            //Сбросить UploadedFile объект (во избежании глюков при сохранении модели)
            $object->$fileAttribute = null;
        }
    }

    /**
     * Удаление файла принадлежащего объекту
     * @param $object ActiveRecord
     * @param $filenameAttribute
     * @param string $directory
     * @return bool
     */
    public static function deleteFile(&$object, $filenameAttribute, $directory = '@webroot/upload/images/')
    {
        if(self::hasFile($object,$filenameAttribute,$directory)){
            $path = \Yii::getAlias($directory.$object->$filenameAttribute);
            return unlink($path);
        }
        return false;
    }

    /**
     * Есть ли файл у конкретного объекта
     * @param $object ActiveRecord
     * @param $filenameAttribute string
     * @param string $directory string
     * @return bool
     */
    public static function hasFile(&$object, $filenameAttribute, $directory = '@webroot/upload/images/')
    {
        if(!$object->hasAttribute($filenameAttribute)){
            return false;
        }elseif(empty($object->$filenameAttribute) || !file_exists(\Yii::getAlias($directory.$object->$filenameAttribute))){
            return false;
        }
        return true;
    }

    /**
     * Получить URL на файд
     * @param $object
     * @param $filenameAttribute
     * @param string $directory
     * @return null|string
     */
    public static function getFileUrl(&$object, $filenameAttribute, $directory = '@webroot/upload/images/')
    {
        if(self::hasFile($object,$filenameAttribute,$directory)){
            return Url::to(str_replace('@webroot','@web',$directory).$object->$filenameAttribute);
        }

        return null;
    }
}