<?php
namespace app\components;

use Yii;
use app\models\SourceMessage;
use yii\i18n\MissingTranslationEvent;
/**
 * Class TranslationEventHandler
 * @package app\components
 */
class TranslationEventHandler
{
    /**
     * Обработка события отсутствия переода для конкретного лейбла
     * @param MissingTranslationEvent $event
     */
    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        if(Yii::$app->params['localization']['addMissing']){
            $exist = SourceMessage::find()->where(['message' => $event->message])->count();
            if(!$exist){
                $sourceMessage = new SourceMessage();
                $sourceMessage->message = $event->message;
                $sourceMessage->category = $event->category;
                $sourceMessage->save();
            }
        }

        if(Yii::$app->params['localization']['markMissing']){
            $event->translatedMessage = "@MISSING: {$event->category}.{$event->message} FOR LANGUAGE {$event->language} @";
        }else{
            $event->translatedMessage = $event->message;
        }
    }
}
