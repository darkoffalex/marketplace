<?php
namespace app\modules\admin\controllers;

use app\models\Language;
use app\models\MessageSearch;
use app\models\SourceMessage;
use kartik\form\ActiveForm;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LabelsController extends Controller
{
    /**
     * Список переводов
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();

        /* @var $languages Language[] */
        $languages = Language::find()->all();
        if(!empty($languages)){
            foreach ($languages as $language){
                $searchModel->addDynamicField("translated_{$language->prefix}");
            }
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создать
     * @return array|string|Response
     */
    public function actionCreate()
    {
        $model = new SourceMessage();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST
        if(Yii::$app->request->isPost){

            //загрузить в модель
            $model->load(Yii::$app->request->post());

            //если все в порядке
            if($model->validate()){

                //обновить
                $model->save();

                //сохранить переводы
                foreach($model->translations as $language => $phrase){
                    $trl = $model->getTrlFor($language);
                    $trl->translation = $phrase;
                    $trl->save();
                }

                //вернуться назад
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Изменение переводимой надписи
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        /* @var $model SourceMessage */
        $model = SourceMessage::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST
        if(Yii::$app->request->isPost){

            //загрузить в модель
            $model->load(Yii::$app->request->post());

            //если все в порядке
            if($model->validate()){

                //обновить
                $model->update();

                //сохранить переводы
                foreach($model->translations as $language => $phrase){
                    $trl = $model->getTrlFor($language);
                    $trl->translation = $phrase;
                    $trl->save();
                }

                //вернуться назад
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Обновление из списка
     * @param $id
     * @param $lng
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionListUpdate($id,$lng)
    {
        //Отдавать ответ в чистом виде
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model SourceMessage */
        $model = SourceMessage::findOne((int)$id);

        if(empty($model) || empty($lng)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $trl = $model->getTrlFor($lng);
        $trl->translation = Yii::$app->request->post('translation');
        return $trl->save() ? 'OK' : 'FAILED';
    }

    /**
     * Удалить
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Language */
        $model = SourceMessage::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }
}