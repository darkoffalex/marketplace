<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use app\helpers\Help;
use Carbon\Carbon;
use Yii;

class MoneyTransactionSearch extends MoneyTransaction
{
    /**
     * ID пользователя
     * @var null|int
     */
    public $user_id = null;

    /**
     * ID счета
     * @var null|int
     */
    public $account_id = null;

    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'from_account_id', 'to_account_id', 'amount', 'status_id', 'type_id', 'web_payment_type_id', 'user_id', 'account_id'], 'integer'],
            [['note', 'description'], 'string'],
            [['created_at'], 'date', 'format' => 'dd.MM.yyyy - dd.MM.yyyy']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        $baseLabels['user_id'] = Yii::t('app','Owner user');
        $baseLabels['account_type_id'] = Yii::t('app','Account type');
        $baseLabels['account_id'] = Yii::t('app','Account');
        return $baseLabels;
    }

    /**
     * Формирование запроса
     * @param $params
     * @param null $accountId
     * @return ActiveDataProvider
     */
    public function search($params, $accountId = null)
    {
        $q = parent::find()->alias('mt')->joinWith(['fromAccount fa', 'toAccount ta']);

        if(!empty($accountId)){
            $q->andWhere(['or', ['mt.from_account_id' => $accountId], ['mt.to_account_id' => $accountId]]);
        }

        $this->load($params);

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['mt.id' => $this->id]);
            }

            if(!empty($this->account_id)){
                $q->andFilterWhere(['or',['mt.from_account_id' => $this->account_id], ['mt.to_account_id' => $this->account_id]]);
            }

            if(!empty($this->user_id)){
                $q->andFilterWhere(['or',['fa.user_id' => $this->user_id], ['ta.user_id' => $this->user_id]]);
            }

            if(!empty($this->from_account_id)){
                $q->andWhere(['mt.from_account_id' => $this->from_account_id]);
            }

            if(!empty($this->to_account_id)){
                $q->andWhere(['mt.to_account_id' => $this->to_account_id]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['mt.status_id' => $this->status_id]);
            }

            if(!empty($this->type_id)){
                $q->andWhere(['mt.type_id' => $this->type_id]);
            }

            if(!empty($this->note)){
                $q->andWhere(['like','mt.note', $this->note]);
            }

            if(!empty($this->description)){
                $q->andWhere(['like','mt.description', $this->description]);
            }

            if(!empty($this->web_payment_type_id)){
                $q->andWhere(['mt.web_payment_type_id' => $this->web_payment_type_id]);
            }

            if(is_numeric($this->amount)){
                $this->amount = Help::toCents($this->amount);
                $error = 1000;
                $q->andWhere('mt.amount > {from_amount} AND mt.amount < {to_amount}',['from_amount' => ($this->amount - $error), 'to_amount' => ($this->amount + $error)]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('mt.created_at >= :from AND mt.created_at <= :to',['from' => $date_from, 'to' => $date_to]);
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