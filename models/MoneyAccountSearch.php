<?php

namespace app\models;

use app\helpers\Help;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class MoneyAccountSearch extends MoneyAccount
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'account_type_id', 'amount'], 'integer'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $q = parent::find();

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['id' => $this->id]);
            }

            if(!empty($this->user_id)){
                $q->andWhere(['user_id' => $this->user_id]);
            }

            if(!empty($this->account_type_id)){
                $q->andWhere(['account_type_id' => $this->account_type_id]);
            }

            if(is_numeric($this->amount)){
                $this->amount = Help::toCents($this->amount);
                $error = 1000;
                $q->andWhere('amount > {from_amount} AND amount < {to_amount}',['from_amount' => ($this->amount - $error), 'to_amount' => ($this->amount + $error)]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('created_at >= :from AND created_at <= :to',['from' => $date_from, 'to' => $date_to]);
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