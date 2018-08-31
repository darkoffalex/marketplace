<?php

namespace app\models;

use app\helpers\Help;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "language".
 */
class Language extends \app\models\base\LanguageBase
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
     * Получить объект соответствующий текущему яюзыку системы
     * @return Language
     */
    public static function findCurrent()
    {
        /* @var $language Language */
        $language = Language::find()->where(['prefix' => Yii::$app->language])->one();

        if(empty($language)){
            $language = new Language();
            $language->prefix = Yii::$app->language;
        }

        return $language;
    }

    /**
     * Получить ссылку на текущую страницу на данном языке
     * @return string
     */
    public function getCurrentLink()
    {
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        $moduleId = Yii::$app->controller->module->id == 'MarketplaceGuide' ? null : Yii::$app->controller->module->id;

        $url = [
            (!empty($moduleId) ? "/$moduleId" : "")."/$controllerId/$actionId"
        ];

        $url = ArrayHelper::merge($url,Yii::$app->request->get());
        $url['language'] = $this->prefix;

        return Url::to($url);
    }
}
