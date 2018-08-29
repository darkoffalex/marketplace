<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class CvSearch extends Cv
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'group_name', 'group_thematics', 'group_geo', 'email', 'phone', 'timezone', 'comfortable_call_time'], 'string', 'max' => 255],
            [['id', 'is_member', 'user_id', 'country_id', 'group_popularity', 'has_viber', 'has_whatsapp', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        return $baseLabels;
    }

    /**
     * Build search query and return as result data provider
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $q = parent::find()->with(['user']);

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['id' => $this->id]);
            }

            if(!empty($this->user_id)){
                $q->andWhere(['user_id' => $this->user_id]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['status_id' => $this->status_id]);
            }

            if(!empty($this->group_description)){
                $q->andWhere(['like','group_description', $this->group_description]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','name', $this->name]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['status_id' => $this->status_id]);
            }

            if(!empty($this->group_name)){
                $q->andWhere(['like','group_name', $this->group_name]);
            }

            if(!empty($this->group_thematics)){
                $q->andWhere(['like','group_thematics', $this->group_thematics]);
            }

            if(!empty($this->group_geo)){
                $q->andWhere(['like','group_geo', $this->group_geo]);
            }

            if(!empty($this->email)){
                $q->andWhere(['like','email', $this->group_geo]);
            }

            if(!empty($this->phone)){
                $q->andWhere(['like','phone', $this->phone]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('created_at >= :from2 AND created_at <= :to2',['from2' => $date_from, 'to2' => $date_to]);
            }
        }

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
    }
}