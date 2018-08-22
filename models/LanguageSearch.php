<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class LanguageSearch extends Language
{
    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'self_name', 'prefix', 'is_default'], 'string', 'max' => 255],
            [['id', 'status_id'], 'integer']
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
        $q = parent::find()->orderBy('priority ASC');

        $this->load($params);

        if($this->validate()){

            if(!empty($this->is_default)){
                $q->andWhere(['is_default' => $this->is_default]);
            }

            if(!empty($this->status_id)){
                $q->andWhere(['status_id' => $this->status_id]);
            }

            if(!empty($this->id)){
                $q->andWhere(['id' => $this->id]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','name', $this->name]);
            }

            if(!empty($this->self_name)){
                $q->andWhere(['like','self_name', $this->self_name]);
            }

            if(!empty($this->prefix)){
                $q->andWhere(['like','prefix', $this->prefix]);
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