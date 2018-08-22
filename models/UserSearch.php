<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class UserSearch extends User
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'name'], 'string', 'max' => 255],
            [['status_id', 'role_id', 'id'], 'integer'],
            [['created_at', 'last_online_at'], 'date', 'format' => 'dd.MM.yyyy - dd.MM.yyyy']
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
        $q = User::find();

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['id' => $this->id]);
            }

            if(!empty($this->username)){
                $q->andWhere(['like','username', $this->username]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','name', $this->name]);
            }


            if(!empty($this->role_id)){
                $q->andWhere(['role_id' => $this->role_id]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['status_id' => $this->status_id]);
            }

            if(!empty($this->last_online_at)){
                $range = explode(' - ',$this->last_online_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('last_online_at >= :from1 AND last_online_at <= :to1',['from1' => $date_from, 'to1' => $date_to]);
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