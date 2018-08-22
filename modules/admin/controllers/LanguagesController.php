<?php

namespace app\modules\admin\controllers;

use app\helpers\Sort;
use app\models\Language;
use app\models\LanguageSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер отвечающий за управление языками
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class LanguagesController extends Controller
{
    /**
     * Список все языков
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание нового языка
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new Language();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){

                //если язык выбран как основной по умолчнию - убрать галку "по умолчянию" у всех остальных
                if($model->is_default){
                    Language::updateAll(['is_default' => (int)false]);
                }

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;
                $model->priority = Sort::GetNextPriority(Language::className());

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                //к списку
                $this->redirect(Url::to(['/admin/languages/index', 'id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Изменить язык
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Language $model */
        $model = Language::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){

                //если язык выбран как основной по умолчнию - убрать галку "по умолчянию" у всех кроме текущего
                if($model->is_default){
                    Language::updateAll(['is_default' => (int)false],new Expression('id != :id', ['id' => $model->id]));
                }

                //базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //к списку
                $this->redirect(Url::to(['/admin/languages/index', 'id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Удаление языка
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Language $model */
        $model = Language::findOne((int)$id);

        //если не найден
        if(empty($model) || $model->is_default){
            throw new NotFoundHttpException('Page not found',404);
        }

        //удаление из базы
        $model->delete();

        //к списку
        $this->redirect(Url::to(['/admin/languages/index', 'id' => $model->id]));
    }

    /**
     * Смена очередности
     * @param $id
     * @param $dir
     * @throws NotFoundHttpException
     */
    public function actionMove($id,$dir)
    {
        /* @var Language $model */
        $model = Language::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //переместить
        Sort::Move($model,$dir,Language::class);

        //к списку
        $this->redirect(Url::to(['/admin/languages/index', 'id' => $model->id]));
    }
}