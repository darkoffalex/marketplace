<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class ShortLinkSearch extends ShortLink
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        $baseRules = parent::rules();
        return $baseRules;
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
        $q = parent::find()->alias('sl');

        if(!empty($userId)) {
            $q->andWhere(['sl.user_id' => $userId]);
        }

        if($this->load($params)){
            if($this->validate()){

                if(!empty($this->id)){
                    $q->andWhere(['sl.id' => $this->id]);
                }

                if(!empty($this->phone)){
                    $q->andWhere(['like','sl.phone',$this->phone]);
                }

                if(!empty($this->original_link)){
                    $q->andWhere(['like','sl.original_link',$this->original_link]);
                }

                if(!empty($this->key)){
                    $q->andWhere(['like','sl.key',$this->key]);
                }

                if(!empty($this->type_id)){
                    $q->andWhere(['sl.type_id' => $this->type_id]);
                }

                if(!empty($this->status_id)){
                    $q->andWhere(['sl.status_id' => $this->status_id]);
                }

                if(!empty($this->clicks)){
                    $q->andWhere('sl.clicks >= :min AND sl.clicks <= :max',['min' => ((int)$this->clicks)-5,'max'=>((int)$this->clicks)+5]);
                }
            }
        }

        if(!empty($this->rating_ordering)){
            $q->orderBy("rating {$this->rating_ordering}");
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