<?php

namespace app\models;

use app\helpers\CropHelper;
use app\helpers\FileLoad;
use Yii;

/**
 * This is the model class for table "poster_image".
 */
class PosterImage extends \app\models\base\PosterImageBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    /**
     * Удаление изображения
     * @return bool
     */
    public function deleteImage()
    {
        if(!empty($this->filename)){
            if(file_exists(Yii::getAlias("@webroot/upload/images/{$this->filename}"))){
                return unlink(Yii::getAlias("@webroot/upload/images/{$this->filename}"));
            }
        }
        return false;
    }

    /**
     * Есть ли файл
     * @return bool
     */
    public function hasFile()
    {
        return FileLoad::hasFile($this,'filename');
    }

    /**
     * Получить URL на оригинальное изображение
     * @return null|string
     */
    public function getUrl()
    {
        return FileLoad::getFileUrl($this,'filename');
    }

    /**
     * Получить ссылку на миниатюру изображения
     * @param int $width
     * @param int $height
     * @return null|string
     */
    public function getThumbnailUrl($width = 640, $height = 480)
    {
        return CropHelper::ThumbnailUrl($this->filename,$width,$height);
    }
}
