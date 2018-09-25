<?php

namespace app\models;

use app\helpers\Converts;
use yii\helpers\Url;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "short_link".
 */
class ShortLink extends \app\models\base\ShortLinkBase
{
    /**
     * @var UploadedFile
     */
    public $image = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['image', 'file', 'extensions' => ['jpg','png','gif','jpeg'], 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 5];
        $rules[] = ['key', 'unique', 'on' => ['editing_wa','editing_re']];
        $rules[] = ['phone', 'required', 'on' => 'editing_wa'];
        $rules[] = ['phone', 'match', 'pattern' => '/^\+(\d+)/', 'on' => 'editing_wa'];
        $rules[] = ['original_link', 'required', 'on' => 'editing_re'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $label['image'] = Yii::t('app','OG picture');
        return $labels;
    }

    /**
     * Получение уникального ключа
     * @param bool $next
     */
    public function ObtainKey($next = false){

        if(empty($this->number) || $next){
            /* @var $lastNumberObject self */
            $lastNumberObject = self::find()->orderBy('number DESC')->one();
            $this->number = !empty($lastNumberObject) ? $lastNumberObject->number + 1 : 1;
            $this->update();
        }

        if(empty($this->custom_key) || empty($this->key)){
            $key = (string)Converts::base_convert($this->number,10,'abcdefghijklmnopqrstuvwxyz0123456789');
            if(ShortLink::find()->where(['key' => $key])->count() > 0){
                $this->ObtainKey(true);
            }

            $this->key = $key;
            $this->update();
        }
    }

    /**
     * Генерация короткой ссылки
     * @param bool $https
     * @param null $domain
     * @return string
     */
    public function GetLink($https = false, $domain = null){
        $s = $https ? 's' : '';
        $domain = !empty($domain) ? $domain : str_replace(['/','user.'],['',''],Url::base('')).'/sl';
        $url = "http{$s}://{$domain}/{$this->key}";
        return $url;
    }
}
