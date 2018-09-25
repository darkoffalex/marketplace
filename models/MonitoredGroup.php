<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use Facebook\Facebook;
use app\models\forms\SettingsForm;
use Carbon\Carbon;

/**
 * This is the model class for table "monitored_group".
 */
class MonitoredGroup extends \app\models\base\MonitoredGroupBase
{
    /**
     * @var array Словари которые относятся к группе
     */
    public $dictionaries_arr = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        foreach ($rules as $index => $originalRule){
            if(($originalRule[0] == 'name' || $originalRule[0] == ['name']) && $originalRule[1] == 'required'){
                $rules[$index]['on'] = 'editing';
            }
        }

        $rules[] = ['dictionaries_arr', 'safe'];
        $rules[] = ['facebook_id', 'required', 'on' => 'editing'];
        $rules[] = ['facebook_id', 'validateFacebookID', 'on' => 'editing'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['dictionaries_arr'] = Yii::t('app','Used dictionaries');
        return $labels;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Сформировать поисковой запрос и вернуть DataProvider
     * @param $params
     * @param null $userId
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null)
    {
        $q = parent::find()->alias('g')->joinWith('monitoredGroupDictionaries as mgd');

        if(!empty($userId)){
            $q->andWhere(['g.user_id' => $userId]);
        }

        if($this->load($params)){
            if($this->validate()){

                if(!empty($this->id)){
                    $q->andWhere(['g.id' => $this->id]);
                }

                if(!empty($this->name)){
                    $q->andWhere(['like','g.name', $this->name]);
                }

                if(!empty($this->facebook_id)){
                    $q->andWhere(['like','g.facebook_id', $this->facebook_id]);
                }

                if(!empty($this->status_id)){
                    $q->andWhere(['g.status_id' => $this->status_id]);
                }

                if(!empty($this->dictionaries_arr)){
                    $q->andWhere(['mgd.dictionary_id' => $this->dictionaries_arr]);
                }
            }
        }

        $q->distinct();

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => !empty($this->per_page) ? (int)$this->per_page : 20,
            ],
        ]);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Проверить доступ к группе
     * @param bool $logErrors
     * @return bool
     */
    public function checkApiAccess($logErrors = true)
    {
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $token = !empty($this->user) ? $this->user->facebook_token : (!empty($currentUser->facebook_token) ? $currentUser->facebook_token : null);

        //Сформировать запрос
        $baseRequestParams =  urldecode(http_build_query([
            'fields' => implode(',',[
                'message',
            ]),
            'limit' => 0,
            'summary' => 'true'
        ]));
        $url = "/".$this->facebook_id."/feed?{$baseRequestParams}";

        //Попытка обратиться к группе через API
        try{
            //Объект для работы с API
            $fb = new Facebook([
                'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
                'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret
            ]);

            //Получить данные
            $fb->get($url, $token);
        }
            //В случае ошибки
        catch (\Exception $exception){
            if($logErrors){
                $this->parsing_errors_log = date('Y-m-d H:i:s', time())." - ".$exception->getMessage();
            }
            return false;
        }

        return true;
    }

    /**
     * Проверить Facebook ID
     * @param $attribute
     * @param $params
     */
    public function validateFacebookID($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->checkApiAccess(false)) {
                $this->addError($attribute, Yii::t('app','Incorrect group ID'));
            }
        }
    }

    /**
     * Получить оптимальную дату начала текущего париснга (если нет - вычислить)
     * @param int $hoursBack
     * @return Carbon
     */
    public function getSyncSinceOptimal($hoursBack = 24*5)
    {
        return !empty($this->sync_since) ? Carbon::parse($this->sync_since) : Carbon::now()->subHours($hoursBack);
    }

    /**
     * Получить оптимальную дату конца текущего париснга (если нет - текущий момент)
     * @return Carbon
     */
    public function getSyncToOptimal()
    {
        return !empty($this->sync_to) ? Carbon::parse($this->sync_to) : Carbon::now();
    }
}
