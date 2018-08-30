<?php
namespace app\modules\groupAdmin\controllers;

use yii\web\Controller;
use Yii;
/**
 * Class MessagesController
 * @package app\modules\groupAdmin\controllers
 */
class MessagesController extends Controller
{
    /**
     * Сообщение о недоступности опции мониторинга
     * @return string
     */
    public function actionUnavailableCommon()
    {
        $message = Yii::t('app','This option is locked until your group admin status be approved');
        return $this->renderPartial('_common',compact('message'));
    }
}
