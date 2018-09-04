<?php
namespace app\modules\groupAdmin\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\MarketplaceKey;
use app\models\MarketplaceSearch;
use app\models\Rate;
use yii\web\Controller;
use Yii;
use app\models\Marketplace;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use kartik\form\ActiveForm;
use yii\web\UploadedFile;
use app\helpers\FileLoad;
use yii\helpers\Url;
/**
 * Class MessagesController
 * @package app\modules\groupAdmin\controllers
 */
class MarketplacesController extends Controller
{
    /**
     * Сообщение о недоступности опции мониторинга
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MarketplaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Редактирование маркетплейса
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Marketplace $model */
        $model = Marketplace::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

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

            //Загрузка изображение
            $model->header_image = UploadedFile::getInstance($model,'header_image');

            //если все данные в итоге корректны
            if($model->validate()){
                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'header_image','header_image_filename',!empty($model->header_image));

                //базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //Если это редактирование изображения
                if(Yii::$app->request->post('image_editing')){
                    return $this->redirect(Url::to(['/group-admin/marketplaces/update', 'id' => $model->id]).'#picture');
                }
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model'));
    }

    /**
     * Смена статуса из списка (ajax)
     * @param $id
     * @param $status
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRateStatusChange($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model Rate */
        $model = Rate::find()->alias('r')->joinWith(['marketplace mp'])->where(['r.id' => (int)$id, 'mp.user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //установить в зависимости от значения status
        $model->status_id = $status == 'true' ? Constants::STATUS_ENABLED : Constants::STATUS_DISABLED;
        if($model->save()){
            return 'OK';
        }

        return 'FAILED';
    }

    /**
     * Создать ключ
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCreateKey($id)
    {
        /* @var Marketplace $model */
        $model = Marketplace::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $key = new MarketplaceKey();
        $key->marketplace_id = $model->id;

        $key->code = Help::randomString(10);
        while (MarketplaceKey::find()->where(['code' => $key->code])->count() > 0){
            $key->code = Help::randomString(10);
        }

        $key->created_at = date('Y-m-d H:i:s');
        $key->created_by_id = Yii::$app->user->id;
        $key->updated_at = date('Y-m-d H:i:s');
        $key->updated_by_id = Yii::$app->user->id;
        $key->save();

        return $this->redirect(Url::to(['/group-admin/marketplaces/update', 'id' => $model->id]).'#keys');
    }

    /**
     * Удаление ключа
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteKey($id)
    {
        /* @var $model MarketplaceKey */
        $model = MarketplaceKey::find()
            ->alias('mpk')->joinWith(['marketplace mp'])
            ->where(['mpk.id' => $id, 'mp.user_id' => Yii::$app->user->id])
            ->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $mpId = $model->marketplace_id;
        $model->delete();

        return $this->redirect(Url::to(['/group-admin/marketplaces/update', 'id' => $mpId]).'#keys');
    }
}
