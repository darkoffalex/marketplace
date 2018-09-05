<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class MarketplaceSearch extends Marketplace
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'group_thematics', 'group_description', 'geo'], 'string', 'max' => 255],
            [['id', 'user_id', 'country_id', 'status_id'], 'integer'],
            [['created_at'], 'date', 'format' => 'dd.MM.yyyy - dd.MM.yyyy']
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
     * Формирование запроса
     * @param $params
     * @param null|int $userId
     * @param null|int $keyOwnerId
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null, $keyOwnerId = null)
    {
        $q = parent::find()->alias('mp')->joinWith(['marketplaceKeys k'])->with(['user']);

        if(empty($keyOwnerId)){
            if(!empty($userId)){
                $q->andWhere(['mp.user_id' => $userId]);
            }
        }else{
            $q->andWhere(['k.used_by_id' => $keyOwnerId]);
        }

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['mp.id' => $this->id]);
            }

            if(!empty($this->user_id)){
                $q->andWhere(['mp.user_id' => $this->user_id]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['mp.status_id' => $this->status_id]);
            }

            if(!empty($this->country_id)){
                $q->andWhere(['mp.country_id' => $this->country_id]);
            }

            if(!empty($this->group_description)){
                $q->andWhere(['like','mp.group_description', $this->group_description]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','mp.name', $this->name]);
            }

            if(!empty($this->group_thematics)){
                $q->andWhere(['like','mp.group_thematics', $this->group_thematics]);
            }

            if(!empty($this->geo)){
                $q->andWhere(['like','mp.geo', $this->geo]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('mp.created_at >= :from2 AND mp.created_at <= :to2',['from2' => $date_from, 'to2' => $date_to]);
            }
        }

        $q->distinct();

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
    }
}