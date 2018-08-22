<?php
namespace app\models;

use yii\data\ActiveDataProvider;
/**
 * Class MessageSearch
 * @package app\models
 */
class MessageSearch extends SourceMessage
{
    private $_dynamic_fields = [];
    private $_dynamic_fields_data = [];

    /**
     * Правило валидации
     * @return array
     */
    public function rules()
    {
        $baseFields = [
            [['message'], 'string', 'max' => 255],
        ];

        if(!empty($this->_dynamic_fields)){
            foreach ($this->_dynamic_fields as $dynamicField){
                $baseFields[] = [[$dynamicField], 'string', 'max' => 255];
            }
        }

        return $baseFields;
    }

    /**
     * Добавление динамического поля
     * @param $fieldName
     */
    public function addDynamicField($fieldName)
    {
        $this->_dynamic_fields[] = $fieldName;
    }

    /**
     * Переопределить получение аттрибутов
     * @return array
     */
    public function attributes()
    {
        $names = parent::attributes();

        foreach($this->_dynamic_fields as $name){
            $names[] = $name;
        }

        return $names;
    }


    /**
     * Переопределить базовый getter
     * @param string $name
     * @return null
     */
    public function __get($name) {
        if (in_array($name,$this->_dynamic_fields)) {
            if (!empty($this->_dynamic_fields_data[$name])) {
                return $this->_dynamic_fields_data[$name];
            } else {
                return null;
            }
        }
        return parent::__get($name);
    }

    /**
     * Переопределить базоый setter
     * @param string $name
     * @param mixed $val
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $val) {
        if (in_array($name,$this->_dynamic_fields)) {
            $this->_dynamic_fields_data[$name] = $val;
        } else {
            parent::__set($name, $val);
        }
    }

    /**
     * Build search query and return as result data provider
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $q = parent::find()->joinWith('messages as trl');

        $this->load($params);

        if($this->validate()){

            if(!empty($this->_dynamic_fields)){
                foreach ($this->_dynamic_fields as $lngTrlFieldName){
                    if(!empty($this->$lngTrlFieldName)){
                        $language = explode('_',$lngTrlFieldName,2)[1];
                        $val = $this->$lngTrlFieldName;

                        $q->andFilterWhere([
                            'and',
                            ['trl.language' => $language],
                            ['like', 'trl.translation', $val]
                        ]);
                    }
                }
            }

            if(!empty($this->message)){
                $q->andWhere(['like','message', $this->message]);
            }

            $q->distinct();
        }

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
    }
}