<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "source_message".
 */
class SourceMessage extends \app\models\base\SourceMessageBase
{
    /**
     * @var array сюда попадают варианты переводов из POST массива
     */
    public $translations = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['translations'],'safe'];
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
     * Получить перевод лля конкретного языка
     * @param $language
     * @return null|string
     */
    public function getTranslatedText($language)
    {
        foreach ($this->messages as $message){
            if($message->language == $language){
                return $message->translation;
            }
        }
        return null;
    }

    /**
     * Возвращает перевод для указнного языка, создает в случае отсутствия (не вписывая в базу)
     * @param $language
     * @return Message
     */
    public function getTrlFor($language)
    {
        /* @var $trl Message */
        $trl = Message::find()->where(['language' => $language, 'id' => $this->id])->one();

        if(empty($trl)){
            $trl = new Message();
            $trl->language = $language;
            $trl->id = $this->id;
        }

        return $trl;
    }
}
