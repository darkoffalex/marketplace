<?php

namespace app\modules\admin\controllers;

use app\helpers\Help;
use app\models\UserSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Query;
use app\models\User;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Котроллер отвечающий за управление пользователями
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class UsersController extends Controller
{
    /**
     * Отобразить таблицу всех пользователей
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создать нового пользователя
     * @return array|string
     */
    public function actionCreateAjax()
    {
        //текущий пользователь
        /* @var $user User */
        $user = Yii::$app->user->identity;

        //новый пользователь
        $model = new User();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данный в итоге корректны
            if($model->validate()){

                $model->setPassword($model->password);
                $model->generateAuthKey();

                //конвертировать дату
                if(!empty($model->birth_date)){
                    $model->birth_date = Help::dateReformat($model->birth_date,'Y-m-d','d.m.Y');
                }

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = $user->id;
                $model->updated_by_id = $user->id;

                //если созранение удалось
                if($model->save()){
                    //перенаправить на редактирование
                    $this->redirect(Url::to(['/admin/users/update', 'id' => $model->id]));
                }
                //если не удалост созранить
                else{
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                    $this->redirect(Url::to(['/admin/users/index', 'id' => $model->id]));
                }
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_create',compact('model'));
    }

    /**
     * Обновление существующего пользователя
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        //получть пользователя
        /* @var $model User */
        $model = User::find()->where(['id' => (int)$id])->one();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //сохранить старый пароль
        $oldPassHash = $model->password_hash;
        $oldAuthKey = $model->auth_key;

        //если POST
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            //если пароль был указан - использовать его
            if(!empty($model->password)){
                $model->setPassword($model->password);
                $model->generateAuthKey();

            }
            //если не был указан - использовать старый пароль
            else{
                $model->password_hash = $oldPassHash;
                $model->auth_key = $oldAuthKey;
            }

            //если все данные корректны
            if($model->validate()){
                //базоые данные
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;

                $model->update();
                $model->refresh();
            }

        }

        //отобразить форму
        return $this->render('edit',compact('model'));
    }

    /**
     * Удаление пользователя
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        //поиск пользователя по ID
        /* @var $model User */
        $model = User::findOne($id);

        //удалить из базы
        $model->delete();

        //вернуться на предыдущую страницу
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * AJAX поиск пользователей для выпадающейго списка (по начальным символам имени)
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionAjaxSearch($q = null, $id = null)
    {
        //формат ответе - JSON
        Yii::$app->response->format = Response::FORMAT_JSON;

        //ответ по умолчанию
        $out = ['results' => ['id' => '', 'text' => '']];

        //если запрос не пуст
        if (!is_null($q)) {

            //сформировать запрос к базе
            $query = new Query();
            $query->select('id, name, username')->from('user');

            //если кол-во слов в запросе больще единицы
            $query->where(['like','name',$q]);

            $query->where(['like','name', $q])
                ->orWhere(['like','username', $q])
                ->orWhere(['id' => (int)$q])
                ->limit(20);

            //получить данные и сформировать ответ
            $command = $query->createCommand();
            $data = array_values($command->queryAll());
            $tmp = [];

            foreach($data as $index => $arr){
                $tmp[] = ['id' => $arr['id'], 'text' => $arr['name']." ({$arr['id']})"];
            }

            $out['results'] = $tmp;
        }
        //если пуст запрос но указан ID
        elseif ($id > 0) {
            //найти по ID и сформировать ответ
            $user = User::findOne((int)$id);
            if(!empty($user)){
                $out['results'] = ['id' => $id, 'text' => $user->name." ({$user->id})"];
            }
        }

        //вернуть ответ
        return $out;
    }
}