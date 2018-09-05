<?php
namespace app\modules\user\controllers;

use yii\web\Controller;
use Yii;
/**
 * Class MessagesController
 * @package app\modules\user\controllers
 */
class MessagesController extends Controller
{
    /**
     * Сообщение о недоступности опции мониторинга
     * @return string
     */
    public function actionUnavailableCommon()
    {
        $message = Yii::t('app','This option is locked until your bind at least one marketplace');
        return $this->renderPartial('_common',compact('message'));
    }
}
