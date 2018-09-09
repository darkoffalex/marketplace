<?php

namespace app\models;

use app\helpers\Constants;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;
use Yii;

class PosterSearch extends Poster
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
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['title','description','phone','whats_app','title_approved','description_approved','phone_approved','whats_app_approved'], 'string', 'max' => 255],
            [['id','status_id','user_id','approved_by_ga','approved_by_sa','approved','published','marketplace_tariff_id','refuse_reason','paid_at','common_status','category_id','marketplace_id'], 'integer'],
            [['created_at'], 'date', 'format' => 'dd.MM.yyyy - dd.MM.yyyy']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        $baseLabels['approved'] = Yii::t('app','Approved');
        $baseLabels['common_status'] = Yii::t('app','Publication Status');
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
        $q = parent::find()->where('status_id != :excepted', ['excepted' => Constants::STATUS_TEMPORARY]);

        $this->load($params);

        if(!empty($userId)){
            $q->andWhere(['user_id' => (int)$userId]);
        }

        if($this->validate()){

            if(!empty($this->id)){
                $q->andWhere(['id' => $this->id]);
            }

            if(!empty($this->user_id)){
                $q->andWhere(['user_id' => $this->user_id]);
            }

            if(!empty($this->title)){
                $q->andWhere(['like','title', $this->title]);
            }

            if(!empty($this->title_approved)){
                $q->andWhere(['like','title_approved', $this->title_approved]);
            }

            if(!empty($this->description)){
                $q->andWhere(['like','description', $this->description]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['status_id' => $this->status_id]);
            }

            if(!empty($this->phone)){
                $q->andWhere(['like','phone', $this->phone]);
            }

            if(!empty($this->phone_approved)){
                $q->andWhere(['like','phone_approved', $this->phone_approved]);
            }

            if(!empty($this->whats_app)){
                $q->andWhere(['like','whats_app', $this->whats_app]);
            }

            if(!empty($this->whats_app_approved)){
                $q->andWhere(['like','whats_app_approved', $this->whats_app_approved]);
            }

            if(is_numeric($this->approved_by_ga)){
                $q->andFilterWhere(['approved_by_ga' => (int)$this->approved_by_ga]);
            }

            if(is_numeric($this->approved_by_sa)){
                $q->andFilterWhere(['approved_by_sa' => (int)$this->approved_by_sa]);
            }

            if(is_numeric($this->published)){
                $q->andFilterWhere(['published' => (int)$this->published]);
            }

            if(!empty($this->marketplace_tariff_id)){
                $q->andFilterWhere(['marketplace_tariff_id' => (int)$this->marketplace_tariff_id]);
            }

            if(is_numeric($this->refuse_reason)){
               if(!empty($this->refuse_reason)){
                   $q->andWhere('refuse_reason IS NOT NULL');
               }else{
                   $q->andWhere('refuse_reason IS NULL');
               }
            }

            if(is_numeric($this->approved)){
                if(!empty($this->approved)){
                    $q->andWhere(['approved_by_ga' => (int)true, 'approved_by_sa' => (int)true]);
                }else{
                    $q->andFilterWhere([
                        'or',
                        ['approved_by_ga' => (int)false],
                        ['approved_by_sa' => (int)false]
                    ]);
                }
            }

            if(is_numeric($this->paid_at)){
                if(empty($this->paid_at)){
                    $q->andFilterWhere([
                        'or',
                        '(paid_at + INTERVAL period_seconds SECOND) < NOW()',
                        'paid_at IS NULL'
                    ]);
                }else{
                    $q->andWhere('(paid_at + INTERVAL period_seconds SECOND) > NOW()');
                }
            }

            if(!empty($this->category_id)){
                $q->andWhere(['category_id' => $this->category_id]);
            }

            if(!empty($this->marketplace_id)){
                $q->andWhere(['marketplace_id' => $this->marketplace_id]);
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