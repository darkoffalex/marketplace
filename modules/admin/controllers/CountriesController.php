<?php

namespace app\modules\admin\controllers;

use app\helpers\Sort;
use app\models\Country;
use app\models\CountrySearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер отвечающий за управление языками
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class CountriesController extends Controller
{
    /**
     * Список все стран
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CountrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание новой страны
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new Country();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;
                $model->priority = Sort::GetNextPriority(Country::class);

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                //к списку
                $this->redirect(Url::to(['/admin/countries/index', 'id' => $model->id]));
            }
        }

        $flags = [];
        $flagsFiles = FileHelper::findFiles(Yii::getAlias('@webroot/common/img/flags/'));
        foreach ($flagsFiles as $index => $filePath){
            $flags[basename($filePath)] = str_replace('.svg','',basename($filePath));
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model','flags'));
    }

    /**
     * Изменить страну
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Country $model */
        $model = Country::findOne((int)$id);

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

                //базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //к списку
                $this->redirect(Url::to(['/admin/countries/index', 'id' => $model->id]));
            }
        }

        $flags = [];
        $flagsFiles = FileHelper::findFiles(Yii::getAlias('@webroot/common/img/flags/'));
        foreach ($flagsFiles as $index => $filePath){
            $flags[basename($filePath)] = str_replace('.svg','',basename($filePath));
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model','flags'));
    }

    /**
     * Удаление страны
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Country $model */
        $model = Country::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //удаление из базы
        $model->delete();

        //к списку
        $this->redirect(Url::to(['/admin/countries/index', 'id' => $model->id]));
    }

    /**
     * Смена очередности
     * @param $id
     * @param $dir
     * @throws NotFoundHttpException
     */
    public function actionMove($id,$dir)
    {
        /* @var Country $model */
        $model = Country::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //переместить
        Sort::Move($model,$dir,Country::class);

        //к списку
        $this->redirect(Url::to(['/admin/countries/index', 'id' => $model->id]));
    }
}