<?php

namespace app\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\helpers\FileHelper;

use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\ManipulatorInterface;

/**
 * Хелпер. Вспомогательный класс для получения ссылок на обрезанные картинки
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\helpers
 */
class CropHelper
{
    /**
     * Получить ссылку на обрезанную картинку какого-то объекта
     * Следует учитывать что у объекта должен быть атрибут с именем файла изображения и атрибут содержащий
     * параметры обрезки изображения
     *
     * @param ActiveRecord $object
     * @param string $filenameAttribute
     * @param string $cropSettingsAttribute
     * @param array array $cropSizes
     * @param string string $directory
     * @param bool $scheme
     * @param bool $randomize
     * @return null|string
     */
    public static function GetCroppedUrl(
        &$object,
        $filenameAttribute,
        $cropSettingsAttribute = null,
        $cropSizes = [468,234],
        $scheme = true,
        $randomize = false,
        $directory = '@webroot/upload/images/')
    {
        //Если нет изображения - вернуть null
        if(!FileLoad::hasFile($object,$filenameAttribute,$directory)){
            return null;
        }

        //Получить наименование файла
        $filename = $object->$filenameAttribute;

        //Если наименование не было получено - вернуть null
        if(empty($filename)){
            return null;
        }

        //Получить полный путь к загруженному файлу
        $originalImagePath = Yii::getAlias($directory.$filename);

        //Область обрезки (может быть пустой)
        $cropArea = null;
        if(!empty($cropSettingsAttribute)){
            $cropArea = !empty($object->$cropSettingsAttribute) ? json_decode($object->$cropSettingsAttribute,true) : null;
        }

        try{
            //Если область обрезки картинки не установлена - используется область по умолчанию
            if(empty($cropArea)){
                //Получить имя и расширение нового файла
                $croppedFilename = md5($filename.'_'.$cropSizes[0].'_'.$cropSizes[0].'_DEFAULT');
                $croppedFilenameExt = pathinfo($originalImagePath, PATHINFO_EXTENSION);

                //Оригинальный файл
                $imageUploaded = Image::getImagine()->open($originalImagePath);

                //Создать директорию в случае ее отсутствия
                FileHelper::createDirectory(Yii::getAlias('@webroot/upload/images/cropped/'));

                //Сохранить обрезанный файл если он отсутствует
                if(!file_exists(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt))){
                    $imageUploaded
                        ->thumbnail(new Box($cropSizes[0],$cropSizes[1]),ManipulatorInterface::THUMBNAIL_OUTBOUND)
                        ->save(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt),['quality' => 100]);
                }
            }
            //Если область установлена
            else{
                //Коррекция области обрезки
                $cropArea['x'] = $cropArea['x'] < 0 ? 0 : $cropArea['x'];
                $cropArea['y'] = $cropArea['y'] < 0 ? 0 : $cropArea['y'];

                //Получить имя и расширение нового файла
                $croppedFilename = md5($filename.'_'.$cropSizes[0].'_'.$cropSizes[0].'_'.$cropArea['x'].'_'.$cropArea['y'].'_'.$cropArea['w'].'_'.$cropArea['h']);
                $croppedFilenameExt = pathinfo($originalImagePath, PATHINFO_EXTENSION);

                //Оригинальный файл
                $imageUploaded = Image::getImagine()->open($originalImagePath);

                //Создать директорию в случае ее отсутствия
                FileHelper::createDirectory(Yii::getAlias('@webroot/upload/images/cropped/'));

                //Сохранить обрезанный файл если он отсутствует
                if(!file_exists(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt))){
                    $imageUploaded
                        ->crop(new Point($cropArea['x'],$cropArea['y']),new Box($cropArea['w'],$cropArea['h']))
                        ->resize(new Box($cropSizes[0],$cropSizes[1]))
                        ->save(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt),['quality' => 100]);
                }
            }
        }
        catch (InvalidArgumentException $ex){
            Yii::info($ex->getMessage(),'info');
            return null;
        }

        //Если включена рандомизация ссылки - сгенерировать случайное число добавляемое к URL
        $rnd = $randomize ? '?r='.rand(0,999) : '';
        //Вернуть URL на обрезанное фото
        return Url::to("@web/upload/images/cropped/{$croppedFilename}.{$croppedFilenameExt}", $scheme).$rnd;
    }

    /**
     * Получить thumbnail изображения
     * @param $filename
     * @param $width
     * @param $height
     * @param bool $scheme
     * @param bool $randomize
     * @param string $directory
     * @return null|string
     */
    public static function ThumbnailUrl($filename,$width,$height,$scheme = true,$randomize = false,$directory = '@webroot/upload/images/')
    {
        if(!file_exists(Yii::getAlias( $directory.$filename))){
            return null;
        }

        //Получить полный путь к загруженному файлу
        $originalImagePath = Yii::getAlias($directory.$filename);

        //Получить имя и расширение нового файла
        $croppedFilename = md5($filename.'_'.$width.'_'.$height.'_DEFAULT');
        $croppedFilenameExt = pathinfo($originalImagePath, PATHINFO_EXTENSION);

        //Создать директорию в случае ее отсутствия
        FileHelper::createDirectory(Yii::getAlias('@webroot/upload/images/cropped/'));

        //Оригинальный файл
        $imageUploaded = Image::getImagine()->open($originalImagePath);

        //Сохранить обрезанный файл если он отсутствует
        if(!file_exists(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt))){
            $imageUploaded
                ->thumbnail(new Box($width,$height),ManipulatorInterface::THUMBNAIL_OUTBOUND)
                ->save(Yii::getAlias('@webroot/upload/images/cropped/'.$croppedFilename.'.'.$croppedFilenameExt),['quality' => 100]);
        }

        //Если включена рандомизация ссылки - сгенерировать случайное число добавляемое к URL
        $rnd = $randomize ? '?r='.rand(0,999) : '';
        //Вернуть URL на обрезанное фото
        return Url::to("@web/upload/images/cropped/{$croppedFilename}.{$croppedFilenameExt}", $scheme).$rnd;
    }
}