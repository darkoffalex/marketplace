<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;

/**
 * This is the model class for table "monitored_group_post".
 */
class MonitoredGroupPost extends \app\models\base\MonitoredGroupPostBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Сформировать поисковой запрос и вернуть DataProvidery
     * @param $params
     * @param null $userId
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null)
    {
        $q = parent::find()->alias('mgp')->joinWith('group as g');

        if(!empty($userId)){
            $q->andWhere(['g.user_id' => $userId]);
        }

        if($this->load($params)){
            if($this->validate()){

                if(!empty($this->id)){
                    $q->andWhere(['mgp.id' => $this->id]);
                }

                if(!empty($this->text)){
                    $q->andWhere(['like','mgp.text', $this->text]);
                }

                if(!empty($this->facebook_id)){
                    $q->andWhere(['like','mgp.facebook_id', $this->facebook_id]);
                }

                if(!empty($this->group_id)){
                    $q->andWhere(['mgp.group_id' => $this->group_id]);
                }

                if(!empty($this->created_at)){
                    $range = explode(' - ',$this->created_at);
                    $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                    $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                    $q->andWhere('mgp.created_at >= :from2 AND mgp.created_at <= :to2',['from2' => $date_from, 'to2' => $date_to]);
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
}
