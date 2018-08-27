<?php

namespace app\modules\admin\controllers;

use app\helpers\Sort;
use app\models\Category;
use app\models\CategorySearch;
use kartik\form\ActiveForm;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CategoriesController extends Controller
{
    /**
     * Показатьс писок категорий
     * @param int $root
     * @return string
     */
    public function actionIndex($root = 0)
    {
        $root = Yii::$app->request->post('expandRowKey',$root);

        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$root);

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_index', compact('searchModel','dataProvider','root'));
        }

        return $this->render('index', compact('searchModel','dataProvider','root'));
    }

    /**
     * Перемещает категорию
     * @param int $id
     * @param string $dir
     * @return \yii\web\Response|string
     * @throws NotFoundHttpException
     */
    public function actionMove($id, $dir)
    {
        /* @var $model Category */
        $model = Category::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('admin','Category not found'),404);
        }

        Sort::Move($model,$dir,Category::class,['parent_category_id' => $model->parent_category_id]);

        if(Yii::$app->request->isAjax){
            return $this->actionIndex($model->parent_category_id);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Увделение
     * @param $id
     * @return \yii\web\Response|string
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Category */
        $model = Category::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('admin','Category not found'),404);
        }

        $parentId = $model->parent_category_id;
        $model->recursiveDelete();

        if(Yii::$app->request->isAjax){
            return $this->actionIndex($parentId);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Создание категории
     * @return array|string|Response
     */
    public function actionCreate()
    {
        $model = new Category();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if(Yii::$app->request->isPost){

            $model->load(Yii::$app->request->post());;

            $model->created_at = date('Y-m-d H:i:s',time());
            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
            $model->updated_by_id = Yii::$app->user->id;
            $model->priority = Sort::GetNextPriority(Category::class,['parent_category_id' => $model->parent_category_id]);

            if($model->validate()){
                $model->save();
                return $this->redirect(Url::to(['/admin/categories/index', 'id' => $model->id]));
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Редактирование категории
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        /* @var $model Category */
        $model = Category::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('admin','Category not found'),404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if(Yii::$app->request->isPost){

            $oldParentCat = $model->parent_category_id;

            $model->load(Yii::$app->request->post());;
            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->updated_by_id = Yii::$app->user->id;

            if($model->validate()){
                if($oldParentCat != $model->parent_category_id){
                    $model->priority = Sort::GetNextPriority(Category::class,['parent_category_id' => $model->parent_category_id]);
                }
                $model->update();
                return $this->redirect(Url::to(['/admin/categories/index', 'id' => $model->id]));
            }
        }

        $model->refresh();

        return $this->renderAjax('_edit',compact('model'));
    }
}