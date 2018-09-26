<?php
namespace app\modules\user\controllers;

use app\models\forms\ProfileForm;
use yii\web\Controller;
use app\models\User;
use Yii;
/**
 * Class ProfileController
 * @package app\modules\user\controllers
 */
class ProfileController extends Controller
{
    /**
     * Страница профиля
     * @return string
     */
    public function actionIndex()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        $model = new ProfileForm();

        if($model->load(Yii::$app->request->post())){

            $model->notification_types = implode(',',$model->notification_types);

            if($model->validate()){
                $user->name = $model->name;
                $user->email = $model->email;
                $user->updated_at = date('Y-m-d H:i:s');
                $user->updated_by_id = $user->id;
                $user->fb_msg_types = $model->notification_types;
                $user->save();
            }
        }

        $model->name = $user->name;
        $model->email = $user->email;
        $model->notification_types = !empty($user->fb_msg_types) ? explode(',',$user->fb_msg_types) : [];

        return $this->render('index',compact('model'));
    }
}