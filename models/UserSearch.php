<?php

namespace app\models;

use app\helpers\Help;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;
use Yii;

class UserSearch extends User
{
    /**
     * Является ли пользователь администратором группы
     * @var null|int
     */
    public $is_group_admin = null;

    /**
     * Является ли пользователь участником
     * @var null|int
     */
    public $is_member = null;

    /**
     * Кол-во учатников во всех группах
     * @var null|int
     */
    public $group_members = null;

    /**
     * Средний доход за день
     * @var null|int
     */
    public $average_agr_day_income = null;

    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['is_member','is_group_admin', 'total_agr_income', 'group_members', 'average_agr_day_income'],'integer'],
            [['username', 'name'], 'string', 'max' => 255],
            [['status_id', 'role_id', 'id'], 'integer'],
            [['created_at', 'last_online_at'], 'date', 'format' => 'dd.MM.yyyy - dd.MM.yyyy'],
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
        $q = User::find()->alias('u')->joinWith(['marketplaces gmp','marketplaceKeys.marketplace ump']);

        $this->load($params);

        if(is_numeric($this->total_agr_income)){
            $this->total_agr_income = Help::toCents($this->total_agr_income);
            $this->average_agr_day_income = Help::toCents($this->average_agr_day_income);
        }

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['u.id' => $this->id]);
            }

            if(!empty($this->username)){
                $q->andWhere(['like','u.username', $this->username]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','u.name', $this->name]);
            }

            if(is_numeric($this->is_group_admin)){
                if(empty($this->is_group_admin)){
                    $q->andWhere('gmp.id IS NULL');
                }else{
                    $q->andWhere('gmp.id IS NOT NULL');
                }
            }

            if(is_numeric($this->is_member)){
                if(empty($this->is_member)){
                    $q->andWhere('ump.id IS NULL');
                }else{
                    $q->andWhere('ump.id IS NOT NULL');
                }
            }

            if(is_numeric($this->total_agr_income)){
                $error = 1000;
                $q->andWhere('u.ag_income_percentage > {from_amount} AND amount < {to_amount}',['from_amount' => ($this->total_agr_income - $error), 'to_amount' => ($this->total_agr_income + $error)]);
                $this->total_agr_income = Help::toPrice($this->total_agr_income);
            }

            if(!empty($this->group_members)){
                $error = 10;
                $q->andWhere('SUM(gmp.group_popularity) > {from_amount} AND SUM(gmp.group_popularity) < {to_amount}',['from_amount' => ($this->group_members - $error), 'to_amount' => ($this->group_members + $error)]);
            }

            if(is_numeric($this->average_agr_day_income)){
                //TODO: какая-то фильтрация
                $this->average_agr_day_income = Help::toPrice($this->average_agr_day_income);
            }

            if(!empty($this->role_id)){
                $q->andWhere(['u.role_id' => $this->role_id]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['u.status_id' => $this->status_id]);
            }

            if(!empty($this->last_online_at)){
                $range = explode(' - ',$this->last_online_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('u.last_online_at >= :from1 AND u.last_online_at <= :to1',['from1' => $date_from, 'to1' => $date_to]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $date_to = Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $q->andWhere('u.created_at >= :from2 AND u.created_at <= :to2',['from2' => $date_from, 'to2' => $date_to]);
            }

        }

        $q->groupBy('u.id');
        //$q->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $dataProvider->sort->attributes['average_agr_day_income'] = [
            'asc' => ['u.total_agr_income / (NOW() - u.created_at)' => SORT_ASC],
            'desc' => ['u.total_agr_income / (NOW() - u.created_at)' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['group_members'] = [
            'asc' => ['SUM(gmp.group_popularity)' => SORT_ASC],
            'desc' => ['SUM(gmp.group_popularity)' => SORT_DESC],
        ];

        return $dataProvider;
    }
}