<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "country".
 */
class Country extends \app\models\base\CountryBase
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
     * Получить ссылку на страницу страны
     * @param bool $scheme
     * @return string
     */
    public function getUrl($scheme = false)
    {
        return Url::to(['country/index','subSubDomain' => $this->domain_alias],$scheme);
    }
}
