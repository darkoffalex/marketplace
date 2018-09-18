<?php

namespace app\models;

use app\helpers\Help;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;
use Yii;

class PayoutProposalSearch extends PayoutProposal
{
    /**
     * @var int подтвержден ли
     */
    public $approved;

    /**
     * @var int общий статус объявления
     */
    public $common_status;

    /**
     * @var int базовый тариф
     */
    public $tariff_id;

    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['description'], 'string', 'max' => 255],
            [['id','status_id','user_id','amount'], 'integer'],
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
     * @param null $userId
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null)
    {
        $q = parent::find()->alias('p');

        if(!empty($userId)){
            $q->andWhere(['p.user_id' => $userId]);
        }

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['p.id' => $this->id]);
            }

            if(!empty($this->user_id)){
                $q->andWhere(['p.user_id' => $this->user_id]);
            }

            if(!empty($this->description)){
                $q->andWhere(['like','p.description', $this->description]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['p.status_id' => $this->status_id]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('p.created_at >= :from2 AND p.created_at <= :to2',['from2' => $date_from, 'to2' => $date_to]);
            }

            if(is_numeric($this->amount)){
                $this->amount = Help::toCents($this->amount);
                $error = 1000;
                $q->andWhere('p.amount > :from_amount AND p.amount < :to_amount',['from_amount' => ($this->amount - $error), 'to_amount' => ($this->amount + $error)]);
                $this->amount = Help::toPrice($this->amount);
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