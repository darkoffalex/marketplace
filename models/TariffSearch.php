<?php

namespace app\models;

use app\helpers\Help;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class TariffSearch extends Tariff
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['name','description'],'string'],
            [['id','special_type','show_on_page','period_unit_type','period_amount','base_price','discounted_price','subscription'],'integer'],
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
     * Build search query and return as result data provider
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

            if(is_numeric($this->subscription)){
                $q->andWhere(['subscription' => $this->subscription]);
            }

            if(is_numeric($this->special_type)){
                $q->andWhere(['special_type' => $this->special_type]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','name', $this->name]);
            }

            if(!empty($this->description)){
                $q->andWhere(['like','description', $this->description]);
            }

            if(is_numeric($this->show_on_page)){
                $q->andWhere(['show_on_page' => $this->show_on_page]);
            }

            if(!empty($this->period_unit_type)){
                $q->andWhere(['period_unit_type' => $this->period_unit_type]);
            }

            if(is_numeric($this->base_price)){
                $this->base_price = Help::toCents($this->base_price);
                $error = 1000;
                $q->andWhere('base_price > {from_bp} AND base_price < {to_bp}',['from_bp' => $this->base_price-$error, 'to_bp' => $this->base_price+$error]);
            }

            if(is_numeric($this->discounted_price)){
                $this->discounted_price = Help::toCents($this->discounted_price);
                $error = 1000;
                $q->andWhere('discounted_price > {from_dp} AND discounted_price < {to_dp}',['from_dp' => $this->discounted_price-$error, 'to_dp' => $this->discounted_price+$error]);
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